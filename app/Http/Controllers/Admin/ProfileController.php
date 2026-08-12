<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the admin's profile / settings.
     */
    public function index()
    {
        $admin = $this->getAdmin();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Update the admin's account details.
     */
    public function update(Request $request)
    {
        $admin = $this->getAdmin();

        $validated = $request->validate([
            'full_name'         => 'required|string|max:255',
            'email'             => ['required', 'email', Rule::unique('users', 'email')->ignore($admin->id)],
            'phone_number'      => ['required', 'string', Rule::unique('users', 'phone_number')->ignore($admin->id)],
            'preferred_language'=> 'nullable|string|max:10',
        ]);

        $admin->update($validated);

        AdminAuditService::log(
            'profile.updated',
            "Updated own admin profile",
            $admin,
            null,
            ['full_name' => $admin->full_name, 'email' => $admin->email],
            $request
        );

        return back()->with('status', 'Your profile has been updated successfully.');
    }

    /**
     * Update the admin's password.
     */
    public function updatePassword(Request $request)
    {
        $admin = $this->getAdmin();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password you entered is incorrect.',
            ]);
        }

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        AdminAuditService::log(
            'profile.password_changed',
            "Changed own admin password",
            $admin,
            null,
            ['password_updated' => true],
            $request
        );

        return back()->with('status', 'Your password has been updated successfully.');
    }

    private function getAdmin(): User
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin instanceof User) {
            abort(403, 'Not an admin account.');
        }
        return $admin;
    }
}
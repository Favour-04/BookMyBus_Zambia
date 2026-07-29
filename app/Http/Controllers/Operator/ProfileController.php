<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the operator's profile.
     */
    public function index()
    {
        // $operator = Auth::guard('operator')->user();
        $operator = $this->getOperator();

        return view('operator-profile', [
            'operator' => $operator,
        ]);
    }
    private function getOperator(){
        return Auth::guard('operator')->user() ?? Operator::find(session('operator_id'));
    }

    /**
     * Update the operator's business details.
     */
    public function update(Request $request)
    {
        // $operator = Auth::guard('operator')->user();
        $operator = $this->getOperator();

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email'        => ['required', 'email', Rule::unique('operators', 'email')->ignore($operator->id)],
            'phone_number' => ['required', 'string', Rule::unique('operators', 'phone_number')->ignore($operator->id)],
            'tpin'         => 'nullable|string|max:50',
            'address'      => 'nullable|string|max:255',
        ]);

        $operator->update($validated);

        return back()->with('status', 'Your business profile has been updated successfully.');
    }

    /**
     * Update the operator's password.
     */
    public function updatePassword(Request $request)
    {
        // $operator = Auth::guard('operator')->user();
        $operator = $this->getOperator();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $operator->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password you entered is incorrect.',
            ]);
        }

        $operator->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Your password has been updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ─── Traveler Registration ────────────────────────────────────────

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name'    => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'phone_number' => 'required|string|unique:users,phone_number',
            'password'     => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create(array_merge($data, ['role' => 'traveler']));
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful.',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    // ─── Traveler Login ───────────────────────────────────────────────

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($data)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    // ─── Operator Registration ────────────────────────────────────────

    public function registerOperator(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'email'        => 'required|email|unique:operators,email',
            'phone_number' => 'required|string',
            'password'     => ['required', 'confirmed', Password::min(8)],
            'tpin'         => 'nullable|string',
            'address'      => 'nullable|string',
        ]);

        $operator = Operator::create($data);

        return response()->json([
            'message'  => 'Operator registration submitted. Awaiting admin verification.',
            'operator' => $operator,
        ], 201);
    }

    // ─── Operator Login ───────────────────────────────────────────────

    public function loginOperator(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $operator = Operator::where('email', $data['email'])->first();

        if (!$operator || !Hash::check($data['password'], $operator->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if (!$operator->is_verified) {
            return response()->json(['message' => 'Your account is pending admin verification.'], 403);
        }

        $token = $operator->createToken('operator_token')->plainTextToken;

        return response()->json([
            'message'  => 'Login successful.',
            'operator' => $operator,
            'token'    => $token,
        ]);
    }

    // ─── Logout ───────────────────────────────────────────────────────

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    // ─── Get Authenticated User ───────────────────────────────────────

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}

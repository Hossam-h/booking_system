<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\API\RegisterRequest;
use App\Http\Requests\API\LoginRequest;

class AuthController extends BaseController
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        if (!isset($data['role'])) {
            $data['role'] = 'customer';
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return $this->sendResponse([
            'user' => $user,
            'token' => $token,
        ], 'Registered successfully');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return $this->sendError('Invalid credentials');
        }

        // Optionally, delete previous tokens or keep multiple sessions
        $token = $user->createToken('api_token')->plainTextToken;

        return $this->sendResponse([
            'user' => $user,
            'token' => $token,
        ], 'Logged in successfully');
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return $this->sendResponse(null, 'Logged out successfully');
    }

    public function me(Request $request)
    {
        return $this->sendResponse($request->user(), 'Current user');
    }
}

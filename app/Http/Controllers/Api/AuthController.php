<?php

namespace App\Http\Controllers\Api;

use App\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Models\User;
use App\Permissions\Abilities;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    use ApiResponses;

    public function login(LoginUserRequest $request): JsonResponse
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            $this->error('Invalid credentials', ['Email or Password not provided'], 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->success(
            [
                'user' => $user,
                'token' => $user->createToken('Api token for ' . $user->email,
                    Abilities::getAbilities($user),
                    now()->addMonth())
                    ->plainTextToken,
            ],
            'Authenticated',
            200
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success([], 'Logged out successfully', 200);
    }

    public function register(): JsonResponse
    {
        return $this->success(
            [
                'user' => Auth::user(),
                //'token' => Auth::user()->createToken('auth_token')->plainTextToken,
            ],
            'Registration successful',
            200
        );
    }
}

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
            $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
            'Authenticated',
            [
                'user' => $user,
                'token' => $user->createToken('Api token for ' . $user->email,
                    Abilities::getAbilities($user),
                    now()->addMonth())
                    ->plainTextToken,
            ]
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('Logged out successfully');
    }

    public function register(): JsonResponse
    {
        return $this->ok(
            'Registration successful',
            [
                'user' => Auth::user(),
                //'token' => Auth::user()->createToken('auth_token')->plainTextToken,
            ]
        );
    }
}

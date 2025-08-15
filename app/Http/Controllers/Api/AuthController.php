<?php

namespace App\Http\Controllers\Api;

use App\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\ApiLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(LoginUserRequest $request): JsonResponse
    {
        $request->validated($request->all());

        if(!Auth::attempt($request->only('email', 'password'))) {
            $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
            'Authenticated',
            [
                'user' => $user,
                'token' => $user->createToken('Api token for ' . $user->email)->plainTextToken,
            ]
        );
    }

    public function register() : JsonResponse
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

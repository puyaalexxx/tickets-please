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

    /**
     * Login
     *
     * Authenticate a user and return an API token.
     *
     * @response 200 {
     *      "success": true,
     *      "status": 200,
     *      "data":
     *          { "user data" },
     *          "token": "{Your_Auth_Token}",
     *      },
     *      "message": "Authenticated"
     * }
     *
     * @group Authentication
     * @unauthenticated
     */
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

    /**
     * Logout
     *
     * Logout the authenticated user by revoking their API token.
     *
     * @response 200 {
     *      "success": true,
     *      "status": 200,
     *      "data": [],
     *      "message": "Logged out successfully"
     * }
     *
     * @group Authentication
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success([], 'Logged out successfully', 200);
    }

    /**
     * Register
     *
     * Register a new user.
     *
     * @group Tickets
     *
     * @return JsonResponse
     */
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

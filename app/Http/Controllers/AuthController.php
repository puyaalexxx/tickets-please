<?php

namespace App\Http\Controllers;

use App\ApiResponses;
use App\Http\Requests\ApiLoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(ApiLoginRequest $request): JsonResponse
    {
        return $this->ok('Login successful');
    }

    public function register() : JsonResponse
    {
        return $this->ok('Registration successful');
    }
}

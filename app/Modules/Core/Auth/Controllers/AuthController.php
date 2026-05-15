<?php

namespace App\Modules\Core\Auth\Controllers;

use App\Modules\Core\Auth\Requests\LoginRequest;
use App\Modules\Core\Auth\Requests\RegisterRequest;
use App\Modules\Core\Auth\Resources\AuthUserResource;
use App\Modules\Core\Auth\Services\AuthService;
use App\Shared\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'user' => new AuthUserResource($user),
            'token' => $token,
        ], 'User registered', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        [$user, $token] = $this->authService->attemptLogin($request->validated());

        return $this->success([
            'user' => new AuthUserResource($user),
            'token' => $token,
        ], 'Login successful');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(new AuthUserResource($request->user()), 'Authenticated user');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->success(null, 'Logged out');
    }
}

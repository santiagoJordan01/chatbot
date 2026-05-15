<?php

namespace App\Modules\Core\Auth\Services;

use App\Models\User;
use App\Shared\Exceptions\ApiException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $payload): User
    {
        return User::query()->create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => Hash::make($payload['password']),
            'business_id' => $payload['business_id'] ?? null,
        ]);
    }

    public function attemptLogin(array $credentials): array
    {
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new ApiException('Invalid credentials', 401);
        }

        $token = $user->createToken($credentials['device_name'] ?? 'api-token')->plainTextToken;

        return [$user, $token];
    }
}

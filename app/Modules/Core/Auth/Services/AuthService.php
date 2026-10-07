<?php

namespace App\Modules\Core\Auth\Services;

use App\Models\User;
use App\Modules\CRM\Models\Business;
use App\Shared\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function register(array $payload): User
    {
        return DB::transaction(function () use ($payload) {
            $user = User::query()->create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'password' => Hash::make($payload['password']),
                'business_id' => $payload['business_id'] ?? null,
            ]);

            if ($user->business_id !== null) {
                return $user;
            }

            $base = Str::slug($payload['name']) ?: 'clinica';
            $business = Business::query()->create([
                'name' => $payload['name'],
                'slug' => $base.'-'.$user->id,
                'business_type' => 'veterinary',
                'timezone' => 'America/Bogota',
            ]);
            $user->forceFill(['business_id' => $business->id])->save();

            return $user->fresh();
        });
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

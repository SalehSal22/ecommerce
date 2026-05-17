<?php

namespace App\Services\Auth;

use App\Models\Admin;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class AdminAuthService
{
    public function login(array $data): array
    {
        $email = mb_strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');

        $admin = Admin::where('email', $email)->first();
        if (!$admin || !Hash::check($password, (string) $admin->password)) {
            throw new DomainException('Invalid credentials.');
        }

        try {
            $token = $this->guard()->login($admin);
        } catch (JWTException $e) {
            throw new DomainException('Unable to generate admin token.');
        }

        return [
            'admin' => $admin,
            'access_token' => $token,
        ];
    }

    public function logout(): void
    {
        try {
            $this->guard()->logout();
        } catch (JWTException $e) {
            // Token might already be invalidated.
        }
    }

    private function guard(): JWTGuard
    {
        /** @var JWTGuard $guard */
        $guard = Auth::guard('admin');

        return $guard;
    }
}

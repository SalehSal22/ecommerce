<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Services\Auth\AdminAuthService;
use DomainException;
use Throwable;

class AuthController extends Controller
{
    public function __construct(protected AdminAuthService $service) {}

    public function login(AdminLoginRequest $request)
    {
        try {
            $result = $this->service->login($request->validated());
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to login.',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'admin' => [
                    'id' => $result['admin']->id,
                    'name' => $result['admin']->name,
                    'email' => $result['admin']->email,
                ],
                'access_token' => $result['access_token'],
            ],
        ]);
    }

    public function logout()
    {
        try {
            $this->service->logout();
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to logout.',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }
}

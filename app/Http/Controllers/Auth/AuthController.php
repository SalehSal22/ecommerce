<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\Auth\UserResource;
use App\Services\Auth\AuthService;
use DomainException;
use Illuminate\Http\Request;
use Throwable;

class AuthController extends Controller
{
    public function __construct(protected AuthService $service) {}

    public function register(RegisterRequest $request)
    {
        try {
            $result = $this->service->startRegistration($request->validated());
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 409);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to start registration',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
            'data' => [
                'email' => $result['email'],
                'otp_expires_at' => $result['otp_expires_at'],
            ],
        ], 201);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $result = $this->service->verifyRegistrationOtp(
                $request->validated('email'),
                $request->validated('otp')
            );
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to verify OTP',
            ], 500);
        }

        return (new UserResource($result))->response()->setStatusCode(200);
    }

    public function refresh(Request $request)
    {
        $request->validate(['refresh_token' => 'required|string']);

        try {
            $result = $this->service->refreshAccessToken($request->string('refresh_token')->toString());
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to refresh access token',
            ], 500);
        }

        return (new UserResource($result))->response()->setStatusCode(200);
    }

    public function login(LoginRequest $request)
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
                'message' => 'Unable to login',
            ], 500);
        }

        return (new UserResource($result))->response()->setStatusCode(200);
    }

    public function logout(LogoutRequest $request)
    {
        try {
            $this->service->logout(
                $request->validated('refresh_token'),
                $request->bearerToken(),
                (bool) $request->boolean('force')
            );
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to logout',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'data' => $request->user(),
        ]);
    }
}

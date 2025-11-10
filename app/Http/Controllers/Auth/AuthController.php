<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Request OTP for administrator authentication
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'method' => 'required|in:EMAIL,SMS',
            'language' => 'nullable|string|size:2|in:FR,EN',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        try {
            $result = $this->authService->requestOtp(
                $request->input('identifier'),
                $request->input('method'),
                $request->input('language', 'FR')
            );

            return response()->json($result, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again later.',
            ], 500);
        }
    }

    /**
     * Verify OTP and authenticate administrator
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'code' => 'required|string|size:6',
            'method' => 'required|in:EMAIL,SMS',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        try {
            $result = $this->authService->verifyOtp(
                $request->input('identifier'),
                $request->input('code'),
                $request->input('method')
            );

            return response()->json([
                'success' => true,
                'message' => 'Authentication successful',
                'user' => $result['administrator'],
                'access_token' => $result['token'],
                'authToken' => $result['token'],
                'token_type' => 'Bearer',
                'expires_at' => $result['expires_at'],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Logout the authenticated administrator
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
            ], 500);
        }
    }

    /**
     * Get current authenticated administrator session
     */
    public function session(Request $request): JsonResponse
    {
        try {
            $user = $request->user()->load('roles.permissions');

            return response()->json([
                'success' => true,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve session',
            ], 500);
        }
    }
}

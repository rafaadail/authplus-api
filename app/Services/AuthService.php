<?php

namespace App\Services;

use Illuminate\Support\Facades;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(){}

    public function login(array $data)
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password']
        ];

        if(! $accessToken = auth('api')->attempt($credentials)) {
            throw new \Exception('Invalid credentials');
        }
        
        return $this->buildTokenResponse($accessToken, auth('api')->user());
    }

    public function me()
    {
        return auth()->user();
    }

    public function refresh($token)
    {
        try {

            if (! $token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refresh token required'
                ], 400);
            }

            $payload = auth()->setToken($token)->getPayload();

            if ($payload->get('type') !== 'refresh') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token type'
                ], 401);
            }

            $user = auth('api')->setToken($token)->authenticate();

            $newAccessToken = auth('api')->login($user);

            return $this->buildTokenResponse($newAccessToken, $user);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired refresh token'
            ], 401);
        }
    }

    private function buildTokenResponse(string $accessToken, $user): array
    {
        $refreshToken = auth('api')
            ->setTTL(60 * 24 * 7)
            ->claims(['type' => 'refresh'])
            ->fromUser($user);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
    }

}
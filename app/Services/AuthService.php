<?php

namespace App\Services;

use Illuminate\Support\Facades;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\RefreshTokenRequiredException;

class AuthService
{
    public function __construct(private LoggerService $logger){}

    public function login(array $data)
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password']
        ];

        $accessToken = auth('api')->attempt($credentials);

        if(! $accessToken) {

            $this->logger->logError('auth.login.error', 'Invalid credentials', [
                'endpoint' => '/auth/login'
            ]);

            throw new InvalidCredentialsException('Invalid credentials');
        }

        $this->logger->logInfo('auth.login.success', 'User logged in successfully', [
            'endpoint' => '/auth/login',
            'user_email' => $data['email']
        ]);
        

        /** @var string $accessToken */

        return $this->buildTokenResponse($accessToken, auth('api')->user());
    }

    public function me()
    {
        $user = auth()->user();

        if (!$user) {

            $this->logger->logError('auth.me.error', 'User not authenticated', [
                'endpoint' => '/auth/me'
            ]);

            throw new \Exception('User not authenticated');
        }

        $this->logger->logInfo('auth.me.success', 'User retrieved successfully', [
            'endpoint' => '/auth/me',
            'user_email' => $user->email
        ]);

        return $user;
    }

    public function refresh($token)
    {
        if (! $token) {
            
            $this->logger->logError('auth.refresh.error', 'Refresh token required', [
                'endpoint' => '/auth/refresh'
            ]);

            throw new RefreshTokenRequiredException('Refresh token required');
        }

        $payload = auth()->setToken($token)->getPayload();

        if ($payload->get('type') !== 'refresh') {
            $this->logger->logError('auth.refresh.error', 'Invalid token type', [
                'endpoint' => '/auth/refresh'
            ]);

            throw new \Exception('Invalid token type');
        }

        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('api');

        /** @var \Tymon\JWTAuth\Contracts\JWTSubject $user */
        $user = $guard->setToken($token)->authenticate();
       
        $newAccessToken = $guard->login($user);

        if(!$newAccessToken) {
            throw new InvalidCredentialsException('Invalid credentials');
        }

        /** @var string $newAccessToken */
        return $this->buildTokenResponse($newAccessToken, $user);
    }

    /**
     * @return array{
     *     access_token: string,
     *     refresh_token: string,
     *     token_type: string,
     *     expires_in: int
     * }
     */
    private function buildTokenResponse(string $accessToken, $user): array
    {
        try {
            /** @var \Tymon\JWTAuth\JWTGuard $guard */
            $guard = auth('api');

            $guard->setTTL(60 * 24 * 7);

            $refreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user);

            return [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'bearer',
                'expires_in' => $guard->factory()->getTTL() * 60
            ];
        
        } catch(\Exception $e) {
            $this->logger->logError('auth.token.error', 'Failed to generate tokens', [
                'endpoint' => '/auth/login',
                'user_email' => $user->email
            ]);
            throw new \Exception($e->getMessage());
        }
    }

    public function logout()
    {
        try {
            auth('api')->logout();

            $this->logger->logInfo('auth.logout.success', 'User logged out successfully', [
                
                'endpoint' => '/auth/logout',
                'user_email' => auth('api')->user()->email ?? 'unknown'
            ]);

        } catch (\Exception $e) {

            $this->logger->logError('auth.logout.error', 'Failed to logout user', [
                'endpoint' => '/auth/logout'
            ]);

            throw new \Exception('Failed to logout user');
        }
    }
}
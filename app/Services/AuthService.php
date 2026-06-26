<?php

namespace App\Services;

use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\InvalidTokenTypeException;
use App\Exceptions\RefreshTokenRequiredException;
use App\Exceptions\UserNotAuthenticatedException;
use App\Models\User;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWTGuard;

class AuthService
{
    public function __construct(private LoggerService $logger) {}

    public function login(array $data): array
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        $accessToken = auth('api')->attempt($credentials);

        if (! $accessToken) {
            throw new InvalidCredentialsException('Invalid credentials');
        }

        $this->logger->logInfo('auth.login.success', 'User logged in successfully', [
            'endpoint' => '/auth/login',
            'user_email' => $data['email'],
        ]);

        /** @var string $accessToken */

        return $this->buildTokenResponse($accessToken, auth('api')->user());
    }

    public function me(): User
    {
        $user = auth()->user();

        $this->logger->logInfo('auth.me.success', 'User retrieved successfully', [
            'endpoint' => '/auth/me',
            'user_email' => $user->email,
        ]);

        return $user;
    }

    public function refresh($token)
    {
        if (!$token) {
            throw new RefreshTokenRequiredException('Refresh token required');
        }
        
        $payload = auth()->setToken($token)->getPayload();

        if ($payload->get('type') !== 'refresh') {
            $this->logger->logError(
                'auth.refresh.error',
                'Invalid token type',
                '/auth/refresh',
                ['token_type' => $payload->get('type')]
            );
            throw new InvalidTokenTypeException('Invalid token type');
        }

        /** @var JWTGuard $guard */
        $guard = auth('api');

        /** @var JWTSubject $user */
        $user = $guard->setToken($token)->authenticate();

        $newAccessToken = $guard->login($user);

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
    private function buildTokenResponse(string $accessToken, JWTSubject $user): array
    {

        /** @var JWTGuard $guard */
        $guard = auth('api');

        $guard->setTTL(60 * 24 * 7);

        $refreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
        ];
    }

    public function logout(): void
    {
        auth('api')->logout();

        $this->logger->logInfo(
            'auth.logout.success',
            'User logged out successfully',
            [
                'endpoint' => '/auth/logout',
                'user_email' => auth('api')->user()->email ?? 'unknown',
            ]
        );
    }
}

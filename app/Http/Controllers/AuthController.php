<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

use App\Http\Requests\LoginRequest;
use OpenApi\Attributes as OA;
use App\Helpers\ApiResponse;
use App\Swagger\Schemas\ErrorResponseSchema;
use App\Swagger\Schemas\SuccessResponseSchema;
use App\Swagger\Schemas\LoginResponseSchema;
use App\Swagger\Schemas\UserResponseSchema;

class AuthController extends Controller
{
    public function __construct(private AuthService $service) { }

    #[OA\Post(
        path: '/api/auth/login',
        summary: 'Login user and get access token',
        tags: ['Auth'],
        description: 'Authenticate user with email and password, returning an access token on success.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful login',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'success',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Login successful.'
                        ),
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/LoginResponse'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ErrorResponse'
                )
            )
        ]
    )]
    public function login(LoginRequest $request)
    {
        $user = $this->service->login($request->validated());

        return ApiResponse::success($user, 'Login successful.');
    }

    #[OA\Get(
        path: '/api/auth/me',
        summary: 'Get authenticated user information',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        description: 'Retrieve information about the currently authenticated user using the provided access token.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful retrieval of user information',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'success',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Login successful.'
                        ),
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/UserResponse'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ErrorResponse'
                )
            )
        ]
    )]
    public function me()
    {
        $user = $this->service->me();
        return ApiResponse::success($user, 'User information retrieved successfully.');
    }

    #[OA\Post(
        path: '/api/auth/refresh',
        summary: 'Refresh access token',
        tags: ['Auth'],
        description: 'Refresh the access token using the current valid token, returning a new access token on success.',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful token refresh',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'success',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Login successful.'
                        ),
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/LoginResponse'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ErrorResponse'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ErrorResponse'
                )
            )
        ]      
    )]
    public function refresh(Request $request)
    {
        $refresh = $this->service->refresh($request->bearerToken());

        return ApiResponse::success($refresh, 'Token refreshed successfully.');
    }

    #[OA\Post(
        path: '/api/auth/logout',
        summary: 'Logout user and invalidate token',
        description: 'Logout the currently authenticated user, invalidating the access token to prevent further use.',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful logout',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/SuccessResponse'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ErrorResponse'
                )
            )
        ]  
    )]
    public function logout(Request $request)
    {
        $this->service->logout();

        return ApiResponse::success([], 'Success logged out.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

use App\Http\Requests\LoginRequest;
use OpenApi\Attributes as OA;

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
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'data', type: 'object')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            )
        ]
    )]
    public function login(LoginRequest $request)
    {
        try {

            $user = $this->service->login($request->validated());

            return response()->json([
                'success' => true,
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        }
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
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'data', type: 'object')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            )
        ]
    )]
    public function me()
    {
        try {
            $user = $this->service->me();

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        }
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
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'data', type: 'object')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            )
        ]      
    )]
    public function refresh(Request $request)
    {
        try {
            $refresh = $this->service->refresh($request->bearerToken());

            return response()->json([
                'success' => true,
                'data' => $refresh
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        }
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
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            )
        ]  
    )]
    public function logout(Request $request)
    {
        try {
            $this->service->logout();

            return response()->json([
                'sucess' => true,
                'message' => 'Success logged out'
            ]);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}

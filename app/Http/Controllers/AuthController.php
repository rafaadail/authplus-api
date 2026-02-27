<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function __construct(private AuthService $service) { }

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

    public function me()
    {
        try {
            $user = $this->service->me();
            
            return response()->json([
                'success' => false, 
                'data' => $user
            ]);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function refresh(Request $request)
    {
        try {
            $refresh = $this->service->refresh($request->bearerToken());

            return response()->json([
                'success' => true,
                'data' => $refresh
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }
}

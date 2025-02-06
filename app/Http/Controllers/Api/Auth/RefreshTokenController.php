<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth; // JWTAuth for handling token-based authentication.

class RefreshTokenController extends Controller
{
    // Use the ApiResponseTrait for consistent API responses
    use ApiResponseTrait;

    /**
     * Refresh Token
     * @OA\Post (
     *     path="/auth/refresh-token",
     *     tags={"Authentication"},
     *     summary="Refresh access and refresh tokens",
     *     description="Generate a new access token and refresh token for the authenticated user using the existing valid refresh token.",
     *     security={{"BearerToken":{}}},    
     * @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         description="Bearer token",
     *         @OA\Schema(
     *             type="string",
     *             example="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Token refreshed successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "success": true,
     *                  "status_code": 200,
     *                  "message": "Token Refreshed Successfully",
     *                  "data": {
     *                      "user": {
     *                          "id": 1467,
     *                          "name": "Md.Rabiul Hasan",
     *                          "location_id": 1,
     *                          "role": "admin",
     *                          "status": 1,
     *                          "created_at": "2025-01-14T09:44:29.000000Z",
     *                          "updated_at": "2025-01-14T09:44:29.000000Z"
     *                      },
     *                      "access_token": {
     *                          "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *                          "token_type": "bearer",
     *                          "expires_in": 1800
     *                      },
     *                      "refresh_token": {
     *                          "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *                          "token_type": "bearer",
     *                          "expires_in": 86400
     *                      }
     *                  }
     *              }
     *          )
     *     ),
     * )
     */
    public function refresh()
    {
        $user = $this->guard()->user();
        // Generate a JWT token for the user (Access token with 15-minute expiration)
        $token = auth()->setTTL(intval(env('JWT_TTL')))->login($user); // Set TTL to 15 minutes for access token
    
        // Generate the refresh token (Refresh token with 1-day expiration)
        $refreshToken = auth()->setTTL(intval(env('JWT_REFRESH_TTL')))->refresh(); // Set TTL to 1440 minutes (1 day) for refresh token


        // Prepare the response data
        $data = [
            'user' => $user,
            'access_token' => $this->respondWithToken($token), // Access token details
            'refresh_token' => $this->respondWithRefreshToken($refreshToken), // Refresh token details
        ];
        // Return success response with refreshed token
        return $this->successApiResponse(200, "Token Refreshed Successfully", $data);
    }

        /**
     * Get the authentication guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard(); // Use the default authentication guard
    }
}

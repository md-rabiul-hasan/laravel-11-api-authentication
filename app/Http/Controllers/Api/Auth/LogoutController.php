<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth; // JWTAuth for handling token-based authentication.

class LogoutController extends Controller
{
    // Use the ApiResponseTrait for consistent API responses
    use ApiResponseTrait;

    /**
     * Logout
     * @OA\Post (
     *     path="/auth/logout",
     *     tags={"Authentication"},
     *     summary="Log out the authenticated user",
     *     description="Ends the session for the authenticated user by invalidating their token.",
     *     security={{"BearerToken":{}}},
     *     @OA\Parameter(
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
     *          description="Logout successful",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "success": true,
     *                  "status_code": 200,
     *                  "message": "Successfully logged out",
     *                  "data": {}
     *              }
     *          )
     *     )
     * )
     */
    public function logout()
    {
        try {
            JWTAuth::getToken(); // Ensures token is already loaded.
            JWTAuth::invalidate(true);
            // Invalidate the current JWT token
            auth()->logout(); // This invalidates the current token
            return $this->successApiResponse(200, "Successfully logged out", []);
        } catch (Exception $e) {
            // Handle unexpected errors
            return $this->errorApiResponse(500, $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;

class MeController extends Controller
{
    // Use the ApiResponseTrait for consistent API responses
    use ApiResponseTrait;

    /**
     * User Information
     * @OA\Get (
     *     path="/auth/me",
     *     tags={"Authentication"},
     *     summary="Get authenticated user information",
     *     description="Retrieve information of the authenticated user by passing a valid bearer token in the Authorization header.",
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
     *          description="User information retrieved successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "success": true,
     *                  "status_code": 200,
     *                  "message": "User information retrieved successfully",
     *                  "data": {
     *                      "user": {
     *                          "id": 1467,
     *                          "name": "Md.Rabiul Hasan",
     *                          "email": "test@example.com",
     *                          "email_verified_at": "2025-02-06T04:20:40.000000Z",
     *                          "created_at": "2025-01-14T09:44:29.000000Z",
     *                          "updated_at": "2025-01-14T09:44:29.000000Z"
     *                      }
     *                  }
     *              }
     *          )
     *     ),
     * )
     */
    public function me()
    {
        $data = [
            "user" => $this->guard()->user()
        ];
        // Return the authenticated user's information
        return $this->successApiResponse(200, "User information retrived successfully", $data);
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

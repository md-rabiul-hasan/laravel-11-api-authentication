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

class LoginController extends Controller
{
    // Use the ApiResponseTrait for consistent API responses
    use ApiResponseTrait;

    /**
     * Login
     * @OA\Post (
     *     path="/auth/login",
     *     tags={"Authentication"},
     *     summary="Login with Email and Password",
     *     description="This login API allows a customer to log in using their email and password.",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="email",
     *                          type="string",
     *                          format="email"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string",
     *                          format="password"
     *                      )
     *                 ),
     *                 example={
     *                     "email": "test@example.com",
     *                     "password": "password"
     *                }
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Successfully Login",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "success": true,
     *                  "status_code": 200,
     *                  "message": "Login Successfully",
     *                  "data": {
     *                      "user": {
     *                          "id": 1,
     *                           "name": "Test User",
     *                           "email": "test@example.com",
     *                           "email_verified_at": "2025-02-06T04:20:40.000000Z",
     *                          "created_at": "2025-02-06T04:20:41.000000Z",
     *                          "updated_at": "2025-02-06T04:20:41.000000Z"
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
     *     @OA\Response(
     *          response=401,
     *          description="Invalid Email or Password",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                   "success": false,
     *                   "status_code": 401,
     *                   "message": "Invalid email or password."
     *              }
     *          )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "success": false,
     *                  "status_code": 400,
     *                  "message": "Please enter email and password"
     *              }
     *          )
     *     )
     * )
     */
    public function login(Request $request)
    {
        // Validate the request data
        $rules = [
            'email' => 'required|email', // Email is required and must be a valid email
            'password' => 'required|string', // Password is required and must be a string
        ];

        $messages = [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
        ];

        try {
            // Validate the request data
            $request->validate($rules, $messages);

            // Retrieve email and password from the request
            $credentials = $request->only('email', 'password');

            // Attempt to authenticate the user
            if (!$token = auth()->attempt($credentials)) {
                return $this->errorApiResponse(401, 'Invalid email or password.');
            }

            // Retrieve the authenticated user
            $user = auth()->user();

            // Generate the access token (Access token with 15-minute expiration)
            $accessToken = auth()->setTTL(intval(env('JWT_TTL')))->login($user); // Set TTL to 15 minutes for access token

            // Generate the refresh token (Refresh token with 1-day expiration)
            $refreshToken = auth()->setTTL(intval(env('JWT_REFRESH_TTL')))->refresh(); // Set TTL to 1440 minutes (1 day) for refresh token

            // Prepare the response data
            $data = [
                'user' => $user,
                'access_token' => $this->respondWithToken($accessToken), // Access token details
                'refresh_token' => $this->respondWithRefreshToken($refreshToken), // Refresh token details
            ];

            // Return success response with user data, access token, and refresh token
            return $this->successApiResponse(200, "Login Successful", $data);

        } catch (ValidationException $e) {
            // Handle validation errors
            return $this->errorApiResponse(400, $e->validator->errors()->first());
        } catch (Exception $e) {
            // Handle any unexpected errors
            return $this->errorApiResponse(500, "An unexpected error occurred: " . $e->getMessage());
        }
    }

      /**
     * Handle login requests for without email password.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginWithDiffrentParamter(Request $request)
    {
        // Validate the request data
        $rules = [
            'employee_id' => 'required|integer', // employee_id is required and must be an integer
        ];
    
        $messages = [
            'employee_id.required' => 'Please enter employee ID.',
            'employee_id.integer' => 'The employee ID must be an integer.',
        ];
    
        try {
            // Validate the request data
            $request->validate($rules, $messages);
    
            // Retrieve the employee_id from the request
            $employee_id = $request->input('employee_id');
    
            // Find the user by employee_id
            $user = User::select(['id', 'name', 'employee_id'])
                        ->where('employee_id', $employee_id)
                        ->first();
    
            // Check if the user exists
            if (!$user) {
                return $this->errorApiResponse(404, 'User not found.');
            }
    
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
    
            // Return success response with user data, access token, and refresh token
            return $this->successApiResponse(200, "Login Successfully", $data);
    
        } catch (ValidationException $e) {
            // Handle validation errors
            return $this->errorApiResponse(422, $e->validator->errors()->first());
        } catch (Exception $e) {
            // Handle any unexpected errors
            return $this->errorApiResponse(500, "An unexpected error occurred: " . $e->getMessage());
        }
    }
}

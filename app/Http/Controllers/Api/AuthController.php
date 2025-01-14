<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth; // JWTAuth for handling token-based authentication.

class AuthController extends Controller
{
    // Use the ApiResponseTrait for consistent API responses
    use ApiResponseTrait;

    /**
     * Handle login requests for employees.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
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

    /**
     * Generate a token response structure.
     *
     * @param  string $token
     * @return array
     */
    protected function respondWithToken($token)
    {
        return [
            'token' => $token,     // The JWT access token
            'token_type'   => 'bearer',   // Token type
            'expires_in'   => intval(env('JWT_TTL')) * 60,        // Expiry time in seconds for access token
        ];
    }

    /**
     * Generate a token response structure.
     *
     * @param  string $token
     * @return array
     */
    protected function respondWithRefreshToken($token)
    {
        return [
            'token' => $token,     // The JWT access token
            'token_type'   => 'bearer',   // Token type
            'expires_in'   => intval(env('JWT_REFRESH_TTL')) * 60,        // Expiry time in seconds for access token
        ];
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

    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
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

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
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
}

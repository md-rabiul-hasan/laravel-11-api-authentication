<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

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
        // Define validation rules and custom error messages
        $rules = [
            'employee_id' => 'required|integer', // employee_id is required and must be an integer
        ];

        $messages = [
            'employee_id.required' => 'Please enter employee ID.', // Custom message for missing employee_id
            'employee_id.integer' => 'The employee ID must be an integer.', // Custom message for invalid employee_id format
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
                return $this->errorApiResponse(404, 'User not found.'); // User not found response
            }

            // Generate a JWT token for the user
            $token = auth()->login($user);

            // Prepare response data
            $data = [
                "user"  => $user, // User details
                "token" => $this->respondWithToken($token), // JWT token details
            ];

            // Return success response with user data and token
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
            'access_token' => $token, // The JWT access token
            'token_type'   => 'bearer', // Token type
            'expires_in'   => $this->guard()->factory()->getTTL() * 1, // Token expiration time in seconds
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
        // Return the authenticated user's information
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            $this->guard()->logout(); // Invalidate the user's token
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
        // Prepare refreshed token data
        $data = [
            "user"  => $this->guard()->user(), // Authenticated user details
            "token" => $this->respondWithToken($this->guard()->refresh()), // New token details
        ];

        // Return success response with refreshed token
        return $this->successApiResponse(200, "Token Refresh Successfully", $data);
    }
}

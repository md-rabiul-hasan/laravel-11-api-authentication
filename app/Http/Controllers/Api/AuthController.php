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
            'employee_id' => 'required|integer' // Ensure employee_id is required and must be an integer
        ];

        $messages = [
            'employee_id.required' => 'Please enter employee ID.', // Custom error message for missing employee_id
            'employee_id.integer' => 'The employee ID must be an integer.' // Custom error message for non-integer employee_id
        ];

        try {
            // Validate the request data using the Request object
            $request->validate($rules, $messages);

            // Retrieve the employee_id from the request
            $employee_id = $request->input('employee_id');

            // Find the user by employee_id
            $user = User::select(['id', 'name', 'employee_id'])
                        ->where('employee_id', $employee_id)
                        ->first();

            // Check if the user exists
            if (!$user) {
                // Return a 404 response if the user is not found
                return $this->errorApiResponse(404, 'User not found.');
            }

            // Generate a token for the authenticated user
            $token = auth()->login($user);

            // Prepare the response data
            $data = [
                "user"  => $user, // Return the user's information
                "token" => $this->respondWithToken($token) // Include the JWT token
            ];

            // Return a successful response
            return $this->successApiResponse(200, "Login Successfully", $data);

        } catch (ValidationException $e) {
            // Handle validation errors and return a 422 response
            return $this->errorApiResponse(422, $e->validator->errors()->first());
        } catch (Exception $e) {
            // Handle unexpected errors and return a 500 response
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
        // Return the token structure including expiry time
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $this->guard()->factory()->getTTL() * 60
        ];
    }

    /**
     * Get the authentication guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard(); // Default authentication guard
    }
}

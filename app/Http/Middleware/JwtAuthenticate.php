<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait; // Import trait for API response handling.
use Closure; // Import Closure class for middleware.
use Exception; // Import base Exception class for error handling.
use Tymon\JWTAuth\Facades\JWTAuth; // JWTAuth for handling token-based authentication.
use Tymon\JWTAuth\Exceptions\TokenExpiredException; // Exception for expired tokens.
use Tymon\JWTAuth\Exceptions\TokenInvalidException; // Exception for invalid tokens.
use Tymon\JWTAuth\Exceptions\JWTException; // General JWT exception.

class JwtAuthenticate
{
    use ApiResponseTrait; // Trait providing helper methods for API responses.

    /**
     * Handle an incoming request.
     *
     * This middleware intercepts HTTP requests to check and validate JWT tokens,
     * ensuring only authenticated users can proceed.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming HTTP request.
     * @param  \Closure  $next  The next middleware or request handler.
     * @return mixed  The response, or the next middleware.
     */
    public function handle($request, Closure $next)
    {
        try {
            // Parse the JWT token from the request and authenticate the user.
            $user = JWTAuth::parseToken()->authenticate();

            // Check if the authenticated user exists.
            if (!$user) {
                // If user is not found, return a 404 (Not Found) error response.
                return $this->errorApiResponse(404, 'User not found');
            }
        } catch (TokenExpiredException $e) {
            // Handle case where the token has expired, return a 401 (Unauthorized) error.
            return $this->errorApiResponse(401, 'Token has expired');
        } catch (TokenInvalidException $e) {
            // Handle case where the token is invalid, return a 401 (Unauthorized) error.
            return $this->errorApiResponse(401, 'Token is invalid');
        } catch (JWTException $e) {
            // Handle case where the token is missing, return a 401 (Unauthorized) error.
            return $this->errorApiResponse(401, 'Token is missing');
        }

        // If the token is valid and the user is authenticated, proceed to the next request handler.
        return $next($request);
    }
}

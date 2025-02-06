<?php

namespace App\Traits;


trait ApiResponseTrait
{
    
    /**
     * The function `errorApiResponse` generates a JSON response with error details including status code,
     * message, and success status.
     * 
     * @param httpStatusCode The `` parameter represents the HTTP status code that will be
     * returned in the response. It indicates the status of the API request, such as 200 for success, 404
     * for not found, 500 for server error, etc.
     * @param message The `message` parameter in the `errorApiResponse` function is a string that
     * represents the error message or description that you want to include in the JSON response. It is the
     * message that will be returned to the client to provide information about the error that occurred.
     * 
     * @return The function `errorApiResponse` returns a JSON response with the following structure:
     * ```
     * {
     *     "success": false,
     *     "status_code":  or ,
     *     "message": 
     * }
     * ```
     * The HTTP status code of the response is set to ``.
     */
    public function errorApiResponse($httpStatusCode, $message)
    {
        return response()->json(
            [
                "success"     => false,
                "status_code" => $httpStatusCode,
                "message"     => $message,
            ],
            $httpStatusCode
        );
    }

    /**
     * The function `successApiResponse` generates a JSON response with success status, message, data,
     * and optional third party API status code.
     * 
     * @param httpStatusCode The `` parameter represents the HTTP status code that will
     * be returned in the response. It indicates the status of the API request, such as 200 for
     * success, 404 for not found, 500 for server error, etc.
     * @param message The `message` parameter in the `successApiResponse` function is a string that
     * represents a message or description related to the API response. It is typically used to provide
     * information or feedback to the client about the outcome of the API request.
     * @param data The `data` parameter in the `successApiResponse` function is an optional parameter
     * that allows you to pass additional data to be included in the JSON response. This data can be an
     * array, object, or any other type of data that you want to send back to the client along with the
     * success response
     * 
     * @return The function `successApiResponse` returns a JSON response with the following structure:
     * - "success": true
     * - "status_code": The value of `` if it is not null, otherwise the value
     * of ``
     * - "message": The value of the `` parameter
     * - "data": An array containing the data passed as the `` parameter
     */
    public function successApiResponse($httpStatusCode, $message, $data=[]){
        return response()->json([
            "success"     => true,
            "status_code" => $httpStatusCode,
            "message"     => $message,
            "data"        => $data
        ], $httpStatusCode);
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
}

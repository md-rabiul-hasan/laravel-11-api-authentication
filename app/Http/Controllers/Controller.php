<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Laravel 11 REST API - JWT Authentication",
 *      description="Comprehensive documentation for the authentication system, providing details on all available endpoints, request parameters, and response structures for secure user authentication.",
 *      @OA\Contact(
 *          name="API Support",
 *          url="https://yourdomain.com",
 *          email="support@yourdomain.com"
 *      )
 * )
 * 
 * @OA\Server(
 *      url="http://localhost:8000/api/v1",
 *      description="Local Development Server for testing and debugging"
 * )
 * 
 * @OA\Tag(
 *      name="Authentication",
 *      description="Endpoints for user authentication, including login, logout, profile details, and token management."
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="BearerToken",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your JWT token in the format: Bearer {token}"
 * )
 */
abstract class Controller
{
    //
}

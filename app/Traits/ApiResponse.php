<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Build a success response
     */
    protected function successResponse($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Build an error response
     */
    protected function errorResponse(string $message = 'Error', int $code = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Response for resource created
     */
    protected function createdResponse($data = null, string $message = 'Resource created successfully')
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Response for resource not found
     */
    protected function notFoundResponse(string $message = 'Resource not found')
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Response for unauthorized action
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized action')
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Response for unauthenticated user
     */
    protected function unauthenticatedResponse(string $message = 'Unauthenticated')
    {
        return $this->errorResponse($message, 401);
    }
}

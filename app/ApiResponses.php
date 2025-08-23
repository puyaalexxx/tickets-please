<?php
declare(strict_types=1);


namespace App;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait ApiResponses
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Handle a successful response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function success(mixed $data, string $message, int $code = ResponseAlias::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => true,
            'status' => $code,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * Handle an error response.
     *
     * @param string $errorMessage
     * @param array $errors
     * @param int $code
     * @return JsonResponse
     */
    protected function error(string $errorMessage, array $errors = [], int $code = ResponseAlias::HTTP_BAD_REQUEST): JsonResponse
    {
        $response = [
            'success' => false,
            'status' => $code,
            'message' => $errorMessage,
            'errors' => $errors,
        ];

        return response()->json($response, $code);
    }
}

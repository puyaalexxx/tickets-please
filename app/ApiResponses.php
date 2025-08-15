<?php
declare(strict_types=1);


namespace App;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function success($message, $status = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    protected function ok($message): JsonResponse
    {
        return $this->success($message, 200);
    }
}

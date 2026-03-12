<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

trait ErrorLogging
{
    protected function logError(Request $request, Throwable $e, string $requestId): void
    {
        $context = [
            'request_id' => $requestId,
            'request' => [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => $request->user()?->id,
                'headers' => $request->headers->all(),
                'payload' => $this->sanitizePayload($request->all()),
            ],
            'exception' => [
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ],
            'server' => [
                'memory_usage' => memory_get_peak_usage(true),
                'execution_time' => microtime(true) - LARAVEL_START,
            ],
        ];

        Log::error('Server Error', $context);
    }

    private function sanitizePayload(array $payload): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'credit_card', 'token'];

        return collect($payload)->map(function ($value, $key) use ($sensitiveKeys) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                return '********';
            }

            return $value;
        })->all();
    }
}

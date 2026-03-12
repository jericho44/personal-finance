<?php

namespace App\Helpers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * @OA\Schema(
 *   schema="responseFormatter",
 *   title="Standard API Response",
 *   description="Standard response format for all API endpoints",
 *   type="object",
 *   required={"meta"},
 *
 *   @OA\Property(
 *     property="meta",
 *     type="object",
 *     required={"code", "status", "message"},
 *     @OA\Property(property="code", type="integer", example=200, description="HTTP status code"),
 *     @OA\Property(property="status", type="string", example="success", enum={"success", "error"}, description="Response status"),
 *     @OA\Property(property="message", type="string", example="Operation successful", nullable=true, description="Response message")
 *   ),
 *   @OA\Property(
 *     property="data",
 *     type="object",
 *     nullable=true,
 *     description="Response payload"
 *   )
 * )
 *
 * @OA\Response(
 *   response="globalResponse",
 *   description="Standard API Response",
 *
 *   @OA\JsonContent(ref="#/components/schemas/responseFormatter")
 * )
 */
class ResponseFormatter
{
    protected static $response = [
        'meta' => [
            'code' => 200,
            'status' => 'success',
            'message' => null,
        ],
        'data' => null,
    ];

    /**
     * Success response formatter
     *
     * @param  mixed  $data
     * @param  string|null  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = null, $message = null, $code = 200)
    {
        self::$response = [
            'meta' => [
                'code' => $code,
                'status' => 'success',
                'message' => $message ?: 'Success',
            ],
            'data' => $data,
        ];

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    /**
     * Error response formatter with advanced error handling
     *
     * @param  int|object  $code
     * @param  string|null  $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($code, $message = null)
    {
        $requestId = Str::uuid()->toString();

        // Reset response to ensure clean state
        self::$response = [
            'meta' => [
                'code' => is_numeric($code) ? $code : 500,
                'status' => 'error',
                'message' => $message ?: 'Error',
                'request_id' => $requestId,
            ],
            'data' => null,
        ];

        // Handle different error types
        if (is_object($error = $code)) {
            switch (get_class($error)) {
                case AuthenticationException::class:
                case UnauthorizedHttpException::class:
                    self::$response['meta']['code'] = 401;
                    self::$response['meta']['message'] = $error->getMessage() ?: 'Akses ditolak! Anda harus login terlebih dahulu untuk mengakses halaman ini.';
                    break;

                case AuthorizationException::class:
                    self::$response['meta']['code'] = 403;
                    self::$response['meta']['message'] = $error->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.';
                    break;

                case NotFoundHttpException::class:
                case ModelNotFoundException::class:
                    self::$response['meta']['code'] = 404;
                    self::$response['meta']['message'] = $error->getMessage() ?: 'Halaman yang Anda cari tidak ditemukan.';
                    break;

                case ValidationException::class:
                case Validator::class:
                    self::$response['meta']['code'] = 422;
                    self::$response['meta']['message'] = 'Silahkan isi form dengan benar terlebih dahulu.';

                    // Stack errors
                    $bags = $error->errors();

                    if ($error instanceof Validator) {
                        $bags = $bags->messages();
                    }

                    // Format errors
                    $data = [];
                    foreach ($bags as $field => $values) {
                        foreach ($values as $value) {
                            $data[] = [
                                'name' => $field,
                                'message' => $value,
                            ];
                        }
                    }

                    self::$response['data']['errors'] = $data;
                    break;

                case ThrottleRequestsException::class:
                    self::logServerError($requestId, $error);

                    self::$response['meta']['code'] = 429;
                    self::$response['meta']['message'] = $error->getMessage();
                    break;

                case HttpException::class:
                    // Http Error caused by user defined
                    // example:
                    // abort(400, 'Some Error Message')

                    self::$response['meta']['code'] = $error->getStatusCode();
                    self::$response['meta']['message'] = $error->getMessage();
                    break;

                default:
                    // Exception|Throwable
                    // Always log 5xx errors first
                    self::logServerError($requestId, $error);

                    if (config('app.debug')) {
                        // .env value APP_DEBUG = true
                        self::$response['meta']['message'] = $error->getMessage();
                        self::$response['data'] = [
                            'exception' => get_class($error),
                            'message' => $error->getMessage(),
                            'file' => $error->getFile(),
                            'line' => $error->getLine(),
                        ];
                    } else {
                        // .env value APP_DEBUG = false
                        self::$response['meta']['message'] = 'An error occurred while processing your request';
                    }
                    break;
            }

            // For critical error
            // code...
        }

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    /**
     * Log server errors for debugging
     *
     * @param  mixed  $error
     */
    public static function logServerError(string $requestId, $error)
    {
        $logContext = [
            'requestId' => $requestId,
            'timestamp' => now()->toIso8601String(),
        ];

        // Add additional error details for logging
        if ($error instanceof \Exception || $error instanceof \Throwable) {
            $logContext['exception'] = [
                'class' => get_class($error),
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'trace' => $error->getTraceAsString(),
            ];
        } elseif (is_array($error)) {
            $logContext['error_details'] = $error;
        }

        // Use Laravel's logging with error level
        Log::error('Server Error', $logContext);
    }
}

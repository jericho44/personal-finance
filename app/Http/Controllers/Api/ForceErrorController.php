<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ForceErrorController extends Controller
{
    /**
     * @OA\Get(
     *   tags={"Api|Testing"},
     *   path="/api/test-error-500",
     *   summary="Test 500 Internal Server Error",
     *   description="Endpoint to test 500 Internal Server Error response",
     *
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="code", type="integer", example=500),
     *         @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="An internal server error occurred while processing your request.")
     *       ),
     *       @OA\Property(property="data", type="object", nullable=true),
     *       @OA\Property(property="trace_id", type="string", format="uuid")
     *     )
     *   )
     * )
     */
    public function testError500()
    {
        try {
            throw new \Exception('Internal Server Error: This is a custom error for testing purposes.');
            abort(500, 'Internal Server Error: This is a custom error for testing purposes.');
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Api|Testing"},
     *   path="/api/test-error-502",
     *   summary="Test 502 Bad Gateway Error",
     *   description="Endpoint to test 502 Bad Gateway Error response",
     *
     *   @OA\Response(
     *     response=502,
     *     description="Bad Gateway",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="code", type="integer", example=502),
     *         @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="A bad gateway error occurred while processing your request.")
     *       ),
     *       @OA\Property(property="data", type="object", nullable=true),
     *       @OA\Property(property="trace_id", type="string", format="uuid")
     *     )
     *   )
     * )
     */
    public function testError502()
    {
        try {
            abort(502, 'Bad Gateway: This is a custom error for testing purposes.');
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Api|Testing"},
     *   path="/api/test-error-503",
     *   summary="Test 503 Service Unavailable Error",
     *   description="Endpoint to test 503 Service Unavailable Error response",
     *
     *   @OA\Response(
     *     response=503,
     *     description="Service Unavailable",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="code", type="integer", example=503),
     *         @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="The service is currently unavailable while processing your request.")
     *       ),
     *       @OA\Property(property="data", type="object", nullable=true),
     *       @OA\Property(property="trace_id", type="string", format="uuid")
     *     )
     *   )
     * )
     */
    public function testError503()
    {
        try {
            abort(503, 'Service Unavailable: This is a custom error for testing purposes.');
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Api|Testing"},
     *   path="/api/test-error-504",
     *   summary="Test 504 Gateway Timeout Error",
     *   description="Endpoint to test 504 Gateway Timeout Error response",
     *
     *   @OA\Response(
     *     response=504,
     *     description="Gateway Timeout",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="code", type="integer", example=504),
     *         @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="A gateway timeout error occurred while processing your request.")
     *       ),
     *       @OA\Property(property="data", type="object", nullable=true),
     *       @OA\Property(property="trace_id", type="string", format="uuid")
     *     )
     *   )
     * )
     */
    public function testError504()
    {
        try {
            abort(504, 'Gateway Timeout: This is a custom error for testing purposes.');
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Api|Testing"},
     *   path="/api/test-error-400",
     *   summary="Test 400 Gateway Timeout Error",
     *   description="Endpoint to test 400 Gateway Timeout Error response",
     *
     *   @OA\Response(
     *     response=400,
     *     description="Gateway Timeout",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="code", type="integer", example=400),
     *         @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="Username already exists. Please try another one!")
     *       ),
     *       @OA\Property(property="data", type="object", nullable=true),
     *       @OA\Property(property="trace_id", type="string", format="uuid")
     *     )
     *   )
     * )
     */
    public function testError400()
    {
        try {
            // abort(400, 'Username already exists. Please try another one!');

            // Equivalent to
            return ResponseFormatter::error(400, 'Username already exists. Please try another one!');
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Api|Testing"},
     *   path="/api/test-error-401",
     *   summary="Test 401 Gateway Timeout Error",
     *   description="Endpoint to test 401 Gateway Timeout Error response",
     *
     *   @OA\Response(
     *     response=401,
     *     description="Gateway Timeout",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="code", type="integer", example=401),
     *         @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="Unauthenticanted")
     *       ),
     *       @OA\Property(property="data", type="object", nullable=true),
     *       @OA\Property(property="trace_id", type="string", format="uuid")
     *     )
     *   )
     * )
     */
    public function testError401()
    {
        try {
            abort(401, 'Unauthenticanted');

            // Equivalent to
            // return ResponseFormatter::error(401, 'Unauthenticanted');
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Api|Testing"},
     *   path="/api/test-error-403",
     *   summary="Test 403 Gateway Timeout Error",
     *   description="Endpoint to test 403 Gateway Timeout Error response",
     *
     *   @OA\Response(
     *     response=403,
     *     description="Gateway Timeout",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="code", type="integer", example=403),
     *         @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="Unauthorized")
     *       ),
     *       @OA\Property(property="data", type="object", nullable=true),
     *       @OA\Property(property="trace_id", type="string", format="uuid")
     *     )
     *   )
     * )
     */
    public function testError403()
    {
        try {
            abort(403, 'Unauthorized');

            // Equivalent to
            // return ResponseFormatter::error(403, 'Unauthorized');
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Api|Testing"},
     *   path="/api/test-error-404",
     *   summary="Test 404 Gateway Timeout Error",
     *   description="Endpoint to test 404 Gateway Timeout Error response",
     *
     *   @OA\Response(
     *     response=404,
     *     description="Gateway Timeout",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="code", type="integer", example=404),
     *         @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="Not Found")
     *       ),
     *       @OA\Property(property="data", type="object", nullable=true),
     *       @OA\Property(property="trace_id", type="string", format="uuid")
     *     )
     *   )
     * )
     */
    public function testError404()
    {
        try {
            abort(404, 'Not Found');

            // Equivalent to
            // return ResponseFormatter::error(404, 'Not Found');
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Api|Testing"},
     *   path="/api/test-error-422",
     *   summary="Test 422 Gateway Timeout Error",
     *   description="Endpoint to test 422 Gateway Timeout Error response",
     *
     *   @OA\Response(
     *     response=422,
     *     description="Gateway Timeout",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="code", type="integer", example=422),
     *         @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="A gateway timeout error occurred while processing your request.")
     *       ),
     *       @OA\Property(property="data", type="object", nullable=true),
     *       @OA\Property(property="trace_id", type="string", format="uuid")
     *     )
     *   )
     * )
     */
    public function testError422(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'username' => 'required|max:1|in:admin,user',
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator);
            }

            // Equivalent to
            $request->validate([
                'name' => 'required|max:255',
                'username' => 'required|max:1|in:admin,user',
            ]);

            throw ValidationException::withMessages([
                'nama' => ['Message 1', 'Message 2'],
                'username' => ['Message 1', 'Message 2'],
            ]);

            // Retrieve the validated input...
            $validated = $validator->validated();
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Api|Testing"},
     *   path="/api/test-error-429",
     *   summary="Test 429 Gateway Timeout Error",
     *   description="Endpoint to test 429 Gateway Timeout Error response",
     *
     *   @OA\Response(
     *     response=429,
     *     description="Gateway Timeout",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="code", type="integer", example=429),
     *         @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="Too many attempts.")
     *       ),
     *       @OA\Property(property="data", type="object", nullable=true),
     *       @OA\Property(property="trace_id", type="string", format="uuid")
     *     )
     *   )
     * )
     */
    public function testError429(Request $request)
    {
        try {
            return ResponseFormatter::success(null, 'Catch me on 10 hit/minutes if u can');
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Api|Testing"},
     *   path="/api/test-swagger-auto-generate",
     *   summary="Test Swagger Auto Generate",
     *   description="Endpoint to Test Swagger Auto Generate response",
     *
     *   @OA\Response(
     *     response=429,
     *     description="Gateway Timeout",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="code", type="integer", example=429),
     *         @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="Too many attempts.")
     *       ),
     *       @OA\Property(property="data", type="object", nullable=true),
     *       @OA\Property(property="trace_id", type="string", format="uuid")
     *     )
     *   )
     * )
     */
    public function testSwaggerAutoGenerate(Request $request)
    {
        try {
            return ResponseFormatter::success(null, 'Catch me on 10 hit/minutes if u can');
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Api|Testing"},
     *   path="/api/test-enum-list",
     *   summary="Test Enum List",
     *   description="Endpoint to Test Enum List response",
     *
     *   @OA\Response(
     *     response=429,
     *     description="Gateway Timeout",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="code", type="integer", example=429),
     *         @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="Too many attempts.")
     *       ),
     *       @OA\Property(property="data", type="object", nullable=true),
     *       @OA\Property(property="trace_id", type="string", format="uuid")
     *     )
     *   )
     * )
     */
    public function testEnumList(Request $request)
    {
        try {
            return ResponseFormatter::success(\App\Enums\ExampleStatusEnum::getList($request));
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception);
        }
    }
}

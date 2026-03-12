<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppVersionResource;
use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HelperController extends Controller
{
    public function __construct() {}

    /**
     * @OA\Get(
     *   tags={"Api|Helper"},
     *   path="/api/helper/latest-version/{type}",
     *   summary="Helper latest version",
     *
     *   @OA\Parameter(
     *     name="type",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string", enum={"web", "android", "ios"})
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function latestVersion(Request $request)
    {
        $appVersion = AppVersion::query()
            ->where('app_type', $request->type)
            ->latest('created_at')
            ->first();

        if (! $appVersion) {
            return ResponseFormatter::error(404, __('models.app_version.not_found'));
        }

        return ResponseFormatter::success(new AppVersionResource($appVersion), __('responses.helper.latest_version_success'));
    }

    /**
     * @OA\Get(
     *   tags={"Api|Helper"},
     *   path="/api/helper/storage/{path}",
     *   summary="Helper storage",
     *
     *   @OA\Parameter(
     *     name="path",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\Parameter(
     *     name="action",
     *     in="path",
     *
     *     @OA\Schema(type="string", enum={"preview", "download"})
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function storage(Request $request, $path)
    {
        if (! $path || ! Storage::exists($path)) {
            return ResponseFormatter::error(404, __('responses.file_not_found'));
        }

        $file = Storage::path($path);

        if (strtolower($request->action) === 'download') {
            return response()->download($file);
        }

        return response()->file($file);
    }
}

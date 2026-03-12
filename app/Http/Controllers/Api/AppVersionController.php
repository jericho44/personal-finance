<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppVersionResource;
use App\Interfaces\AppVersionInterface;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    public function __construct(
        private AppVersionInterface $appVersionRepository
    ) {}

    /**
     * @OA\Get(
     *   tags={"Api|AppVersion"},
     *   path="/api/app-version",
     *   summary="AppVersion index",
     *
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\Parameter(
     *     name="limit",
     *     in="query",
     *
     *     @OA\Schema(type="integer")
     *   ),
     *
     *   @OA\Parameter(
     *     name="isForceUpdate",
     *     in="query",
     *
     *     @OA\Schema(type="integer")
     *   ),
     *
     *   @OA\Parameter(
     *     name="appType",
     *     in="query",
     *
     *     @OA\Schema(type="integer")
     *   ),
     *
     *   @OA\Parameter(
     *     name="sortBy",
     *     in="query",
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\Parameter(
     *     name="orderBy",
     *     in="query",
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\Parameter(
     *     name="page",
     *     in="query",
     *
     *     @OA\Schema(type="integer")
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'nullable|numeric',
            'limit' => 'nullable|numeric|min:0|max:100',
        ]);

        $appVersions = $this->appVersionRepository->getAll(
            select: [],
            withRelations: [],
            search: $request->search,
            filter: [
                'isForceUpdate' => $request->is_force_update,
                'appType' => $request->app_type,
                'startDate' => $request->start_date,
                'endDate' => $request->end_date,
            ],
            sortOption: [
                'orderCol' => $request->sort_by,
                'orderDir' => $request->order_by,
            ],
            paginateOption: [
                'method' => 'paginate',
                'length' => $request->limit,
                'page' => $request->page,
            ],
            reformat: function ($model) {
                return tap($model, function ($paginate) {
                    return $paginate->getCollection()
                        ->transform(function ($row) {
                            return new AppVersionResource($row);
                        });
                });
            }
        );

        return ResponseFormatter::success($appVersions, 'Data berhasil ditampilkan');
    }

    /**
     * @OA\Get(
     *   tags={"Api|AppVersion"},
     *   path="/api/app-version/{id}",
     *   summary="AppVersion show",
     *
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function show(Request $request, $id)
    {
        $appVersion = $this->appVersionRepository->findByIdHash($id);

        if ($appVersion) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }

        return ResponseFormatter::success(new AppVersionResource($appVersion), 'Data berhasil ditampilkan');
    }

    /**
     * @OA\Post(
     *   tags={"Api|AppVersion"},
     *   path="/api/app-version",
     *   summary="AppVersion store",
     *
     *   @OA\RequestBody(
     *     required=true,
     *
     *     @OA\JsonContent(
     *       type="object",
     *       required={"code", "appType"},
     *
     *       @OA\Property(property="code", type="string"),
     *       @OA\Property(property="isForceUpdate", type="boolean"),
     *       @OA\Property(property="description", type="string"),
     *       @OA\Property(property="url", type="string"),
     *       @OA\Property(property="appType", type="string", enum={"web", "android", "ios"})
     *     )
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|regex:/^\d+(\.\d+){0,2}(-[a-zA-Z0-9.]+)?$/',
            'is_force_update' => 'nullable|boolean',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'app_type' => 'required|in:web,android,ios',
        ]);

        $isCodeExists = $this->appVersionRepository->checkCode(
            $request->code,
            $request->app_type
        );

        if ($isCodeExists) {
            return ResponseFormatter::error(400, 'Kode versi sudah terdaftar');
        }

        $this->appVersionRepository->create([
            'code' => $request->code,
            'is_force_update' => $request->boolean('is_force_update'),
            'description' => e($request->description),
            'url' => $request->url,
            'app_type' => $request->app_type,
        ]);

        return ResponseFormatter::success(null, 'Data berhasil disimpan');
    }

    /**
     * @OA\Put(
     *   tags={"Api|AppVersion"},
     *   path="/api/app-version/{id}",
     *   summary="AppVersion update",
     *
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\RequestBody(
     *     required=true,
     *
     *     @OA\JsonContent(
     *       type="object",
     *       required={"code", "appType"},
     *
     *       @OA\Property(property="code", type="string"),
     *       @OA\Property(property="isForceUpdate", type="boolean"),
     *       @OA\Property(property="description", type="string"),
     *       @OA\Property(property="url", type="string"),
     *       @OA\Property(property="appType", type="string", enum={"web", "android", "ios"})
     *     )
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|regex:/^\d+(\.\d+){0,2}(-[a-zA-Z0-9.]+)?$/',
            'is_force_update' => 'nullable|boolean',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'app_type' => 'required|in:web,android,ios',
        ]);

        $appVersion = $this->appVersionRepository->findByIdHash($id);

        if (! $appVersion) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }

        $isCodeExists = $this->appVersionRepository->checkCode(
            $request->code,
            $request->app_type,
            $appVersion->id
        );

        if ($isCodeExists) {
            return ResponseFormatter::error(400, 'Kode versi sudah terdaftar');
        }

        $this->appVersionRepository->update($appVersion, [
            'code' => $request->code,
            'is_force_update' => $request->boolean('is_force_update'),
            'description' => e($request->description),
            'url' => $request->url,
            'app_type' => $request->app_type,
        ]);

        return ResponseFormatter::success(null, 'Data berhasil diperbarui');
    }

    /**
     * @OA\Delete(
     *   tags={"Api|AppVersion"},
     *   path="/api/app-version/{id}",
     *   summary="AppVersion destroy",
     *
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $this->appVersionRepository->delete($id);

        return ResponseFormatter::success(null, 'Data berhasil dihapus');
    }
}

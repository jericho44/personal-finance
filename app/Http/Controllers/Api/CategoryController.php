<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $categoryRepository;

    public function __construct(\App\Interfaces\CategoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @OA\Get(
     *   tags={"Api|Category"},
     *   path="/api/categories",
     *   summary="Get list of categories",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="type", in="query", @OA\Schema(type="string", enum={"income", "expense"})),
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", default=15)),
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'nullable|numeric',
            'limit' => 'nullable|numeric|min:0|max:100',
        ]);

        $categories = $this->categoryRepository->getAll(
            userId: $request->user()->id,
            select: [],
            withRelations: [],
            join: [],
            filter: [
                'type' => $request->type
            ],
            where: null,
            search: $request->search,
            sortOption: [
                'orderCol' => $request->sort_by,
                'orderDir' => $request->order_by,
            ],
            paginateOption: [
                'method' => 'paginate',
                'length' => $request->limit ?? 15,
                'page' => $request->page,
            ],
            reformat: function ($models) {
                if ($models instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                    $models->getCollection()->transform(function ($row) {
                        return new \App\Http\Resources\CategoryResource($row);
                    });
                    return $models;
                }
                
                $models->transform(function ($row) {
                    return new \App\Http\Resources\CategoryResource($row);
                });
                return $models;
            }
        );

        return ResponseFormatter::success($categories, 'Data berhasil ditampilkan');
    }

    /**
     * @OA\Post(
     *   tags={"Api|Category"},
     *   path="/api/categories",
     *   summary="Store new category",
     *   security={{"authBearerToken":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name", "type"},
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="type", type="string", enum={"income", "expense"}),
     *       @OA\Property(property="color", type="string"),
     *       @OA\Property(property="icon", type="string")
     *     )
     *   ),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $category = $this->categoryRepository->create($request->all(), $request->user()->id);
        return ResponseFormatter::success(new \App\Http\Resources\CategoryResource($category), 'Data berhasil ditambahkan');
    }

    /**
     * @OA\Get(
     *   tags={"Api|Category"},
     *   path="/api/categories/{id}",
     *   summary="Get category detail",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function show(Request $request, $id)
    {
        $category = $this->categoryRepository->findByIdHash($id, [], $request->user()->id);
        if (!$category) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }
        return ResponseFormatter::success(new \App\Http\Resources\CategoryResource($category), 'Data berhasil ditampilkan');
    }

    /**
     * @OA\Put(
     *   tags={"Api|Category"},
     *   path="/api/categories/{id}",
     *   summary="Update category",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name", "type"},
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="type", type="string", enum={"income", "expense"}),
     *       @OA\Property(property="color", type="string"),
     *       @OA\Property(property="icon", type="string")
     *     )
     *   ),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $category = $this->categoryRepository->update($id, $request->all(), $request->user()->id);
        return ResponseFormatter::success(new \App\Http\Resources\CategoryResource($category), 'Data berhasil diperbarui');
    }

    /**
     * @OA\Delete(
     *   tags={"Api|Category"},
     *   path="/api/categories/{id}",
     *   summary="Delete category",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $this->categoryRepository->delete($id, $request->user()->id);
        return ResponseFormatter::success(null, 'Category deleted');
    }
}

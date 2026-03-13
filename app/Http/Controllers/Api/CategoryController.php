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

    public function show(Request $request, $id)
    {
        $category = $this->categoryRepository->findByIdHash($id, [], $request->user()->id);
        if (!$category) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }
        return ResponseFormatter::success(new \App\Http\Resources\CategoryResource($category), 'Data berhasil ditampilkan');
    }

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

    public function destroy(Request $request, $id)
    {
        $this->categoryRepository->delete($id, $request->user()->id);
        return ResponseFormatter::success(null, 'Category deleted');
    }
}

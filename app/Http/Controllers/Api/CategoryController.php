<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\Builder;
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
        $categories = $this->categoryRepository->getAll(
            select: [],
            withRelations: [],
            where: function (Builder $query) use ($request) {
                $query->where('user_id', $request->user()->id);
            },
            search: $request->search,
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
                            return new CategoryResource($row);
                        });
                });
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
        return response()->json(['success' => true, 'data' => $category]);
    }

    public function show(Request $request, $id)
    {
        $category = $this->categoryRepository->findByIdHash($id, $request->user()->id);
        return response()->json(['success' => true, 'data' => $category]);
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
        return response()->json(['success' => true, 'data' => $category]);
    }

    public function destroy(Request $request, $id)
    {
        $this->categoryRepository->delete($id, $request->user()->id);
        return response()->json(['success' => true, 'message' => 'Category deleted']);
    }
}

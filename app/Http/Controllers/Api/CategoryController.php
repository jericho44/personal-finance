<?php

namespace App\Http\Controllers\Api;

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
        $categories = $this->categoryRepository->getAll($request->user()->id);
        return response()->json(['success' => true, 'data' => $categories]);
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

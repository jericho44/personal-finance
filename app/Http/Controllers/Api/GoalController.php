<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    private $goalRepository;

    public function __construct(\App\Interfaces\GoalInterface $goalRepository)
    {
        $this->goalRepository = $goalRepository;
    }

    public function index(Request $request)
    {
        $goals = $this->goalRepository->getAll(
            userId: $request->user()->id,
            select: [],
            withRelations: [],
            filter: [
                'is_completed' => $request->is_completed
            ],
            search: $request->search,
            sortOption: [
                'orderCol' => $request->sort_by ?? 'created_at',
                'orderDir' => $request->order_by ?? 'desc'
            ],
            paginateOption: [
                'method' => 'paginate',
                'length' => $request->limit ?? 15,
                'page' => $request->page,
            ],
            reformat: function ($models) {
                if ($models instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                    $models->getCollection()->transform(function ($row) {
                        return new \App\Http\Resources\GoalResource($row);
                    });
                    return $models;
                }
                
                $models->transform(function ($row) {
                    return new \App\Http\Resources\GoalResource($row);
                });
                return $models;
            }
        );

        return ResponseFormatter::success($goals, 'Data berhasil ditampilkan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'numeric|min:0',
            'deadline' => 'nullable|date',
            'color' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_completed' => 'boolean',
        ]);

        $goal = $this->goalRepository->create($request->all(), $request->user()->id);
        return ResponseFormatter::success(new \App\Http\Resources\GoalResource($goal), 'Data berhasil ditambahkan');
    }

    public function show(Request $request, $id)
    {
        $goal = $this->goalRepository->findByIdHash($id, [], $request->user()->id);
        if (!$goal) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }
        return ResponseFormatter::success(new \App\Http\Resources\GoalResource($goal), 'Data berhasil ditampilkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'numeric|min:0',
            'deadline' => 'nullable|date',
            'color' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_completed' => 'boolean',
        ]);

        $goal = $this->goalRepository->update($id, $request->all(), $request->user()->id);
        return ResponseFormatter::success(new \App\Http\Resources\GoalResource($goal), 'Data berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $this->goalRepository->delete($id, $request->user()->id);
        return ResponseFormatter::success(null, 'Data berhasil dihapus');
    }
}

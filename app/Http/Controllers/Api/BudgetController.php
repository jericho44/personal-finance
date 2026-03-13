<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    private $budgetRepository;

    public function __construct(\App\Interfaces\BudgetInterface $budgetRepository)
    {
        $this->budgetRepository = $budgetRepository;
    }

    public function index(Request $request)
    {
        $budgets = $this->budgetRepository->getAll(
            userId: $request->user()->id,
            withRelations: ['category'],
            filter: [
                'is_active' => $request->is_active,
                'category_id' => $request->category_id
            ],
            search: $request->search,
            sortOption: [
                'orderCol' => $request->sort_by,
                'orderDir' => $request->order_by
            ],
            paginateOption: [
                'method' => 'paginate',
                'length' => $request->limit ?? 15,
                'page' => $request->page,
            ],
            reformat: function ($models) {
                if ($models instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                    $models->getCollection()->transform(function ($row) {
                        return new \App\Http\Resources\BudgetResource($row);
                    });
                    return $models;
                }
                
                $models->transform(function ($row) {
                    return new \App\Http\Resources\BudgetResource($row);
                });
                return $models;
            }
        );
        return ResponseFormatter::success($budgets, 'Data berhasil ditampilkan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $budget = $this->budgetRepository->create($request->all(), $request->user()->id);
        $budget->load('category');
        return ResponseFormatter::success(new \App\Http\Resources\BudgetResource($budget), 'Data berhasil ditambahkan');
    }

    public function show(Request $request, $id)
    {
        $budget = $this->budgetRepository->findByIdHash($id, ['category'], $request->user()->id);
        if (!$budget) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }
        return ResponseFormatter::success(new \App\Http\Resources\BudgetResource($budget), 'Data berhasil ditampilkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $budget = $this->budgetRepository->update($id, $request->all(), $request->user()->id);
        $budget->load('category');
        return ResponseFormatter::success(new \App\Http\Resources\BudgetResource($budget), 'Data berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $this->budgetRepository->delete($id, $request->user()->id);
        return ResponseFormatter::success(null, 'Data berhasil dihapus');
    }

    public function progress(Request $request)
    {
        $period = $request->query('period', 'current_month');
        $progress = $this->budgetRepository->getBudgetProgress($request->user()->id, $period);
        
        $reformattedProgress = collect($progress)->map(function ($item) {
            $item['budget'] = new \App\Http\Resources\BudgetResource($item['budget']);
            return $item;
        });

        return ResponseFormatter::success($reformattedProgress, 'Data kemajuan anggaran berhasil ditampilkan');
    }
}

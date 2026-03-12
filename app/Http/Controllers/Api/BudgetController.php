<?php

namespace App\Http\Controllers\Api;

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
        $filters = $request->only(['is_active', 'category_id']);
        $budgets = $this->budgetRepository->getAll($request->user()->id, $filters);
        return response()->json(['success' => true, 'data' => $budgets]);
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
        return response()->json(['success' => true, 'data' => $budget]);
    }

    public function show(Request $request, $id)
    {
        $budget = $this->budgetRepository->findByIdHash($id, ['category'], $request->user()->id);
        return response()->json(['success' => true, 'data' => $budget]);
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
        return response()->json(['success' => true, 'data' => $budget]);
    }

    public function destroy(Request $request, $id)
    {
        $this->budgetRepository->delete($id, $request->user()->id);
        return response()->json(['success' => true, 'message' => 'Budget deleted']);
    }

    public function progress(Request $request)
    {
        $period = $request->query('period', 'current_month');
        $progress = $this->budgetRepository->getBudgetProgress($request->user()->id, $period);
        return response()->json(['success' => true, 'data' => $progress]);
    }
}

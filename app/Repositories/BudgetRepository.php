<?php

namespace App\Repositories;

use App\Models\Budget;
use App\Interfaces\BudgetInterface;
use App\Models\Transaction;
use Carbon\Carbon;

class BudgetRepository extends BaseRepository implements BudgetInterface
{
    public function __construct(Budget $model)
    {
        parent::__construct($model);
    }

    public function getAll(
        int $userId,
        $select = [],
        $withRelations = [],
        $join = [],
        $filter = [],
        $where = null,
        $search = null,
        $sortOption = [],
        $paginateOption = [],
        $reformat = null
    ) {
        $model = $this->model->query();

        if (!empty($select)) {
            $model->select($select);
        }

        $model->where('user_id', $userId);

        if (! empty($withRelations)) {
            $model->with($withRelations);
        }

        if (is_callable($where)) {
            $model->where($where);
        }

        $model->when(strtolower($search), function ($query, $search) {
            $query->whereHas('category', function($q) use ($search) {
                $q->where(\Illuminate\Support\Facades\DB::raw('LOWER(categories.name)'), 'LIKE', "%{$search}%");
            });
        });

        if ($this->filled($filter, 'is_active')) {
            $model->where('is_active', collect([true, 1, '1', 'true'])->contains($filter['is_active']));
        }
        
        if ($this->filled($filter, 'category_id')) {
            $model->where('category_id', $filter['category_id']);
        }

        $model->orderBy(
            $this->input($sortOption, 'orderCol', 'start_date'),
            strtolower($this->input($sortOption, 'orderDir', 'desc')) === 'asc' ? 'asc' : 'desc'
        );

        $length = $this->input($paginateOption, 'length', 15);
        if (strtolower($this->input($paginateOption, 'method', 'paginate')) === 'paginate' && $length > 0) {
            $model = $model->paginate($length, ['*'], 'page', $this->input($paginateOption, 'page'));
        } else {
            $model = $model->limit($length > 0 ? $length : null)->get();
        }

        if (is_callable($reformat)) {
            $model = $reformat($model);
        }

        return $model;
    }

    public function findByIdHash($idHash, $withRelations = ['category'], int $userId = null)
    {
        $query = $this->model->where('id_hash', $idHash);
        if ($withRelations) {
            $query->with($withRelations);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->firstOrFail();
    }

    public function create($data, $userId = null)
    {
        if ($userId) {
            $data['user_id'] = $userId;
        }

        return parent::create($data);
    }

    public function update($idHash, $data, $userId = null)
    {
        $budget = $this->findByIdHash($idHash, [], $userId);
        return parent::update($budget, $data);
    }

    public function delete($idHash, $userId = null)
    {
        $budget = $this->findByIdHash($idHash, [], $userId);
        return parent::delete($budget);
    }

    public function getBudgetProgress(int $userId, string $period = 'current_month')
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');

        if ($period === 'last_month') {
            $startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        }

        // Get budgets overlapping with the requested period
        $budgets = $this->model->with(['category'])
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->get();

        $progressData = [];

        foreach ($budgets as $budget) {
            // Find total spent for this category in the budget period
            $spent = Transaction::where('user_id', $userId)
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->whereBetween('date', [$budget->start_date, $budget->end_date])
                ->sum('amount');
            
            $progressData[] = [
                'budget' => $budget,
                'spent' => (float) $spent,
                'remaining' => $budget->amount - $spent,
                'percentage' => $budget->amount > 0 ? min(round(($spent / $budget->amount) * 100, 2), 100) : 0,
                'isOverBudget' => $spent > $budget->amount
            ];
        }

        return $progressData;
    }
}

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

    /**
     * @OA\Get(
     *   tags={"Api|Budget"},
     *   path="/api/budgets",
     *   summary="Get list of budgets",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="is_active", in="query", @OA\Schema(type="boolean")),
     *   @OA\Parameter(name="category_id", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", default=15)),
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
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

    /**
     * @OA\Post(
     *   tags={"Api|Budget"},
     *   path="/api/budgets",
     *   summary="Store new budget",
     *   security={{"authBearerToken":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"category_id", "amount", "start_date", "end_date"},
     *       @OA\Property(property="category_id", type="string", example="cat-123"),
     *       @OA\Property(property="amount", type="number", format="float", example=1000000),
     *       @OA\Property(property="start_date", type="string", format="date", example="2024-03-01"),
     *       @OA\Property(property="end_date", type="string", format="date", example="2024-03-31"),
     *       @OA\Property(property="is_active", type="boolean", example=true),
     *       @OA\Property(property="notes", type="string", nullable=true)
     *     )
     *   ),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
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

    /**
     * @OA\Get(
     *   tags={"Api|Budget"},
     *   path="/api/budgets/{id}",
     *   summary="Get budget detail",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function show(Request $request, $id)
    {
        $budget = $this->budgetRepository->findByIdHash($id, ['category'], $request->user()->id);
        if (!$budget) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }
        return ResponseFormatter::success(new \App\Http\Resources\BudgetResource($budget), 'Data berhasil ditampilkan');
    }

    /**
     * @OA\Put(
     *   tags={"Api|Budget"},
     *   path="/api/budgets/{id}",
     *   summary="Update budget",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"category_id", "amount", "start_date", "end_date"},
     *       @OA\Property(property="category_id", type="string"),
     *       @OA\Property(property="amount", type="number", format="float"),
     *       @OA\Property(property="start_date", type="string", format="date"),
     *       @OA\Property(property="end_date", type="string", format="date"),
     *       @OA\Property(property="is_active", type="boolean"),
     *       @OA\Property(property="notes", type="string", nullable=true)
     *     )
     *   ),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
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

    /**
     * @OA\Delete(
     *   tags={"Api|Budget"},
     *   path="/api/budgets/{id}",
     *   summary="Delete budget",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $this->budgetRepository->delete($id, $request->user()->id);
        return ResponseFormatter::success(null, 'Data berhasil dihapus');
    }

    /**
     * @OA\Get(
     *   tags={"Api|Budget"},
     *   path="/api/budgets/progress",
     *   summary="Get budget progress for a specific period",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="period", in="query", @OA\Schema(type="string", enum={"current_month", "last_month", "this_year"})),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
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

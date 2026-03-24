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

    /**
     * @OA\Get(
     *   tags={"Api|Goal"},
     *   path="/api/goals",
     *   summary="Get list of financial goals",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="is_completed", in="query", @OA\Schema(type="boolean")),
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", default=15)),
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
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

    /**
     * @OA\Post(
     *   tags={"Api|Goal"},
     *   path="/api/goals",
     *   summary="Store new goal",
     *   security={{"authBearerToken":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name", "target_amount"},
     *       @OA\Property(property="name", type="string", example="New Laptop"),
     *       @OA\Property(property="target_amount", type="number", format="float", example=15000000),
     *       @OA\Property(property="current_amount", type="number", format="float", example=0),
     *       @OA\Property(property="deadline", type="string", format="date", example="2024-12-31"),
     *       @OA\Property(property="color", type="string", example="#28a745"),
     *       @OA\Property(property="notes", type="string", nullable=true),
     *       @OA\Property(property="is_completed", type="boolean", example=false)
     *     )
     *   ),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
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

    /**
     * @OA\Get(
     *   tags={"Api|Goal"},
     *   path="/api/goals/{id}",
     *   summary="Get goal detail",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function show(Request $request, $id)
    {
        $goal = $this->goalRepository->findByIdHash($id, [], $request->user()->id);
        if (!$goal) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }
        return ResponseFormatter::success(new \App\Http\Resources\GoalResource($goal), 'Data berhasil ditampilkan');
    }

    /**
     * @OA\Put(
     *   tags={"Api|Goal"},
     *   path="/api/goals/{id}",
     *   summary="Update goal",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name", "target_amount"},
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="target_amount", type="number", format="float"),
     *       @OA\Property(property="current_amount", type="number", format="float"),
     *       @OA\Property(property="deadline", type="string", format="date"),
     *       @OA\Property(property="color", type="string"),
     *       @OA\Property(property="notes", type="string", nullable=true),
     *       @OA\Property(property="is_completed", type="boolean")
     *     )
     *   ),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
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

    /**
     * @OA\Delete(
     *   tags={"Api|Goal"},
     *   path="/api/goals/{id}",
     *   summary="Delete goal",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $this->goalRepository->delete($id, $request->user()->id);
        return ResponseFormatter::success(null, 'Data berhasil dihapus');
    }
}

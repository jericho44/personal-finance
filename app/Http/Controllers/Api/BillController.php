<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillController extends Controller
{
    private $billRepository;

    public function __construct(\App\Interfaces\BillInterface $billRepository)
    {
        $this->billRepository = $billRepository;
    }

    /**
     * @OA\Get(
     *   tags={"Api|Bill"},
     *   path="/api/bills",
     *   summary="Get list of recurring bills",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="category_id", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="is_paid", in="query", @OA\Schema(type="boolean")),
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", default=15)),
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function index(Request $request)
    {
        $bills = $this->billRepository->getAll(
            userId: $request->user()->id,
            select: [],
            withRelations: ['category'],
            filter: [
                'category_id' => $request->category_id,
                'is_paid' => $request->is_paid
            ],
            search: $request->search,
            sortOption: [
                'orderCol' => $request->sort_by ?? 'due_date',
                'orderDir' => $request->order_by ?? 'asc'
            ],
            paginateOption: [
                'method' => 'paginate',
                'length' => $request->limit ?? 15,
                'page' => $request->page,
            ],
            reformat: function ($models) {
                if ($models instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                    $models->getCollection()->transform(function ($row) {
                        return new \App\Http\Resources\BillResource($row);
                    });
                    return $models;
                }
                
                $models->transform(function ($row) {
                    return new \App\Http\Resources\BillResource($row);
                });
                return $models;
            }
        );

        return ResponseFormatter::success($bills, 'Data berhasil ditampilkan');
    }

    /**
     * @OA\Post(
     *   tags={"Api|Bill"},
     *   path="/api/bills",
     *   summary="Store new bill",
     *   security={{"authBearerToken":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name", "amount", "due_date", "frequency"},
     *       @OA\Property(property="name", type="string", example="Spotify"),
     *       @OA\Property(property="amount", type="number", format="float", example=55000),
     *       @OA\Property(property="due_date", type="string", format="date", example="2024-03-25"),
     *       @OA\Property(property="frequency", type="string", enum={"once", "daily", "weekly", "monthly", "yearly"}),
     *       @OA\Property(property="is_paid", type="boolean", example=false),
     *       @OA\Property(property="category_id", type="string", example="cat-123"),
     *       @OA\Property(property="notes", type="string", nullable=true)
     *     )
     *   ),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'frequency' => 'required|in:once,daily,weekly,monthly,yearly',
            'is_paid' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
            'notes' => 'nullable|string',
        ]);

        $bill = $this->billRepository->create($request->all(), $request->user()->id);
        $bill->load('category');
        return ResponseFormatter::success(new \App\Http\Resources\BillResource($bill), 'Data berhasil ditambahkan');
    }

    /**
     * @OA\Get(
     *   tags={"Api|Bill"},
     *   path="/api/bills/{id}",
     *   summary="Get bill detail",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function show(Request $request, $id)
    {
        $bill = $this->billRepository->findByIdHash($id, ['category'], $request->user()->id);
        if (!$bill) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }
        return ResponseFormatter::success(new \App\Http\Resources\BillResource($bill), 'Data berhasil ditampilkan');
    }

    /**
     * @OA\Put(
     *   tags={"Api|Bill"},
     *   path="/api/bills/{id}",
     *   summary="Update bill",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name", "amount", "due_date", "frequency"},
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="amount", type="number", format="float"),
     *       @OA\Property(property="due_date", type="string", format="date"),
     *       @OA\Property(property="frequency", type="string", enum={"once", "daily", "weekly", "monthly", "yearly"}),
     *       @OA\Property(property="is_paid", type="boolean"),
     *       @OA\Property(property="category_id", type="string"),
     *       @OA\Property(property="notes", type="string", nullable=true)
     *     )
     *   ),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'frequency' => 'required|in:once,daily,weekly,monthly,yearly',
            'is_paid' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
            'notes' => 'nullable|string',
        ]);

        $bill = $this->billRepository->update($id, $request->all(), $request->user()->id);
        $bill->load('category');
        return ResponseFormatter::success(new \App\Http\Resources\BillResource($bill), 'Data berhasil diperbarui');
    }

    /**
     * @OA\Delete(
     *   tags={"Api|Bill"},
     *   path="/api/bills/{id}",
     *   summary="Delete bill",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $this->billRepository->delete($id, $request->user()->id);
        return ResponseFormatter::success(null, 'Data berhasil dihapus');
    }
}

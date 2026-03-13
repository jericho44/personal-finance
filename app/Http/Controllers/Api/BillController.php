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

    public function show(Request $request, $id)
    {
        $bill = $this->billRepository->findByIdHash($id, ['category'], $request->user()->id);
        if (!$bill) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }
        return ResponseFormatter::success(new \App\Http\Resources\BillResource($bill), 'Data berhasil ditampilkan');
    }

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

    public function destroy(Request $request, $id)
    {
        $this->billRepository->delete($id, $request->user()->id);
        return ResponseFormatter::success(null, 'Data berhasil dihapus');
    }
}

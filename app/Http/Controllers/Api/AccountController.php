<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    private $accountRepository;

    public function __construct(\App\Interfaces\AccountInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function index(Request $request)
    {
        $accounts = $this->accountRepository->getAll(
            userId: $request->user()->id,
            filter: [
                'type' => $request->type
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
                        return new \App\Http\Resources\AccountResource($row);
                    });
                    return $models;
                }
                
                $models->transform(function ($row) {
                    return new \App\Http\Resources\AccountResource($row);
                });
                return $models;
            }
        );

        return ResponseFormatter::success($accounts, 'Data berhasil ditampilkan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:cash,bank,ewallet,credit_card,investment',
            'balance' => 'numeric',
            'currency' => 'string|max:3',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $account = $this->accountRepository->create($request->all(), $request->user()->id);
        return ResponseFormatter::success(new \App\Http\Resources\AccountResource($account), 'Data berhasil ditambahkan');
    }

    public function show(Request $request, $id)
    {
        $account = $this->accountRepository->findByIdHash($id, [], $request->user()->id);
        if (!$account) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }
        return ResponseFormatter::success(new \App\Http\Resources\AccountResource($account), 'Data berhasil ditampilkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:cash,bank,ewallet,credit_card,investment',
            'balance' => 'numeric',
            'currency' => 'string|max:3',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $account = $this->accountRepository->update($id, $request->all(), $request->user()->id);
        return ResponseFormatter::success(new \App\Http\Resources\AccountResource($account), 'Data berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $this->accountRepository->delete($id, $request->user()->id);
        return ResponseFormatter::success(null, 'Data berhasil dihapus');
    }
}

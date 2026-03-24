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

    /**
     * @OA\Get(
     *   tags={"Api|Account"},
     *   path="/api/accounts",
     *   summary="Get list of accounts",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="type", in="query", @OA\Schema(type="string", enum={"cash", "bank", "ewallet", "credit_card", "investment"})),
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", default=15)),
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
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

    /**
     * @OA\Post(
     *   tags={"Api|Account"},
     *   path="/api/accounts",
     *   summary="Store new account",
     *   security={{"authBearerToken":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name", "type"},
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="type", type="string", enum={"cash", "bank", "ewallet", "credit_card", "investment"}),
     *       @OA\Property(property="balance", type="number", format="float"),
     *       @OA\Property(property="currency", type="string", maxLength=3),
     *       @OA\Property(property="color", type="string"),
     *       @OA\Property(property="icon", type="string")
     *     )
     *   ),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
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

    /**
     * @OA\Get(
     *   tags={"Api|Account"},
     *   path="/api/accounts/{id}",
     *   summary="Get account detail",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function show(Request $request, $id)
    {
        $account = $this->accountRepository->findByIdHash($id, [], $request->user()->id);
        if (!$account) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }
        return ResponseFormatter::success(new \App\Http\Resources\AccountResource($account), 'Data berhasil ditampilkan');
    }

    /**
     * @OA\Put(
     *   tags={"Api|Account"},
     *   path="/api/accounts/{id}",
     *   summary="Update account",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name", "type"},
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="type", type="string", enum={"cash", "bank", "ewallet", "credit_card", "investment"}),
     *       @OA\Property(property="balance", type="number", format="float"),
     *       @OA\Property(property="currency", type="string", maxLength=3),
     *       @OA\Property(property="color", type="string"),
     *       @OA\Property(property="icon", type="string")
     *     )
     *   ),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
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

    /**
     * @OA\Delete(
     *   tags={"Api|Account"},
     *   path="/api/accounts/{id}",
     *   summary="Delete account",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $this->accountRepository->delete($id, $request->user()->id);
        return ResponseFormatter::success(null, 'Data berhasil dihapus');
    }
}

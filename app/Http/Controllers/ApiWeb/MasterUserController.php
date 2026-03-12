<?php

namespace App\Http\Controllers\ApiWeb;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Interfaces\RoleInterface;
use App\Interfaces\UserInterface;
use App\Rules\SecurePasswordRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MasterUserController extends Controller
{
    public function __construct(
        private UserInterface $userRepository,
        private RoleInterface $roleRepository
    ) {}

    /**
     * @OA\Get(
     *   tags={"ApiWeb|Master|User"},
     *   path="/api-web/master/user",
     *   summary="User index",
     *
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\Parameter(
     *     name="limit",
     *     in="query",
     *
     *     @OA\Schema(type="integer", example="10")
     *   ),
     *
     *   @OA\Parameter(
     *     name="roleId",
     *     in="query",
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\Parameter(
     *     name="isActive",
     *     in="query",
     *
     *     @OA\Schema(type="integer", enum={0,1})
     *   ),
     *
     *   @OA\Parameter(
     *     name="sortBy",
     *     in="query",
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\Parameter(
     *     name="orderBy",
     *     in="query",
     *
     *     @OA\Schema(type="string", enum={"asc","desc"})
     *   ),
     *
     *   @OA\Parameter(
     *     name="page",
     *     in="query",
     *
     *     @OA\Schema(type="integer", example="1")
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'nullable|numeric',
            'limit' => 'nullable|numeric|min:0|max:100',
        ]);

        $users = $this->userRepository->getAll(
            select: [],
            withRelations: ['role'],
            filter: [
                'roleId' => $request->role_id,
                'isActive' => $request->is_active,
            ],
            search: $request->search,
            sortOption: [
                'orderCol' => $request->sort_by,
                'orderDir' => $request->order_by,
            ],
            paginateOption: [
                'method' => 'paginate',
                'length' => $request->limit,
                'page' => $request->page,
            ],
            reformat: function ($model) {
                return tap($model, function ($paginate) {
                    return $paginate->getCollection()
                        ->transform(function ($row) {
                            return new UserResource($row);
                        });
                });
            }
        );

        return ResponseFormatter::success($users, 'Data berhasil ditampilkan');
    }

    /**
     * @OA\Get(
     *   tags={"ApiWeb|Master|User"},
     *   path="/api-web/master/user/{id}",
     *   summary="User show",
     *
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function show(Request $request, $id)
    {
        $user = $this->userRepository->findByIdHash($id);

        if (! $user) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }

        $user->load('role');

        return ResponseFormatter::success(new UserResource($user), 'Data berhasil ditampilkan');
    }

    /**
     * @OA\Post(
     *     tags={"ApiWeb|Master|User"},
     *     path="/api-web/master/user",
     *     summary="User store",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *             required={"roleId","username","password","passwordConfirmed"},
     *
     *             @OA\Property(property="roleId", type="string"),
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="passwordConfirmation", type="string", format="password"),
     *             @OA\Property(property="consultantId", type="string"),
     *         )
     *     ),
     *
     *     @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|uuid',
            'username' => 'required|min:3|unique:users,username',
            'name' => 'required|max:255|regex:/^[a-zA-Z0-9.,\s\-]+$/',
            'password' => ['required', 'confirmed', new SecurePasswordRule],
            'password_confirmation' => 'required',
        ]);

        $role = $this->roleRepository->findByIdHash($request->role_id);

        if (! $role) {
            return ResponseFormatter::error(400, 'Data role tidak ditemukan');
        }

        $this->userRepository->create([
            'role_id' => $role->id,
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return ResponseFormatter::success(null, 'Data berhasil ditambahkan');
    }

    /**
     * @OA\Put(
     *     tags={"ApiWeb|Master|User"},
     *     path="/api-web/master/user/{id}",
     *     summary="User update",
     *
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string", example="")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="roleId", type="string"),
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="name", type="string"),
     *         )
     *     ),
     *
     *     @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function update(Request $request, $id)
    {
        $user = $this->userRepository->findByIdHash($id);

        if (! $user) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }

        $request->validate([
            'role_id' => 'required|uuid',
            'username' => "required|min:3|unique:users,username,{$user->id}",
            'name' => 'required|max:255|regex:/^[a-zA-Z0-9.,\s\-]+$/',
        ]);

        $role = $this->roleRepository->findByIdHash($request->role_id);

        if (! $role) {
            return ResponseFormatter::error(400, 'Data role tidak ditemukan');
        }

        $this->userRepository->update($user, [
            'role_id' => $role->id,
            'name' => $request->name,
            'username' => $request->username,
        ]);

        return ResponseFormatter::success(null, 'Data berhasil ditambahkan');
    }

    /**
     * @OA\Put(
     *     tags={"ApiWeb|Master|User"},
     *     path="/api-web/master/user/{id}/change-status",
     *     summary="User changeStatus",
     *
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function changeStatus(Request $request, $id)
    {
        $user = $this->userRepository->switchStatus($id);

        return ResponseFormatter::success($user, 'Status berhasil diperbarui');
    }

    /**
     * @OA\Put(
     *     tags={"ApiWeb|Master|User"},
     *     path="/api-web/master/user/{id}/reset-password",
     *     summary="User resetPassword",
     *
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function resetPassword(Request $request, $id)
    {
        $this->userRepository->resetPassword($id);

        return ResponseFormatter::success(null, 'Password berhasil direset');
    }
}

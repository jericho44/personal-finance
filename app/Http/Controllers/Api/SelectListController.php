<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Http\Resources\RoleResource;
use App\Interfaces\RoleInterface;
use App\Interfaces\UserInterface;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravolt\Indonesia\IndonesiaService;
use Laravolt\Indonesia\Models\City;

class SelectListController extends Controller
{
    public function __construct(
        private RoleInterface $roleRepository,
        private UserInterface $userRepository,
        private IndonesiaService $indonesiaService
    ) {}

    /**
     * @OA\Get(
     *   tags={"Api|SelectList|Role"},
     *   path="/api/select-list/role",
     *   summary="SelectList Role",
     *   description="List of role",
     *
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *
     *     @OA\Schema(type="string"),
     *   ),
     *
     *   @OA\Parameter(
     *     name="limit",
     *     in="query",
     *
     *     @OA\Schema(type="integer"),
     *   ),
     *
     *   @OA\Parameter(
     *     name="sortBy",
     *     in="query",
     *
     *     @OA\Schema(type="string"),
     *   ),
     *
     *   @OA\Parameter(
     *     name="orderBy",
     *     in="query",
     *
     *     @OA\Schema(type="string", enum={"asc","desc"}),
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function role(Request $request)
    {
        $request->validate([
            'limit' => 'nullable|numeric|min:0|max:100',
        ]);

        $roles = $this->roleRepository->getAll(
            select: [],
            withRelations: [],
            filter: [
                'isActive' => true,
            ],
            search: $request->search,
            sortOption: [
                'orderCol' => $request->sort_by,
                'orderDir' => $request->order_by,
            ],
            paginateOption: [
                'length' => $request->limit,
            ],
            reformat: function ($model) {
                return RoleResource::collection($model);
            }
        );

        return ResponseFormatter::success($roles);
    }

    /**
     * @OA\Get(
     *   tags={"Api|SelectList|User"},
     *   path="/api/select-list/user",
     *   summary="SelectList User",
     *   description="List of user",
     *
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *
     *     @OA\Schema(type="string"),
     *   ),
     *
     *   @OA\Parameter(
     *     name="limit",
     *     in="query",
     *
     *     @OA\Schema(type="integer"),
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
     *     name="sortBy",
     *     in="query",
     *
     *     @OA\Schema(type="string"),
     *   ),
     *
     *   @OA\Parameter(
     *     name="orderBy",
     *     in="query",
     *
     *     @OA\Schema(type="string", enum={"asc","desc"}),
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function user(Request $request)
    {
        $request->validate([
            'limit' => 'nullable|numeric|min:0|max:100',
        ]);

        $users = $this->userRepository->getAll(
            select: [],
            withRelations: [
                'role',
            ],
            where: null,
            search: $request->search,
            filter: [
                'roleId' => $request->role_id,
                'isActive' => true,
            ],
            sortOption: [
                'orderCol' => $request->sort_by,
                'orderDir' => $request->order_by,
            ],
            paginateOption: [
                'length' => $request->limit,
            ],
            reformat: function ($model) {
                return RoleResource::collection($model);
            }
        );

        return ResponseFormatter::success($users);
    }

    /**
     * @OA\Get(
     *   tags={"Api|SelectList|Locations"},
     *   path="/api/select-list/provinces",
     *   summary="SelectList Provinces",
     *   description="Get list of provinces, optionally filtered by 'search'",
     *
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *
     *     @OA\Schema(type="string"),
     *     description="Text to filter provinces by name (case-insensitive)"
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function provinces(Request $request)
    {
        $provinces = $this->indonesiaService->search($request->search)->allProvinces();

        return ResponseFormatter::success($provinces, 'Data retrieved successfully');
    }

    /**
     * @OA\Get(
     *   tags={"Api|SelectList|Locations"},
     *   path="/api/select-list/cities",
     *   summary="SelectList Cities",
     *   description="Get list of cities by province_code (request 'id'), optionally filtered by 'search'",
     *
     *   @OA\Parameter(
     *     name="id",
     *     in="query",
     *
     *     @OA\Schema(type="string"),
     *     description="Province code"
     *   ),
     *
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *
     *     @OA\Schema(type="string"),
     *     description="Text to filter cities by name (case-insensitive)"
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function cities(Request $request)
    {
        $cities = City::where('province_code', $request->id);
        if (! empty($request->search)) {
            $searchTerm = '%'.strtolower($request->search).'%';
            $cities->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
        }

        return ResponseFormatter::success($cities->get(), 'Data retrieved successfully');
    }

    /**
     * @OA\Get(
     *   tags={"Api|SelectList|Locations"},
     *   path="/api/select-list/districts",
     *   summary="SelectList Districts",
     *   description="Get list of districts by city ID (request 'id')",
     *
     *   @OA\Parameter(
     *     name="id",
     *     in="query",
     *
     *     @OA\Schema(type="string"),
     *     description="City ID"
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function districts(Request $request)
    {
        return $this->indonesiaService
            ->findCity($request->id, ['districts'])
            ->districts
            ->pluck('name', 'id');
    }

    /**
     * @OA\Get(
     *   tags={"Api|SelectList|Locations"},
     *   path="/api/select-list/villages",
     *   summary="SelectList Villages",
     *   description="Get list of villages by district ID (request 'id')",
     *
     *   @OA\Parameter(
     *     name="id",
     *     in="query",
     *
     *     @OA\Schema(type="string"),
     *     description="District ID"
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function villages(Request $request)
    {
        return $this->indonesiaService->findDistrict($request->id, ['villages'])->villages->pluck('name', 'id');
    }

    /**
     * @OA\Get(
     *   tags={"Api|SelectList|Locations"},
     *   path="/api/select-list/countries",
     *   summary="SelectList Countries",
     *   description="Get a limited list of countries, optionally filtered by 'search'. Sortable by 'sort_by' with direction 'order_by'.",
     *
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *
     *     @OA\Schema(type="string"),
     *     description="Partial match filter for country code or name (case-insensitive)"
     *   ),
     *
     *   @OA\Parameter(
     *     name="limit",
     *     in="query",
     *
     *     @OA\Schema(type="integer", minimum=1, maximum=100),
     *     description="Number of countries to fetch"
     *   ),
     *
     *   @OA\Parameter(
     *     name="sort_by",
     *     in="query",
     *
     *     @OA\Schema(type="string", enum={"id","code","name"}),
     *     description="Sort column"
     *   ),
     *
     *   @OA\Parameter(
     *     name="order_by",
     *     in="query",
     *
     *     @OA\Schema(type="string", enum={"asc","desc"}),
     *     description="Sort direction"
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function countries(Request $request)
    {
        $request->validate([
            'limit' => 'nullable|numeric|min:1|max:100',
            'sort_by' => 'nullable|in:id,code,name',
            'order_by' => 'nullable|in:asc,desc',
        ]);

        $search = strtolower($request->input('search'));
        $orderBy = $request->input('sort_by', 'name');
        $orderDir = $request->input('order_by', 'asc');
        $limit = $request->input('limit', 18);

        $model = Country::query()
            ->when($search, function ($query, $value) {
                $likeValue = '%'.$value.'%';
                $query->where(function ($q) use ($likeValue) {
                    $q->where(DB::raw('LOWER(code)'), 'like', $likeValue)
                        ->orWhere(DB::raw('LOWER(name)'), 'like', $likeValue);
                });
            })
            ->orderBy($orderBy, $orderDir)
            ->limit($limit)
            ->get();

        return ResponseFormatter::success(
            CountryResource::collection($model),
            'Countries retrieved successfully'
        );
    }
}

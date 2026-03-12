<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Interfaces\ProvinceInterface;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function __construct(
        private ProvinceInterface $provinceRepository
    ) {}

    /**
     * @OA\Get(
     *   tags={"Api|Province"},
     *   path="/api/provinces",
     *   summary="Get paginated Provinces",
     *   description="Get a list of provinces with pagination, possibly filtered by search text, sorted by column and direction",
     *
     *   @OA\Parameter(
     *     name="page",
     *     in="query",
     *     required=false,
     *     description="Page number",
     *
     *     @OA\Schema(type="integer", default=1)
     *   ),
     *
     *   @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     required=false,
     *     description="Items per page",
     *
     *     @OA\Schema(type="integer", default=10)
     *   ),
     *
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *     description="Name search text",
     *     required=false,
     *
     *     @OA\Schema(type="string", default="")
     *   ),
     *
     *   @OA\Parameter(
     *     name="sort_by",
     *     in="query",
     *     required=false,
     *     description="Column to sort by",
     *
     *     @OA\Schema(type="string", default="name")
     *   ),
     *
     *   @OA\Parameter(
     *     name="order_by",
     *     in="query",
     *     required=false,
     *     description="Sort direction (asc/desc)",
     *
     *     @OA\Schema(type="string", default="asc")
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $limit = (int) $request->input('limit', 10);
        $page = (int) $request->input('page', 1);
        $sortBy = $request->input('sort_by', 'name');
        $orderBy = $request->input('order_by', 'asc');

        $provinces = $this->provinceRepository->getProvincesPaginated(
            search: $search,
            sortBy: $sortBy,
            orderBy: $orderBy,
            length: $limit,
            page: $page
        );

        return ResponseFormatter::success($provinces, 'Provinces retrieved successfully');
    }
}

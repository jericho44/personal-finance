<?php

namespace App\Http\Controllers\ApiWeb;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct() {}

    /**
     * @OA\Get(
     *   tags={"ApiWeb|Dashboard"},
     *   path="/api-web/dashboard",
     *   summary="Index",
     *
     *   @OA\Parameter(
     *     name="startYear",
     *     in="query",
     *
     *     @OA\Schema(type="string", description="Y")
     *   ),
     *
     *   @OA\Parameter(
     *     name="endYear",
     *     in="query",
     *
     *     @OA\Schema(type="string", description="Y")
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'start_year' => 'nullable|date_format:Y',
            'end_year' => 'nullable|date_format:Y',
        ]);

        $charts = [
            'card1' => 100,
            'card2' => 492,
            'card3' => 721,
            'card4' => 0,
        ];

        return ResponseFormatter::success(compact('charts'), 'Data berhasil ditampilkan');
    }
}

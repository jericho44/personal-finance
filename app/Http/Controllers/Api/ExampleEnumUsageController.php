<?php

namespace App\Http\Controllers\Api;

use App\Enums\ExampleStatusEnum;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ExampleEnumUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'bar_value' => ExampleStatusEnum::BAR,
            'bar_is' => ExampleStatusEnum::isBar(ExampleStatusEnum::BAR),
            'bar_label' => ExampleStatusEnum::getLabel(ExampleStatusEnum::BAR),
            'approved_value' => ExampleStatusEnum::APPROVED,
            'approved_is' => ExampleStatusEnum::isApproved(ExampleStatusEnum::APPROVED),
            'approved_label' => ExampleStatusEnum::getLabel(ExampleStatusEnum::APPROVED),
            // 'foo_value' => ExampleStatusEnum::FOO, // throw Error (Unregistered)
            // 'foo_is' => ExampleStatusEnum::isFoo(99), throw Error (Unregistered)
            'foo_label' => ExampleStatusEnum::getLabel(99),
        ];

        // example with database
        // $user = User::all()->map(function ($row) {
        //     $row->status_label = ExampleStatusEnum::isApproved($row->status);
        //     return $row;
        // });

        return ResponseFormatter::success($data);

        // response
        // {
        //     "meta": {
        //         "code": 200,
        //         "status": "success",
        //         "message": null
        //     },
        //     "data": {
        //         "draftValue": 0,
        //         "draftIs": true,
        //         "draftLabel": null,
        //         "approvedValue": 2,
        //         "approvedIs": true,
        //         "approvedLabel": "Aproved",
        //         "fooLabel": null
        //     }
        // }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

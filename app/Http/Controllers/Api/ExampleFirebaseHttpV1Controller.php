<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ExampleFirebaseHttpV1Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ExampleFirebaseHttpV1Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * @OA\Post(
     *   tags={"Api|Example|FirebaseHttpV1"},
     *   path="/api/example/firebase-http-v1",
     *   summary="Example firebase http v1 on store",
     *
     *   @OA\RequestBody(
     *     required=true,
     *
     *     @OA\JsonContent(
     *       type="object",
     *       required={"name"},
     *
     *       @OA\Property(property="name", type="string"),
     *     )
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        Notification::send(
            User::where('id', 3)->get(),
            new ExampleFirebaseHttpV1Notification($request->all())
        );

        return ResponseFormatter::success();
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

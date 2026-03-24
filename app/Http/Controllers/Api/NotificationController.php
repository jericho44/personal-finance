<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * @OA\Get(
     *   tags={"Api|Notification"},
     *   path="/api/notification",
     *   summary="Notification Index",
     *
     *   @OA\Parameter(
     *     name="page",
     *     in="query",
     *     required=false,
     *
     *     @OA\Schema(type="integer", example="1")
     *   ),
     *
     *   @OA\Parameter(
     *     name="length",
     *     in="query",
     *     required=false,
     *
     *     @OA\Schema(type="integer", example="10")
     *   ),
     *
     *   @OA\Parameter(
     *     name="status",
     *     in="query",
     *     required=false,
     *
     *     @OA\Schema(type="string", enum={"read","unread"})
     *   ),
     *
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *     required=false,
     *
     *     @OA\Schema(type="string", example="")
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'nullable|numeric',
            'length' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|in:read,unread',
        ]);

        $user = $request->user();

        $notifications = tap(
            $user->notifications()
                ->when($request->status, function ($query, $status) {
                    switch ($status) {
                        case 'read':
                            $query->whereNotNull('read_at');
                            break;

                        case 'unread':
                            $query->whereNull('read_at');
                            break;

                        default:
                            // code...
                            break;
                    }
                })
                ->orderBy('created_at', 'desc')
                ->paginate($request->input('length', 10)),
            function ($paginate) {
                return $paginate->getCollection()
                    ->transform(function ($row) {
                        return new NotificationResource($row);
                    });
            }
        );

        return ResponseFormatter::success($notifications, 'Data berhasil ditampilkan');
    }

    /**
     * @OA\Get(
     *   tags={"Api|Notification"},
     *   path="/api/notification/read/{id}",
     *   summary="Notification read",
     *
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string", example="")
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function read(Request $request, $id)
    {
        $user = $request->user();

        $notification = $user->notifications()->find($id);

        if (! $notification) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }

        $notification->markAsRead();

        return ResponseFormatter::success(null, 'Data berhasil ditandai sudah dibaca');
    }

    /**
     * @OA\Post(
     *   tags={"Api|Notification"},
     *   path="/api/notification/read-all",
     *   summary="Mark all notifications as read",
     *   security={{"authBearerToken":{}}},
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function readAll(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return ResponseFormatter::success(null, 'Data berhasil ditandai sudah dibaca semua');
    }

    /**
     * @OA\Get(
     *   tags={"Api|Notification"},
     *   path="/api/notification/unread-count",
     *   summary="Get unread notification count",
     *   security={{"authBearerToken":{}}},
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();
        $count = $user->unreadNotifications()->count();

        return ResponseFormatter::success(compact('count'), 'Data jumlah belum dibaca berhasil ditampilkan');
    }
}

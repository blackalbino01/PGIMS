<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for a given notifiable entity.
     *
     * Accepts optional query parameters:
     * - notifiable_type: The class/type of the notifiable entity.
     * - notifiable_id: The ID of the notifiable entity.
     *
     * @queryParam notifiable_type string Optional class name of the notifiable entity. Example: App\Models\User
     * @queryParam notifiable_id int Optional ID of the notifiable entity. Example: 1
     *
     * @response [
     *   {
     *     "id": 5,
     *     "type": "info",
     *     "title": "New Update Available",
     *     "message": "Version 2.0 is now live!",
     *     "is_read": false,
     *     "notifiable_type": "App\Models\User",
     *     "notifiable_id": 1,
     *     "created_at": "2025-09-19T09:18:00Z",
     *     "updated_at": "2025-09-19T09:18:00Z"
     *   }
     * ]
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Notification::query();

        if ($request->filled(key: 'notifiable_type') && $request->filled(key: 'notifiable_id')) {
            $query->where(column: 'notifiable_type', operator: $request->notifiable_type)
                  ->where(column: 'notifiable_id', operator: $request->notifiable_id);
        }

        return $query->get();
    }

    /**
     * Display a specific notification.
     *
     * @urlParam notification int required The ID of the notification.
     *
     * @response {
     *   "id": 5,
     *   "type": "info",
     *   "title": "New Update Available",
     *   "message": "Version 2.0 is now live!",
     *   "is_read": false,
     *   "notifiable_type": "App\Models\User",
     *   "notifiable_id": 1,
     *   "created_at": "2025-09-19T09:18:00Z",
     *   "updated_at": "2025-09-19T09:18:00Z"
     * }
     *
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Notification $notification)
    {
        return $notification;
    }

    /**
     * Store a new notification.
     *
     * @bodyParam type string required The notification type/category. Example: info
     * @bodyParam title string required Notification title. Example: New Update Available
     * @bodyParam message string required Notification message content.
     * @bodyParam is_read boolean Whether the notification has been read. Defaults to false.
     * @bodyParam notifiable_type string required The class/type of the notifiable entity. Example: App\Models\User
     * @bodyParam notifiable_id int required The ID of the notifiable entity. Example: 1
     *
     * @response 201 {
     *   "id": 5,
     *   "type": "info",
     *   "title": "New Update Available",
     *   "message": "Version 2.0 is now live!",
     *   "is_read": false,
     *   "notifiable_type": "App\Models\User",
     *   "notifiable_id": 1,
     *   "created_at": "2025-09-19T09:18:00Z",
     *   "updated_at": "2025-09-19T09:18:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'type' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
            'is_read' => 'boolean',
            'notifiable_type' => 'required|string',
            'notifiable_id' => 'required|integer',
        ]);

        $notification = Notification::create(attributes: $validated);

        return response()->json(data: $notification, status: 201);
    }

    /**
     * Update a notification.
     *
     * @urlParam notification int required The ID of the notification.
     * @bodyParam type string Nullable Notification type/category.
     * @bodyParam title string Nullable Notification title.
     * @bodyParam message string Nullable Notification message.
     * @bodyParam is_read boolean Nullable Whether the notification has been read.
     * @bodyParam notifiable_type string Nullable Class/type of the notifiable entity.
     * @bodyParam notifiable_id int Nullable ID of the notifiable entity.
     *
     * @response {
     *   "id": 5,
     *   "type": "update",
     *   "title": "Update Released",
     *   "message": "Version 2.0 has been released.",
     *   "is_read": true,
     *   "notifiable_type": "App\Models\User",
     *   "notifiable_id": 1,
     *   "created_at": "2025-09-19T09:18:00Z",
     *   "updated_at": "2025-09-19T10:00:00Z"
     * }
     *
     * @param Request $request
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Notification $notification)
    {
        $validated = $request->validate(rules: [
            'type' => 'sometimes|string',
            'title' => 'sometimes|string',
            'message' => 'sometimes|string',
            'is_read' => 'boolean',
            'notifiable_type' => 'sometimes|string',
            'notifiable_id' => 'sometimes|integer',
        ]);

        $notification->update(attributes: $validated);

        return response()->json(data: $notification);
    }

    /**
     * Delete a notification.
     *
     * @urlParam notification int required The ID of the notification.
     *
     * @response 204 {}
     *
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return response()->json(data: null, status: 204);
    }
}
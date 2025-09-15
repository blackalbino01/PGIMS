<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for a given notifiable.
     * Accepts optional query parameters: notifiable_type and notifiable_id.
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
     */
    public function show(Notification $notification)
    {
        return $notification;
    }

    /**
     * Store a new notification.
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
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return response()->json(data: null, status: 204);
    }
}

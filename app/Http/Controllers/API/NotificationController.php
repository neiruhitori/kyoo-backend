<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notification;
use Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notificationsData = Notification::whereUserId(Auth::id());
        $notifications = $notificationsData->orderBy('created_at', 'desc')->get();
        $notificationsData->update(['is_read' => true]);
        return response()->json([
            'success' => true,
            'message' => 'get notification success',
            'data' => $notifications
        ]);
    }
}

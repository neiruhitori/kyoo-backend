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
        $notifications = $notificationsData->get();
        $notificationsData->update(['is_read' => true]);
        return response()->json([
            'success' => true,
            'message' => 'get notification success',
            'data' => $notifications
        ]);
    }
}

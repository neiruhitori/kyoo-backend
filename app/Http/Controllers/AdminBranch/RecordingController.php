<?php

namespace App\Http\Controllers\AdminBranch;

use Auth;
use App\Http\Controllers\Controller;
use App\Models\Audio;
use Illuminate\Http\Request;

class RecordingController extends Controller
{
    public function index()
    {
        return view('adminBranch.service-quality.recordings');
    }

    public function getRecordings(Request $request)
    {
        $messageFileName = Audio::where('branch_id', Auth::user()->branch_id)
            ->orderByDesc('created_at')
            ->limit(100);
        
        if ($request->created_at) {
            $messageFileName->whereDate('created_at', $request->created_at);
        }

        return response()->json(['message_filenames' => $messageFileName->get()]);
    }
}

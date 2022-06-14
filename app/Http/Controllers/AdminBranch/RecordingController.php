<?php

namespace App\Http\Controllers\AdminBranch;

use Auth;
use App\Http\Controllers\Controller;
use App\Models\Audio;

class RecordingController extends Controller
{
    public function index()
    {
        return view('adminBranch.service-quality.recordings');
    }

    public function getRecordings()
    {
        $messageFileName = Audio::where('branch_id', Auth::user()->branch_id)
            ->orderByDesc('created_at')
            ->get();
        return response()->json(['message_filenames' => $messageFileName]);
    }
}

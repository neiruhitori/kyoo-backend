<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Audio;
use Illuminate\Support\Facades\Storage;

class VoiceRecordingController extends Controller
{
    public function index()
    {
        $messageFileName = Audio::orderByDesc('created_at')->get();
        return response()->json(['message_filenames' => $messageFileName]);
    }

    public function store(Request $request)
    {
        $customerName = $request->input('customer_name');
        $duration = $request->input('duration');
        $message = $request->input('message');
        $uuid = Str::uuid();

        Storage::disk('public')->put($uuid, base64_decode($message));

        Audio::create([
            'customer_name' => $customerName,
            'filename' => $uuid,
            'file_size_in_bytes' => filesize(Storage::disk('public')->path($uuid)),
            'duration' => $duration
        ]);

        return response()->json("success",201);
    }
}

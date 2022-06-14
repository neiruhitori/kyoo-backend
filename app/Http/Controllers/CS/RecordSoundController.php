<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Audio;
use Illuminate\Support\Facades\Storage;
use App\Events\RecordingCreated;
use Auth;
use Carbon\Carbon;

class RecordSoundController extends Controller
{
    public function index()
    {
        $workstation_id = Auth::user()->WorkstationVct->workstation_id;

        $recordings = Audio::where([
            'branch_id' => Auth::user()->branch_id,
            'workstation_id' => $workstation_id
        ])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($recording) {
                $recording->recorded_at = Carbon::parse($recording->created_at)
                    ->locale('id')
                    ->isoFormat('DD MMMM YYYY HH:mm:ss');
                
                return $recording;
            });

        return view('cs.record-sound', [
            'recordings' => $recordings,
            'workstation_id' => $workstation_id
        ]);
    }

    public function store(Request $request)
    {
        $customerName = $request->input('customer_name');
        $duration = $request->input('duration');
        $message = $request->input('message');
        $uuid = Str::uuid();

        Storage::disk('public')->put($uuid, base64_decode($message));

        $audio = Audio::create([
            'customer_name' => $customerName,
            'filename' => $uuid,
            'file_size_in_bytes' => filesize(Storage::disk('public')->path($uuid)),
            'duration' => $duration,
            'branch_id' => $request->branch_id,
            'workstation_id' => $request->workstation_id
        ]);

        event(new RecordingCreated($audio));

        return response()->json("success",201);
    }
}

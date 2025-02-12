<?php

namespace App\Http\Controllers\CS;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\AudioRecordingRepositoryInterface;
use Illuminate\Support\Carbon;
use App\Events\RecordingCreated;

class VoiceRecorderController extends Controller
{
    private AudioRecordingRepositoryInterface $audioRecordingRepo;

    public function __construct(AudioRecordingRepositoryInterface $audioRecordingRepo)
    {
        $this->audioRecordingRepo = $audioRecordingRepo;
    }

    public function index()
    {
        $recordings = $this->audioRecordingRepo->getRecentRecordings([
            'branch_id' => Auth::user()->branch_id,
            'workstation_id' => Auth::user()->WorkstationVct->workstation_id
        ]);

        // Error handling
        if (isset($recordings['error'])) {
            throw new \Exception($recordings['error']['message']);
        }

        // Transform response
        $recordings = $recordings->map(function ($recording) {
            $this->transformResponse($recording);

            return $recording;
        });

        $data = [
            'recordings' => $recordings
        ];

        return view('cs.voiceRecorder', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'branch_id' => 'required',
            'workstation_id' => 'required',
            'vct_id' => 'required'
        ]);
        
        $audio = $this->audioRecordingRepo
            ->recordAudio($request->all(), $request->bearerToken());

        // Error handling
        if (isset($audio['error'])) {
            return response()->json($audio, $audio['error']['code']);
        }

        // Transform response
        $this->transformResponse($audio);

        // Broadcast event
        event(new RecordingCreated($audio));

        return response()->json($audio, 201);
    }

    private function transformResponse(&$recording) {
        $recording['formatted'] = [
            'created_at' => Carbon::parse($recording['created_at'])
                    ->locale(app()->getLocale())
                    ->isoFormat('DD MMMM YYYY HH:mm:ss'),
            'duration' => $this->formatDuration($recording['duration'])
        ];
    }

    private function formatDuration($duration)
    {
        $formattedDuration = '';

        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        $seconds = $duration % 3600 % 60;

        if ($hours > 0) {
            $formattedDuration .= $hours . " " . __('Hour') ;
        }

        if ($minutes > 0) {
            $formattedDuration .= ($hours > 0 ? ' ' : '') . $minutes . " " . __('Minutes') ;
        }

        if ($seconds > 0) {
            $formattedDuration .= ($minutes > 0 ? ' ' : '') . $seconds . " " . __('Seconds') ;
        }

        return $formattedDuration;
    }
}

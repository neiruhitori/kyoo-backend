<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\AudioRecordingRepositoryInterface;

class AudioRecordingController extends Controller
{
    private $audioRecordingRepo;

    public function __construct(AudioRecordingRepositoryInterface $audioRecordingRepo)
    {
        $this->audioRecordingRepo = $audioRecordingRepo;
    }

    public function index()
    {
        return view('adminBranch.serviceQuality.audioRecording', [
            'storageUrl' => env('KYOO_AUDIO_URL')
        ]);
    }

    public function getAll(Request $request)
    {
        try {
            $recordings = $this->audioRecordingRepo->getRecordings($request->all());

            if (isset($recordings['error'])) {
                return response()->json($recordings, $recordings['error']['code']);
            }

            return response()->json($recordings, 200);
        } catch (Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

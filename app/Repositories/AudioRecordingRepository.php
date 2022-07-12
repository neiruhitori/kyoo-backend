<?php

namespace App\Repositories;

use App\Interfaces\AudioRecordingRepositoryInterface;
use App\BranchType;
use Illuminate\Support\Facades\Http;

class AudioRecordingRepository implements AudioRecordingRepositoryInterface
{
    private $audioRecordingUrl;

    public function __construct()
    {
        $this->audioRecordingUrl = env('AUDIO_RECORDING_URL') . '/api';
    }

    public function getRecordings($params = [])
    {
        $recordings = Http::get($this->audioRecordingUrl . '/recordings', $params)
            ->collect();

        return $recordings;
    }

    public function getRecentRecordings($params = [])
    {
        $recentRecordings = Http::get($this->audioRecordingUrl . '/recordings/recent', $params)
            ->collect();
        
        return $recentRecordings;
    }

    public function recordAudio($audio)
    {
        $recording = Http::post($this->audioRecordingUrl . '/recordings', $audio)
            ->collect();

        return $recording;
    }
}
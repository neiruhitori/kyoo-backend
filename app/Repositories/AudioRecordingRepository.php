<?php

namespace App\Repositories;

use App\Interfaces\AudioRecordingRepositoryInterface;
use Illuminate\Support\Facades\Http;

class AudioRecordingRepository implements AudioRecordingRepositoryInterface
{
    private $audioRecordingUrl;

    public function __construct()
    {
        $this->audioRecordingUrl = config('app.kyoo_audio_url') . '/api';
    }

    public function getRecordings($params = [])
    {
        $recordings = Http::withHeaders([
            'Accept' => 'application/json'
        ])
            ->get($this->audioRecordingUrl . '/recordings', $params)
            ->collect();

        return $recordings;
    }

    public function getRecentRecordings($params = [])
    {
        $recentRecordings = Http::withHeaders([
            'Accept' => 'application/json'
        ])
            ->get($this->audioRecordingUrl . '/recordings/recent', $params)
            ->collect();
        
        return $recentRecordings;
    }

    public function recordAudio($audio, $token = null)
    {
        $recording = Http::withHeaders([
            'Accept' => 'application/json'
        ])
            ->post($this->audioRecordingUrl . '/recordings', $audio)
            ->collect();

        return $recording;
    }
}
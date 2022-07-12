<?php

namespace App\Interfaces;

interface AudioRecordingRepositoryInterface 
{
    public function getRecordings($params);
    public function getRecentRecordings($params);
    public function recordAudio($audio);
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use FFMpeg;
use Storage;
use App\DirectQueue;

class QueueCallerController extends Controller
{
    public function call(DirectQueue $directQueue)
    {
        $timeRequest = time();
        $audio_files = ['audio/vo/nomor_antrian.mp3'];

        // Queue number audio
        for ($i = 0; $i < strlen($directQueue->queue_no); $i++) {
            array_push($audio_files, 'audio/vo/' . $directQueue->queue_no[$i] . '.mp3');
        }

        array_push($audio_files, 'audio/vo/dicounter.mp3');

        // Counter number audio
        if ($directQueue->Workstation) {
            $counter_id = preg_replace('/\D/', '',$directQueue->Workstation->label);

            for ($i = 0; $i < strlen($counter_id); $i++) {
                array_push($audio_files, 'audio/vo/' . $counter_id[$i] . '.mp3');
            }
        }

        $fileName = 'audio/vo/mixed_sound_'.$directQueue->branch_id.'_'.$directQueue->queue_no.'_'.$timeRequest.'.mp3';
        FFMpeg::open($audio_files)
            ->export()
            ->inFormat(new FFMpeg\Format\Audio\Mp3)
            ->concatWithTranscoding($hasVideo = false, $hasAudio = true)
            ->save($fileName);

        $mixed_sound = base64_encode(Storage::get($fileName));
        Storage::delete($fileName);

        return response()->json([
            'audio' => [
                'mime' => 'audio/wav',
                'data' => 'data:audio/wav;base64,' . $mixed_sound
            ]
        ]);
    }
}

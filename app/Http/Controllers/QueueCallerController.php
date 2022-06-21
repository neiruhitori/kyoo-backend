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
        $audio_files = ['audio/vo/nomor-antrian.wav'];

        // Queue number audio
        for ($i = 0; $i < strlen($directQueue->queue_no); $i++) {
            array_push($audio_files, 'audio/vo/' . $directQueue->queue_no[$i] . '.wav');
        }

        array_push($audio_files, 'audio/vo/mohon-ke-counter.wav');

        // Counter number audio
        if ($directQueue->Workstation) {
            $counter_id = substr($directQueue->Workstation->label, 8);

            for ($i = 0; $i < strlen($counter_id); $i++) {
                array_push($audio_files, 'audio/vo/' . $counter_id[$i] . '.wav');
            }
        }

        FFMpeg::open($audio_files)
            ->export()
            ->inFormat(new FFMpeg\Format\Audio\Wav)
            ->concatWithTranscoding($hasVideo = false, $hasAudio = true)
            ->save('audio/vo/mixed-sound.wav');
        
        $mixed_sound = base64_encode(Storage::get('audio/vo/mixed-sound.wav'));
        Storage::delete('audio/vo/mixed-sound.wav');

        return response()->json([
            'audio' => [
                'mime' => 'audio/wav',
                'data' => 'data:audio/wav;base64,' . $mixed_sound
            ]
        ]);
    }
}

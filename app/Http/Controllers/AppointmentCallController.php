<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Appointment;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Audio\Wav;
use Illuminate\Support\Facades\Storage;

class AppointmentCallController extends Controller
{
    public function call($appointment_id)
    {
        $appointment = Appointment::with('Workstation')
            ->where('id', $appointment_id)
            ->first();

        $audioFiles = ['audio/vo/nomor-antrian.wav'];

        $appointmentNumber = strval($appointment->number);

        // Queue number audio
        for ($i = 0; $i < strlen($appointmentNumber); $i++) {
            $fileName = $appointmentNumber[$i] . '.wav';
            $filePath = '/audio/vo/' . $fileName;

            if (file_exists(storage_path('app/') . $filePath)) {
                array_push($audioFiles, $filePath);
            }
        }

        array_push($audioFiles, 'audio/vo/mohon-ke-counter.wav');

        // Counter number audio
        if ($appointment->Workstation) {
            $counterId = substr($appointment->Workstation->label, 8);

            for ($i = 0; $i < strlen($counterId); $i++) {
                array_push($audioFiles, 'audio/vo/' . $counterId[$i] . '.wav');
            }
        }

        FFMpeg::open($audioFiles)
            ->export()
            ->inFormat(new Wav)
            ->concatWithTranscoding($hasVideo = false, $hasAudio = true)
            ->save('audio/vo/mixed-sound.wav');
        
        $mixedSound = base64_encode(Storage::get('audio/vo/mixed-sound.wav'));
        Storage::delete('audio/vo/mixed-sound.wav');

        return response()->json([
            'audio' => [
                'mime' => 'audio/wav',
                'data' => 'data:audio/wav;base64,' . $mixedSound
            ]
        ]);
    }
}

@extends('layouts.app')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/daisyui@2.15.2/dist/full.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div>
    <button id="save" disabled hidden>Save</button>
    <div class="w-[1050px] mx-auto mt-[86px]">
        <div class="w-[510px] h-[99px] bg-[#0D61A1] rounded-[12px] text-white text-center mx-auto pt-[18px]">
            <input type="text" id="customer_name" class="bg-[#0D61A1] text-bold text-[20px] border-b-[1px] border-white outline-none text-center mb-[8px] pb-[5px]">
            <p class="text-[rgba(255,255,255,0.52)] text-semibold text-[14px]">Nama Customer</p>
        </div>
        <div class="shadow-[0px_4px_22px_rgba(0,0,0,0.1)] w-[510px] h-[330px] mx-auto mt-[27px] text-center mt-[24px] pt-[24px] relative">
            <p class="text-[46px] h-[63px] text-[rgba(0,0,0,0.32)] font-bold leading-[64px]"><span id="seconds">00</span>:<span id="tens" class="text-[#000000]">00</span></p>
            <div class="w-[151px] h-[152px] mx-auto border-[1px] border-solid border-[rgba(0,0,0,0.13)] mt-[24px] rounded-[100px] flex items-center justify-center">
                <button class=" w-[76px] h-[76px] button-audio flex items-center justify-center" id="record">
                    <img src="{{asset('img/audio-conversations/microphone-inactive.png')}}" class="w-[24px] h-[24px]" id="microphone" alt="">
                </button>
                <button class=" w-[76px] h-[76px] button-audio flex items-center justify-center hidden" id="stop">
                    <img src="{{asset('img/audio-conversations/microphone-inactive.png')}}" class="w-[24px] h-[24px]" id="microphone" alt="">
                </button>
            </div>
            <button class="w-[50px] h-[50px] bg-[#FAF8F8] border-[1px] border-solid border-[rgba(0,0,0,0.13)] absolute right-[100px] bottom-[118px] rounded-[25px] flex items-center justify-center hidden" id="pauseresume">
                <img src="{{asset('img/audio-conversations/pause.png')}}" class="w-[24px] h-[24px]" id="imgPlay" alt="">
            </button>
            <div class="flex justify-center items-center mt-[24px]">
                <h3 class="font-semibold text-[14px] text-[rgba(0,0,0,0.24)]">Klik tombol</h3>
                <img src="{{asset('img/audio-conversations/microphone-inactive.png')}}" class="w-[20px] h-[20px] mx-[12.9px]" id="recordPreview" alt="">
                <h3 class="font-semibold text-[14px] text-[rgba(0,0,0,0.24)] flex wording-audio">untuk mulai</h3>
            </div>
        </div>
    </div>
</div>

<style>
    audio {
      display: block;
      margin: 5px;
    }

    .button-audio {
      background: #FF3B6A;
      box-shadow: 6px 15px 45px rgba(0, 0, 0, 0.46), inset 3px 5px 19px rgba(255, 255, 255, 0.47);
      box-shadow: 0 0 0 0 rgba(0, 0, 0, 1);
      margin: 10px;
      height: 70px;
      width: 70px;
      transform: scale(1);
      transition: 0.5s;
      /* animation: pulse-black 3s infinite; */
    }

    #record {
      border-radius: 60px;
    }

    #stop {
      border-radius: 20px;
      transition: 2s all ease-in;
    }

    #pause {
      opacity: 0;
      transition: 0.5s all;
    }

    @keyframes pulse-black {
      0% {
        /* transform: scale(0.95); */
        box-shadow: 0 0 0 0 rgba(0, 0, 0, 0.4);
      }

      70% {
        /* transform: scale(1.3); */
        box-shadow: 0 0 0 35px rgba(0, 0, 0, 0);
      }

      100% {
        /* transform: scale(0.95); */
        box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
      }
    }

    tr {
      height: 53px;
    }

    tr:nth-child(odd) {
      background-color: #FBFBFB;
    }

    tr:nth-child(even) {
      background-color: #FDFEFF;
    }

    tr>td:nth-child(6) {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    tr>td:nth-child(1) {
      width: 200px;
    }

</style>
<script>
    const recordAudio = () =>
        new Promise(async resolve => {
            var constraints = {
                audio: true,
                video: false
            };
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            var options = {
                audioBitsPerSecond: 128000,
                videoBitsPerSecond: 2500000,
                mimeType: 'audio/mp4'
            }
            const mediaRecorder = new MediaRecorder(stream);
            let audioChunks = [];

            mediaRecorder.addEventListener('dataavailable', event => {
                audioChunks.push(event.data);
            });

            const start = () => {
                audioChunks = [];
                mediaRecorder.start();
                console.log("state: ");
                console.log(mediaRecorder.state);
            };

            const stop = () =>
                new Promise(resolve => {
                    mediaRecorder.addEventListener('stop', () => {
                        const audioBlob = new Blob(audioChunks, {
                            type: 'audio/mpeg'
                        });
                        const audioUrl = URL.createObjectURL(audioBlob);
                        const audio = new Audio(audioUrl);
                        const play = () => audio.play();
                        resolve({
                            audioChunks,
                            audioBlob,
                            audioUrl,
                            play
                        });
                    });

                    mediaRecorder.stop();
                });

            const pause = () => {
                mediaRecorder.pause();
            };
            const resume = () => {
                mediaRecorder.resume();
            };

            resolve({
                start,
                stop,
                pause,
                resume
            });
        });

    const sleep = time => new Promise(resolve => setTimeout(resolve, time));

    const recordButton = document.querySelector('#record');
    const pauseresumeButton = document.querySelector('#pauseresume');
    const stopButton = document.querySelector('#stop');
    const pulse = document.querySelector('#pulse');
    const microphone = document.querySelector('#microphone');
    const wordingAudio = document.querySelector('.wording-audio');
    const imgPlay = document.querySelector('#imgPlay');
    const saveButton = document.querySelector('#save');
    const savedAudioMessagesContainer = document.querySelector('#myTable > tbody');

    pauseresumeButton.setAttribute('disabled', true);
    let recorder;
    let audio;
    let state;

    pauseresumeButton.addEventListener('click', async () => {

        recordButton.setAttribute('disabled', false);
        if (state == 'active') {
            await recorder.pause()
            state = 'inactive'
            document.getElementById('imgPlay').src = "{{asset('img/audio-conversations/play.png')}}"
            clearInterval(Interval);
            status = 'pause';
            stopButton.style.animation = "";
            imgPlay.src = "{{asset('img/audio-conversations/play.png')}}"
        } else {
            await recorder.resume()
            state = 'active'
            document.getElementById('imgPlay').src = "{{asset('img/audio-conversations/pause.png')}}"
            Interval = setInterval(startTimer, 10);
            stopButton.style.animation = "pulse-black 2.5s infinite";
            imgPlay.src = "{{asset('img/audio-conversations/pause.png')}}"
        }

    });

    var appendTens = document.getElementById("tens")
    var appendSeconds = document.getElementById("seconds")
    var status = 'play';
    var seconds = 00;
    var tens = 00;
    var Interval;

    function startTimer() {
        tens++;

        if (tens <= 9) {
            appendTens.innerHTML = "0" + tens;
        }

        if (tens > 9) {
            appendTens.innerHTML = tens;

        }

        if (tens > 99) {
            console.log("seconds");
            seconds++;
            appendSeconds.innerHTML = "0" + seconds;
            tens = 0;
            appendTens.innerHTML = "0" + 0;
        }

        if (seconds > 9) {
            appendSeconds.innerHTML = seconds;
        }

    }



    recordButton.addEventListener('click', async () => {
        // if (microphoneAccess == false) {
        // Swal.fire('Mohon izinkan akses mikrofon')

        // } else {
        // microphone.src = "./assets/microphone-active.png"
        recordButton.classList.add("hidden")
        stopButton.classList.remove("hidden")

        // recordPreview.src = "./assets/microphone-active.png"
        wordingAudio.innerHTML = 'untuk berhenti'

        stopButton.style.animation = "pulse-black 2.5s infinite";
        stopButton.style.borderRadius = "20px";

        pauseresumeButton.classList.remove("hidden");

        setTimeout(() => {
            pauseresumeButton.style.opacity = 1
        }, 100)



        recordButton.setAttribute('disabled', true);
        stopButton.removeAttribute('disabled');
        saveButton.setAttribute('disabled', true);
        pauseresumeButton.removeAttribute('disabled');

        if (!recorder) {
            recorder = await recordAudio();
        }
        await recorder.start();
        state = "active";

        clearInterval(Interval);
        Interval = setInterval(startTimer, 10);
        // }
    });

    stopButton.addEventListener('click', async () => {
        let duration = document.getElementById('seconds').innerHTML;

        //button action
        pauseresumeButton.classList.add('hidden');
        recordButton.classList.remove("hidden")
        stopButton.classList.add("hidden")

        //pulse animation
        stopButton.style.animation = "";

        //information play/pause
        wordingAudio.innerHTML = 'untuk mulai'
        // recordPreview.src = "./assets/microphone-slash.png"

        // stop timer
        clearInterval(Interval);
        tens = "00";
        seconds = "00";
        appendTens.innerHTML = tens;
        appendSeconds.innerHTML = seconds;


        // document.getElementById('pauseresume').innerHTML = '-'
        pauseresumeButton.setAttribute('disabled', true);

        recordButton.removeAttribute('disabled');
        stopButton.setAttribute('disabled', true);
        saveButton.removeAttribute('disabled');
        // document.getElementById('pauseresume').innerHTML = 'pause'

        audio = await recorder.stop();

        //langsung save
        const reader = new FileReader();
        reader.readAsDataURL(audio.audioBlob);
        reader.onload = () => {
            const base64AudioMessage = reader.result.split(',')[1];
            let customer_name = document.getElementById('customer_name').value;
            fetch('/cs/record-sound', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '{{ csrf_token() }}'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    customer_name: customer_name,
                    duration: duration,
                    message: base64AudioMessage,
                    branch_id: '{{ Auth::user()->branch_id }}',
                    workstation_id: '{{ Auth::user()->WorkstationVct->workstation_id }}'
                })
            }).then(res => {
                if (res.status === 201) {
                    return populateAudioMessages();
                }
                console.log('Invalid status saving audio message: ' + res.status);
            });
        };
    });

    saveButton.addEventListener('click', () => {
        const reader = new FileReader();
        reader.readAsDataURL(audio.audioBlob);
        reader.onload = () => {
            const base64AudioMessage = reader.result.split(',')[1];

            fetch('/cs/record-sound', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: base64AudioMessage
                })
            }).then(res => {
                if (res.status === 201) {
                    return populateAudioMessages();
                }
                console.log('Invalid status saving audio message: ' + res.status);
            });
        };
    });
</script>
@endsection
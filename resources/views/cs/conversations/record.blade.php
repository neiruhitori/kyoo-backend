@extends('layouts.app')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/daisyui@2.15.2/dist/full.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div>
    <button id="play" disabled hidden>Play</button>
    <button id="save" disabled hidden>Save</button>

    <div id="saved-audio-messages" class="flex flex-col h-screen pl-[1rem] w-[1050px] mx-auto pt-[30px]">
        <div class="container mx-auto flex flex-wrap">
            <div class="basis-full mb-[31px]">
                <h3 class="text-[18px] leading-[25px] font-bold">
                    Transaksi Rekaman
                </h3>
            </div>
            <div class="basis-full">
                <table id="myTable" border="1" class="w-full rounded-tl-[12px]">
                    <thead class="h-[53px] text-white text-center">
                        <tr>
                            <th class="bg-[#0E5EA0] rounded-tl-[12px]">Nama File</th>
                            <th class="bg-[#0E5EA0]">Nama Pelanggan</th>
                            <th class="bg-[#0E5EA0]">Tanggal</th>
                            <th class="bg-[#0E5EA0]">Ukuran File</th>
                            <th class="bg-[#0E5EA0]">Durasi</th>
                            <th class="bg-[#0E5EA0] rounded-tr-[12px]">Audio</th>
                            <!-- <th>Duration</th> -->
                        </tr>
                    </thead>
                    <tbody id="tbody_id" class="text-center">

                    </tbody>
                </table>
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
            height: 20px;
            width: 20px;
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
      
        const savedAudioMessagesContainer = document.querySelector('#myTable > tbody');

        const populateAudioMessages = () => {
            return fetch('/cs/conversations/messages').then(res => {
                if (res.status === 200) {
                    return res.json().then(json => {
                        json.message_filenames.forEach(data => {
                            console.log(data)
                            let audioElement = document.querySelector(`[data-audio-filename="${data.filename}"]`);
                            if (!audioElement) {
                                audioElement = document.createElement('audio');
                                audioElement.src = `/storage/${data.filename}`;
                                audioElement.setAttribute("controlslist", "nodownload");
                                audioElement.setAttribute('data-audio-filename', data.filename);
                                audioElement.setAttribute('controls', true);

                                var d = new Date(data.created_at);

                                var options = {
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric'
                                };

                                // console.log(d.toLocaleDateString('en-ZA', options));
                                let newDate = d.toLocaleDateString('id-ID', options);

                                //add data to table
                                let table_body = document.getElementById("tbody_id");
                                let row = table_body.insertRow(-1);

                                // Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:
                                let cell1 = row.insertCell(0);
                                let cell2 = row.insertCell(1);
                                let cell3 = row.insertCell(2);
                                let cell4 = row.insertCell(3);
                                let cell5 = row.insertCell(4);
                                let cell6 = row.insertCell(5).append(audioElement);

                                // Add some text to the new cells:
                                cell1.innerHTML = data.filename;
                                cell2.innerHTML = data.customer_name;
                                cell3.innerHTML = newDate;
                                cell4.innerHTML = (data.file_size_in_bytes / (1024 * 1024)).toFixed(3) + ' MB';
                                let minute = 0;
                                let second = 0;

                                if (data.duration > 60) {
                                    minute = Math.floor(data.duration / 60)
                                    second = data.duration % 60
                                    cell5.innerHTML = minute + ":" + second;
                                } else {
                                    console.log(data.duration)
                                    cell5.innerHTML = data.duration + ' seconds';
                                }

                                // savedAudioMessagesContainer.append(audioElement);
                            }
                        });
                    });
                }
                console.log('Invalid status getting messages: ' + res.status);
            });
        };
        populateAudioMessages();
    </script>
@endsection
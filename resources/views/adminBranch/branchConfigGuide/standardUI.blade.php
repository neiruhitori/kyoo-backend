@extends('layouts.app')

@push('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    .number-list {
        display: flex;
    }

    .number-list .number-indicator {
        font-weight: bold;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        line-height: 0px;
        height: 1.25rem;
        width: 1.25rem;
        margin: 4px;
        background-color: black;
        color: white;
        border-radius: 999999999px;
    }
        @font-face {
        font-family: 'Inter', sans-serif;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                {{-- <div class="card-header py-3">
                </div> --}}
                <div class="card-body">
                    <h4 class="font-weight-bold" style="color: #103C7C">
                        {{ __('How to Configure Queue') }}
                    </h4>
                    <p class="mb-5">Berikut merupakan tahapan yang anda dapat lakukan sebagai admin cabang agar Anda dan Pelanggan anda dapat menggunakan platform Antrian KYOO</p>
                    <div class="row d-flex" style="justify-content: space-around">
                        {{-- Left Blue Card --}}
                        <div class="col-md-6 p-4 rounded" style="background-color: #33A0FF4D;">
                            <h5 style="color: #103C7C" class="mb-3 font-weight-bold">Konfigurasi utama antrian</h5>

                            <div class="timeline">
                                <div class="timeline-step">
                                    <div class="timeline-icon mt-3">
                                      <i class="fas fa-building text-white"></i>
                                    </div>
                                    <div class="timeline-card">
                                        <h6 class="font-weight-bold text-dark">{{ __('Step :no', ['no' => 1]) }}</h6>
                                        <p class="mb-0">Anda perlu mengubah informasi tentang cabang anda</p>
                                        <p>di <a href="{{ route('admin-branch.branch-information.profile') }}" target="__blank" class="text-decoration-none">{{ __('Branch Profile') }}</a></p>
                                    </div>
                                </div>

                                <div class="timeline-step">
                                    <div class="timeline-icon mt-3">
                                        <i class="fas fa-map-marker-alt text-white"></i>
                                    </div>
                                    <div class="timeline-card">
                                        <h6 class="font-weight-bold text-dark">{{ __('Step :no', ['no' => 2]) }}</h6>
                                        <p class="mb-0">Anda perlu mengubah lokasi kantor cabang anda</p>
                                        <p>di <a href="{{ route('admin-branch.branch-information.location') }}" target="__blank"  class="text-decoration-none">{{ __('Branch Location') }}</a></p>
                                    </div>
                                </div>

                                <div class="timeline-step">
                                    <div class="timeline-icon mt-3">
                                        <i class="fas fa-hand-holding-heart text-white"></i>
                                    </div>
                                    <div class="timeline-card">
                                        <h6 class="font-weight-bold text-dark">{{ __('Step :no', ['no' => 3]) }}</h6>
                                        <p class="mb-0">Anda perlu mengubah jenis dan nama layanan anda kepada pelanggan</p>
                                        <p>di <a href="{{ route('admin-branch.branch-configuration.department.index') }}" target="__blank" class="text-decoration-none">{{ __('Service Type') }}</a></p>
                                    </div>
                                </div>

                                <div class="timeline-step">
                                    <div class="timeline-icon mt-3">
                                        <i class="fas fa-calendar text-white"></i>
                                    </div>
                                    <div class="timeline-card">
                                        <h6 class="font-weight-bold text-dark">{{ __('Step :no', ['no' => 4]) }}</h6>
                                        <p class="mb-0">Anda perlu mengubah jadwal Buka dan Tutup Layanan anda</p>
                                        <p>di <a href="{{ route('admin-branch.branch-configuration.schedule.index') }}" target="__blank" class="text-decoration-none">{{ __('Service Schedule') }}</a></p>
                                    </div>
                                </div>

                                <div class="timeline-step">
                                    <div class="timeline-icon mt-3">
                                        <i class="fas fa-user-lock text-white"></i>
                                    </div>
                                    <div class="timeline-card">
                                        <h6 class="font-weight-bold text-dark">{{ __('Step :no', ['no' => 5]) }}</h6>
                                        <p class="mb-0">Anda perlu mengubah akses Petugas Layanan</p>
                                        <p>di <a href="{{ route('admin-branch.branch-configuration.user.index') }}" target="__blank" class="text-decoration-none">{{ __('Staff') }}</a></p>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        {{-- Right Blue Card --}}
                        <div class="col-md-6 p-4 rounded" style="background-color: #33A0FF4D;">
                            <h5 style="color: #103C7C" class="font-weight-bold">{{ __('Optional Configuration') }}</h5>
                            <h6 class="mb-5">{{ __('You can also change') }}</h6>
                                <div class="timeline-step">
                                    <div class="timeline-icon mt-3">
                                        <i class="fas fa-building text-white"></i>
                                    </div>
                                    <div class="timeline-card">
                                        <p>Nama departemen dari menu <a href="{{ route('admin-branch.branch-configuration.department.index') }}" target="__blank" class="text-decoration-none">{{ __('Department') }}</a></p>
                                    </div>
                                </div>

                                <div class="timeline-step">
                                    <div class="timeline-icon mt-3">
                                        <i class="fas fa-hand-holding-heart text-white"></i>
                                    </div>
                                    <div class="timeline-card">
                                        <p>Nama Layanan dari menu <a href="{{ route('admin-branch.branch-configuration.department.index') }}" target="__blank" class="text-decoration-none">{{ __('Department') }}</a></p>
                                    </div>
                                </div>

                                <div class="timeline-step">
                                    <div class="timeline-icon mt-3">
                                        <i class="fas fa-passport text-white"></i>
                                    </div>
                                    <div class="timeline-card">
                                        <p>Nama Meja dari menu  <a href="{{ route('admin-branch.branch-configuration.workstation.index') }}" target="__blank" class="text-decoration-none">{{ __('Workstation') }}</a></p>
                                    </div>
                                </div>

                                <div class="timeline-step">
                                    <div class="timeline-icon mt-3">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                    <div class="timeline-card">
                                        <p>Nama user petugas layanan dari menu  <a href="{{ route('admin-branch.branch-configuration.user.index') }}" target="__blank" class="text-decoration-none">{{ __('Staff') }}</a></p>
                                    </div>
                                </div>
                                
                        </div>

                    </div>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="font-weight-bold" style="color: #103C7C">{{ __('Customer Guide') }}</h5>
                    <div class="accordion mb-3" id="accordionParent1">
                        <div style="border-radius: 0.5rem; overflow: hidden;">

                            <div id="headingOne" style="background-color: #33A0FF4D;">
                                <button 
                                    class="btn btn-block text-left d-flex align-items-center accordion-toggle-custom collapsed" 
                                    type="button"
                                    data-toggle="collapse" 
                                    data-target="#accordion1" 
                                    aria-expanded="false" 
                                    aria-controls="accordion1"
                                    style="color: #103C7C; gap: 1rem; outline: none; box-shadow: none; padding: 1rem;"
                                >
                                    <div class="d-flex justify-content-center align-items-center" 
                                        style="width: 60px; height: 60px; background-color: #103C7C; border-radius: 10%;">
                                        <img src="{{ asset('img/panduan-1.png') }}" style="width: 80%; object-fit: contain;" alt="">
                                    </div>
                                    <div class="pt-1">
                                        <span class="badge badge-pill badge-primary">Cara 1</span>
                                        <h6 class="mb-0 font-weight-bold">{{ __('Take a queue through the Web Portal') }}</h6>
                                    </div>
                                </button>
                            </div>

                            <div 
                                id="accordion1" 
                                class="collapse" 
                                aria-labelledby="headingOne" 
                                data-parent="#accordionParent1" 
                                style="background-color: #33A0FF4D;"
                            >
                                <div style="padding: 0rem 1rem 1rem 5.75rem;">
                                    <p class="mb-0">
                                        Pelanggan dapat mengakses alamat web dibawah ini untuk mengambil antrian onsite/appointment/booking layanan Anda.
                                    </p>
                                    <p>
                                       Alamat web dibawah ini bisa ditempatkan di website, instagram, sosial media dan channel informasi institusi anda lainnya.
                                    </p>
                                    <div class="d-flex align-items-center">
                                        <div class="p-1 rounded d-flex align-items-center justify-content-between" style="background-color: #fff; width: 40%; max-width: 100%; gap: 0.5rem;">
                                            <a href="{{ $short_url }}" id="branchURL" target="_blank">{{ $short_url }}</a>
                                            <button type="button" class="btn btn-primary" onclick="copyText()">
                                                <i class="fas fa-copy text-white"></i>
                                            </button>
                                        </div>
                                        <p class="mb-0 ml-3 d-none" id="hasCopied">Copied!</p>
                                    </div>
                            </div>
                        </div>
                        </div>
                    </div>


                    <div class="accordion mb-3" id="accordionParent2">
                        <div style="border-radius: 0.5rem; overflow: hidden;">

                            <div id="headingOne" style="background-color: #33A0FF4D;">
                                <button 
                                    class="btn btn-block text-left d-flex align-items-center accordion-toggle-custom collapsed" 
                                    type="button"
                                    data-toggle="collapse" 
                                    data-target="#accordion2" 
                                    aria-expanded="false" 
                                    aria-controls="accordion2"
                                    style="color: #103C7C; gap: 1rem; outline: none; box-shadow: none; padding: 1rem;"
                                >
                                    <div class="d-flex justify-content-center align-items-center" 
                                        style="width: 60px; height: 60px; background-color: #103C7C; border-radius: 10%;">
                                        <img src="{{ asset('img/panduan-2.png') }}" style="width: 80%; object-fit: contain;" alt="">
                                    </div>
                                    <div class="pt-1">
                                        <span class="badge badge-pill badge-success">Cara 2</span>
                                        <h6 class="mb-0 font-weight-bold"> {{ __('Take a queue via QR Code') }}</h6>
                                    </div>
                                </button>
                            </div>

                            <div 
                                id="accordion2" 
                                class="collapse" 
                                aria-labelledby="headingOne" 
                                data-parent="#accordionParent2" 
                                style="background-color: #33A0FF4D;"
                            >
                                <div style="padding: 0rem 1rem 1rem 5.75rem;">
                                    <p class="mb-0">
                                        Pelanggan dapat melakukan scan QR-Code cabang, anda dapat men-cetak dan menempelkan QR-Code ini di pintu masuk Cabang 
                                    </p>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div style="max-width: 595px; margin: 0 auto;" class="mb-4">
                                            <canvas width="595" height="842" class="shadow mt-3" id="qr-poster">
                                                Canvas not supported
                                            </canvas>
                                            <div class="mt-3 d-flex justify-content-center" style="gap: 1rem">
                                                <a href="#" class="btn btn-primary px-4 py-2" style="background-color: #103C7C"onclick="downloadPoster()">
                                                    <span class="fas fa-download mr-2"></span>
                                                    Download
                                                </a>

                                                <a href="#" class="btn btn-primary px-3 py-2" style="background-color: #103C7C" onclick="printPoster()">
                                                    <span class="fas fa-print mr-2"></span>
                                                    Print
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        </div>
                    </div>

                    <div class="accordion mb-3" id="accordionParent3">
                        <div style="border-radius: 0.5rem; overflow: hidden;">

                            <div id="headingOne" style="background-color: #33A0FF4D;">
                                <button 
                                    class="btn btn-block text-left d-flex align-items-center accordion-toggle-custom collapsed" 
                                    type="button"
                                    data-toggle="collapse" 
                                    data-target="#accordion3" 
                                    aria-expanded="false" 
                                    aria-controls="accordion3"
                                    style="color: #103C7C; gap: 1rem; outline: none; box-shadow: none; padding: 1rem;"
                                >
                                    <div class="d-flex justify-content-center align-items-center" 
                                        style="width: 60px; height: 60px; background-color: #103C7C; border-radius: 10%;">
                                        <img src="{{ asset('img/panduan-3.png') }}" style="width: 55%; object-fit: contain;" alt="">
                                    </div>
                                    <div class="pt-1">
                                        <span class="badge badge-pill badge-warning">Cara 3</span>
                                        <h6 class="mb-0 font-weight-bold">{{ __('Take a queue through the KYOO App') }}</h6>
                                    </div>
                                </button>
                            </div>

                            <div 
                                id="accordion3" 
                                class="collapse" 
                                aria-labelledby="headingOne" 
                                data-parent="#accordionParent3" 
                                style="background-color: #33A0FF4D;"
                            >
                                <div style="padding: 0rem 1rem 1rem 5.75rem;">
                                    <p class="mb-0">
                                        Pelanggan dapat mendownload Aplikasi KYOO untuk mengambil antrian
                                    </p>
                                     <div style="width: 145px;height: auto;">
                                        <a href="https://play.google.com/store/apps/details?id=com.kyoo.kyoo_app" target="__blank">
                                            <img src="/img/playstore.png" height="70px" />
                                        </a>
                                    </div>
                            </div>
                        </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
.col-md-6{
    max-width: 49%
}
.timeline {
  position: relative;
}

.timeline::after {
  content: '';
  position: absolute;
  top: 20px;
  bottom: 90px;
  left: 16px;
  width: 2px;
  background: repeating-linear-gradient(
    to bottom,
    #103C7C,
    #103C7C 5px,
    transparent 5px,
    transparent 10px
  );
  z-index: 0;
}

.timeline-step {
  position: relative;
  display: flex;
  align-items: flex-start;
  margin-bottom: 1rem;
  z-index: 1;
}

.timeline-icon {
  width: 35px;
  height: 35px;
  min-width: 35px;
  background-color: #103C7C;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  z-index: 2;
  position: relative;
}

.timeline-card {
  background: #fff;
  padding: 1rem 1.5rem;
  border-radius: 0.5rem;
  margin-left: 1rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  width: 100%;
}
.accordion-toggle-custom.collapsed {
    padding-bottom: 1rem !important;
}

.accordion-toggle-custom:not(.collapsed) {
    padding-bottom: 0rem !important;
}

.accordion-toggle-custom {
    transition: padding 0.3s ease;
}

.accordion-toggle-custom::after {
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    transition: transform 0.2s ease;
    margin-left: auto;
}

.accordion-toggle-custom[aria-expanded="false"]::after {
    content: "\f107";
}

.accordion-toggle-custom[aria-expanded="true"]::after {
    content: "\f106";
}
</style>

<script>
      function copyText() {
        var copyText = document.getElementById("branchURL").textContent;
        var hasCopied = document.getElementById("hasCopied");

        navigator.clipboard.writeText(copyText).then(function () {
           if(hasCopied.classList.contains('d-none')){
            hasCopied.classList.remove('d-none');
           }
           setTimeout(function () {
            hasCopied.classList.add("d-none");
        }, 5000);
        }).catch(function (err) {
            console.error("Failed to copy: ", err);
        });
    }

    window.addEventListener('load', function () {
        const canvas = document.getElementById('qr-poster');
        const ctx = canvas.getContext('2d');
        const currentLocale = "{{ app()->getLocale() }}" || 'en'; //ambil locale
        // console.log(currentLocale);
        

        if (canvas.getContext) draw(canvas)
    })

    function draw(canvas, scale = 1, done) {
        canvas.width = 595 * scale;
        canvas.height = 842 * scale;

        const ctx = canvas.getContext('2d')

        ctx.scale(scale, scale)
        ctx.fillStyle = '#FAFCFF'
        ctx.fillRect(0, 0, canvas.width, canvas.height)

        // Poster title
        ctx.fillStyle = '#114077'
        ctx.font = `700 ${36}px 'Inter', sans-serif`
        ctx.textBaseline = 'top'
        ctx.fillText("{{ __('Scan Here') }}", 197, 42)

        // Poster description
        ctx.font = `normal ${16}px 'Inter', sans-serif`
        // Teks pertama
        let text1 = "{{ __('To take a queue or check-in,') }}";
        let text1Width = ctx.measureText(text1).width;
        let x1 = (canvas.width - text1Width) / 2; // Posisi x di tengah
        ctx.fillText(text1, x1, 94);

        // Teks kedua
        let text2 = "{{ __('scan the QR Code below') }}";
        let text2Width = ctx.measureText(text2).width;
        let x2 = (canvas.width - text2Width) / 2; // Posisi x di tengah
        ctx.fillText(text2, x2, 118);

        ctx.resetTransform()

        const branchName = `{{ Auth::user()->Branch->name }}`
        const address = `{{ Auth::user()->Branch->address }}`
        const regionName = `{{ Auth::user()->Branch->Regency->name }}, {{ Auth::user()->Branch->Regency->province->name }}`

        ctx.fillStyle = '#114077'
        
        ctx.font = `700 ${24 * scale}px 'Inter', sans-serif`
        // Branch name
        const branchNameWidth = ctx.measureText(branchName).width
        ctx.fillText(branchName, (canvas.width / 2) - (branchNameWidth / 2), 419 * scale)

        ctx.font = `normal ${16 * scale}px 'Inter', sans-serif`

        // Address
        const addressWidth = ctx.measureText(address).width
        ctx.fillText(address, (canvas.width / 2) - (addressWidth / 2), 459 * scale)

        // Region Name
        const regionNameWidth = ctx.measureText(regionName).width
        ctx.fillText(regionName, (canvas.width / 2) - (regionNameWidth / 2), 483 * scale)

        ctx.scale(scale, scale)

        // QR frame
        ctx.fillStyle = '#2087FF'
        const path = new Path2D('M18 4.90909H9.81818C7.10697 4.90909 4.90909 7.10696 4.90909 9.81818V18H0V9.81818C0 4.39574 4.39576 0 9.81818 0H18V4.90909Z')

        ctx.translate(193, 172)
        ctx.fill(path)
        ctx.resetTransform()

        ctx.scale(scale, scale)
        ctx.translate(193, 381)
        ctx.rotate(-90 * Math.PI / 180)
        ctx.fill(path)
        ctx.resetTransform()

        ctx.scale(scale, scale)
        ctx.translate(401, 172)
        ctx.rotate(90 * Math.PI / 180)
        ctx.fill(path)
        ctx.resetTransform()

        ctx.scale(scale, scale)
        ctx.translate(401, 381)
        ctx.rotate(180 * Math.PI / 180)
        ctx.fill(path)
        ctx.resetTransform()

        // QR image
        ctx.scale(scale, scale)

        imgEl = new Image()
        imgEl.src = 'data:image/svg+xml;base64,' + btoa(`{!! $qr !!}`)
        imgEl.onload = function () {
            ctx.drawImage(imgEl, 207, 186, 180, 180)

            // Footer Logo
            logoEl = new Image()
            logoEl.src = `{{ asset('img/logo-color.svg') }}`
            logoEl.onload = function () {
                ctx.drawImage(logoEl, 247, 778, 100, 32)
                if (done) done()
            }
        }
        
        // Footer background
        ctx.fillStyle = '#CCE4FF'
        ctx.fillRect(0, 542, 595, 301)

        // Yellow Background
        ctx.fillStyle = '#F6D378'
        ctx.fillRect(0, 542, 595, 58)

        // Footer title
        ctx.fillStyle = '#114077'
        ctx.font = '700 18px \'Inter\', sans-serif'

        ctx.fillText("{{ __('HOW TO SCAN QR CODE') }}", 199, 562)

        // Bullet
        ctx.beginPath();
        ctx.arc(54, 644, 12, 0, 2 * Math.PI)
        ctx.arc(238, 644, 12, 0, 2 * Math.PI)
        ctx.arc(423, 644, 12, 0, 2 * Math.PI)
        ctx.closePath();
        ctx.fill()

        // Bullet number
        ctx.fillStyle = '#FAFCFF'
        ctx.font = '16px \'Inter\', sans-serif'

        ctx.fillText('1', 50, 637)
        ctx.fillText('2', 233, 637)
        ctx.fillText('3', 418, 637)  

        // Guide text
        ctx.fillStyle = '#114077'
        ctx.font = '16px \'Inter\', sans-serif'

        ctx.fillText("{{ __('Use your phone') }}", 42, 676)
        ctx.fillText("{{ __('camera app') }}", 42, 700)
        ctx.fillText("{{ __('or visit') }}", 42, 724)

        ctx.font = '700 16px \'Inter\', sans-serif'
        ctx.fillText('scan.kyoo.id', 42, 748)

        ctx.font = '16px \'Inter\', sans-serif'

        ctx.fillText("{{ __('Point your camera at') }}", 226, 676)
        ctx.fillText("{{ __('the QR Code above') }}", 226, 700)

        ctx.fillText("{{ __('Select the queue') }}", 411, 676)
        ctx.fillText("{{ __('service type') }}", 411, 700)
    }

    function downloadPoster() {
        const canvas = document.createElement('canvas')
        const ctx = canvas.getContext('2d')

        draw(canvas, 2, function () {
            const imgUrl = canvas.toDataURL('image/png');

            const a = document.createElement('a')
            a.href = imgUrl
            a.download = 'Kyoo QR Poster - {{ Auth::user()->Branch->name }}'    
            a.click()
        })
    }

    function printPoster() {
        const canvas = document.createElement('canvas')
        const ctx = canvas.getContext('2d')

        draw(canvas, 2, function () {
            const posterImg = new Image()
            posterImg.src = canvas.toDataURL('image/png')

            const win = window.open('', 'PosterPrintWindow')

            win.document.head.innerHTML = `<title>
                    Kyoo QR Poster - {{ Auth::user()->Branch->name }}    
                </title>
                
                <style>
                    @page {
                        margin: 0;
                        padding: 0;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }

                    body {
                        margin: 0;
                        padding: 0;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }

                    img {
                        height: 100vh;
                        margin: 0 auto;
                        display: block;
                    }
                </style>`
            win.document.body.append(posterImg)

            
            posterImg.onload = function () {
                win.print()
            }
        })
    }
</script>
@endsection
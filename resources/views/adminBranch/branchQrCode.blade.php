@extends('layouts.app')

@push('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    @font-face {
        font-family: 'Inter', sans-serif;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div style="max-width: 595px; margin: 0 auto;" class="mb-4">
                <div class="mb-3">
                    <a href="#" class="btn btn-secondary" onclick="downloadPoster()">
                        <span class="fas fa-download mr-2"></span>
                        Download
                    </a>

                    <a href="#" class="btn btn-secondary" onclick="printPoster()">
                        <span class="fas fa-print mr-2"></span>
                        Print
                    </a>
                </div>

                <canvas width="595" height="842" class="shadow" id="qr-poster">
                    Canvas not supported
                </canvas>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    window.addEventListener('load', function () {
        const canvas = document.getElementById('qr-poster');
        const ctx = canvas.getContext('2d');

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
        ctx.fillText('Scan Disini', 197, 42)

        // Poster description
        ctx.font = `normal ${16}px 'Inter', sans-serif`
        ctx.fillText('Untuk mengambil antrian ataupun check-in,', 134, 94)
        ctx.fillText('scan Kode QR di-bawah', 209, 118)

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

        ctx.fillText('CARA SCAN KODE QR', 199, 562)

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

        ctx.fillText('Gunakan aplikasi', 42, 676)
        ctx.fillText('kamera handphone', 42, 700)
        ctx.fillText('Anda atau akses', 42, 724)

        ctx.font = '700 16px \'Inter\', sans-serif'
        ctx.fillText('kyoo.id/scan', 42, 748)

        ctx.font = '16px \'Inter\', sans-serif'

        ctx.fillText('Arahkan kamera ke', 226, 676)
        ctx.fillText('kode QR diatas', 226, 700)

        ctx.fillText('Pilih jenis layanan', 411, 676)
        ctx.fillText('antrian', 411, 700)
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
@endpush
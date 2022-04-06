@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Panduan Pelanggan - Ambil antrian melalui Web Portal</h6>
            </div>

            <div class="card-body">
                <p class="mb-2">
                    Pelanggan dapat mengakses alamat web dibawah ini untuk mengambil antrian onsite/appointement/booking layanan Anda. Alamat web dibawah ini bisa tempatkan di website, Instagram, sosial media, dan channel informasi Institusi Anda lainnya.
                </p>
    
                <div style="display: flex; justify-content: center; align-items: center;">
                    <a href="{{ url('kyooTicket/' . $queue_type . '/' . Auth::user()->branch_id. '/services') }}" target="__blank" id="kyooTicketUrl">{{ url('kyooTicket/' . $queue_type . '/' . Auth::user()->branch_id. '/services') }}</a>
                    <button class="btn btn-secondary ml-2" onclick="copyToClipboard()">Copy URL</button>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Panduan Pelanggan - Ambil antrian melalui QR code</h6>
            </div>

            <div class="card-body">
                <p class="mb-2">
                    Pelanggan dapat melakukan scan QR-code Cabang, Anda dapat men-cetak dan menempatkan QR-code ini di pintu masuk Cabang.
                </p>
                <div>
                    <a href="{{ route('adminBranch.branchQrCode') }}" class="btn btn-warning">Lihat QR-code Cabang</a>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Panduan Pelanggan - Ambil antrian melalui aplikasi KYOO</h6>
            </div>

            <div class="card-body">
                <p class="mb-2">
                    Pelanggan dapat mendownload aplikasi KYOO untuk mengambil antrian.
                </p>
                <div>
                    <a href="https://play.google.com/store/apps/details?id=com.kyoo.android" target="__blank">
                        <img src="/img/playstore.png" height="70px" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard() {
        const urlEl = document.getElementById('kyooTicketUrl')
        navigator.clipboard.writeText(urlEl.href)
    }
</script>
@endsection
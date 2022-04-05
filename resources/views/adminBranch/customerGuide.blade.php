@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Panduan Pelanggan</h6>
            </div>

            <div class="card-body">
                <p class="mb-4">
                    Pelanggan dapat mengakses alamat web dibawah ini untuk mengambil antrian onsite/appointement/booking layanan Anda. Alamat web dibawah ini bisa tempatkan di website, Instagram, sosial media, dan channel informasi Institusi Anda lainnya.
                </p>
    
                <div style="display: flex; justify-content: center; align-items: center;">
                    <a href="{{ url('kyooTicket/appointment/' . Auth::user()->branch_id. '/services') }}" target="__blank" id="kyooTicketUrl">{{ url('kyooTicket/appointment/' . Auth::user()->branch_id. '/services') }}</a>
                    <button class="btn btn-secondary ml-2" onclick="copyToClipboard()">Copy URL</button>
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
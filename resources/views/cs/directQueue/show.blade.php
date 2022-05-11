@extends('layouts.app')

@section('content')
<div style="max-width: 730px;">
    <div class="card shadow mb-4">
        <div class="ticket-row">
            <div style="flex: 1 1 0%; padding: 2rem;">
                <div class="ticket-row" style="margin-bottom: 2.5rem;">
                    <div class="ticket-column">
                        <span class="queue-no-label">Nomor Antrian</span>
                        <h4 class="queue-no">{{ $direct_queue['queue_no'] }}</h4>
                    </div>

                    <div class="ticket-column" style="font-size: 1.25rem;">
                        @if ($direct_queue['status'] == 'tidak hadir')
                            <span class="badge badge-danger" style="text-transform: capitalize;">{{ $direct_queue['status'] }}</span>
                        @elseif ($direct_queue['status'] == 'layanan selesai')
                            <span class="badge badge-success" style="text-transform: capitalize;">{{ $direct_queue['status'] }}</span>
                        @else
                            <span class="badge badge-warning" style="text-transform: capitalize;">{{ $direct_queue['status'] }}</span>
                        @endif
                    </div>
                </div>

                <div class="ticket-row" style="margin-bottom: 2rem;">
                    <div class="ticket-column">
                        <span class="ticket-label">Tanggal</span>
                        <p class="ticket-value">{{ $direct_queue['date'] }}</p>
                    </div>

                    <div class="ticket-column">
                        <span class="ticket-label">Layanan</span>
                        <p class="ticket-value">{{ $direct_queue['service_name'] }}</p>
                    </div>
                </div>

                <div class="ticket-row">
                    <div class="ticket-column">
                        <span class="ticket-label">Kode Unik</span>
                        <p class="ticket-value" style="text-transform: uppercase;">{{ $direct_queue['booking_code'] }}</p>
                    </div>

                    <div class="ticket-column">
                        <span class="ticket-label">Meja</span>
                        <p class="ticket-value">{{ $direct_queue['workstation_label'] }}</p>
                    </div>
                </div>
            </div>

            <div class="ticket-column qr-section">
                <p class="text-center" style="color: #114077;">Scan disini untuk melihat status antrian</p>

                <div class="qr-container">
                    {!! $qr_code !!}
                </div>
            </div>
        </div>
    </div>

    <div class="text-right">
        <a href="{{ route('cs.directQueue.monitor') }}" class="btn btn-warning">Kembali</a>
    </div>
</div>
@endsection

@push('css')
<style>
    .queue-no-label {
        text-transform: uppercase;
        font-size: 1rem;
    }

    .queue-no {
        font-size: 4rem;
        letter-spacing: 4;
        font-weight: bold;
        margin-bottom: 0;
        color: #114077;
    }

    .ticket-row {
        display: flex;
        gap: 1rem;
    }

    .ticket-column {
        flex: 1 1 0%;
    }

    .ticket-label {
        font-size: .875rem;
        text-transform: uppercase;
    }

    .ticket-value {
        font-size: 1.125rem;
        font-weight: bold;
        margin-bottom: 0;
        color: #000000;
    }

    .qr-section {
        display: flex;
        flex-direction: column;
        justify-content: center;
        background-color: #CCE4FF;
        max-width: 260px;
        padding: 2rem;
    }

    .qr-container {
        display: flex;
        justify-content: center;
        width: max-content;
        margin: 0 auto;
        position: relative;
        background-color: white;
        border-radius: 16px;
        overflow: hidden;
    }
</style>
@endpush
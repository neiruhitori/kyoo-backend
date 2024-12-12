@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-7">

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monitoring Departemen</h6>
                </div>

                <div class="card-body">
                  

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-4" id="table">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle">Nomor Antrian</th>
                                    <th rowspan="2" class="align-middle text-right">Kode Unik</th>
                                    <th rowspan="2" class="align-middle text-right">Ambil Antrian</th>
                                    <th rowspan="2" class="align-middle text-right">Antrian Dipanggil</th>
                                    <th rowspan="2" class="align-middle text-right">Mulai Layanan</th>
                                    <th rowspan="2" class="align-middle text-right">Layanan Selesai</th>
                                    <th colspan="3" class="text-center">Durasi Antrian</th>
                                    <th rowspan="2" class="align-middle text-right">Meja</th>
                                    <th rowspan="2" class="align-middle text-right">Layanan</th>
                                    <th rowspan="2" class="align-middle text-right">Layanan Tujuan Transfer</th>
                                    <th rowspan="2" class="align-middle text-right">Petugas Layanan</th>
                                    <th rowspan="2" class="align-middle text-right">Status</th>
                                </tr>
    
                                <tr>
                                    {{-- Waktu Tunggu Child Header --}}
                                    <th class="text-center">Durasi Tunggu</th>
                                    <th class="text-center">Durasi Layanan (Panggil)</th>
                                    <th class="text-center">Durasi Layanan</th>
                                </tr>
                            </thead>
    
                            <tbody>
                                <tr>
                                    <td>{{ $data->queue_no }}</td>
                                    <td>{{ $data->booking_code }}</td>
                                    <td>{{ date('Y M d H:i:s', strtotime($data->created_at)) }}</td>
                                    <td>
                                        @if($data->call_time)
                                            {{ date('Y M d H:i:s', strtotime($data->call_time)) }}
                                        @elseif($data->called_at)
                                        {{ date('Y M d H:i:s', strtotime($data->called_at)) }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data->called_at)
                                            {{ date('Y M d H:i:s', strtotime($data->called_at)) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data->done_at)
                                            {{ date('Y M d H:i:s', strtotime($data->done_at)) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data->called_at)
                                        @php
                                            $waktuCreate = \Carbon\Carbon::parse($data->created_at);
                                            $cek = $data->call_time ? $data->call_time : $data->called_at;
                                            $waktuPanggil = \Carbon\Carbon::parse($cek);
                                            $durasiTunggu = $waktuPanggil ? $waktuPanggil->diff($waktuCreate) : null;
                                            $formattedDurasiTunggu = $durasiTunggu 
                                                ? sprintf('%02d:%02d:%02d', $durasiTunggu->h, $durasiTunggu->i, $durasiTunggu->s) 
                                                : '-';
                                        @endphp
                                            {{ $formattedDurasiTunggu }} 
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data->call_time)
                                        @php
                                            $waktuPanggil = \Carbon\Carbon::parse($data->call_time) ?: '';
                                            $waktuSelesai = \Carbon\Carbon::parse($data->done_at);
                                            $durasiLayanan = $waktuPanggil ? $waktuPanggil->diff($waktuSelesai) : null;
                                            $formattedDurasiLayanan = $durasiLayanan 
                                            ? sprintf('%02d:%02d:%02d', $durasiLayanan->h, $durasiLayanan->i, $durasiLayanan->s) 
                                            : '-';
                                        @endphp
                                        {{ $formattedDurasiLayanan }}
                                        @elseif(!$data->call_time && $data->called_at)
                                            @php
                                                $waktuPanggil = \Carbon\Carbon::parse($data->called_at);
                                                $waktuSelesai = \Carbon\Carbon::parse($data->done_at) ?: '';
                                                $durasiLayanan = $waktuPanggil ? $waktuPanggil->diff($waktuSelesai) : null;
                                                $formattedDurasiLayanan = $durasiLayanan 
                                                ? sprintf('%02d:%02d:%02d', $durasiLayanan->h, $durasiLayanan->i, $durasiLayanan->s) 
                                                : '-';
                                            @endphp
                                            {{ $formattedDurasiLayanan }}
                                    @else
                                        -
                                    @endif
                                    </td>
                                    <td>
                                    @if ($data->called_at)
                                        @php
                                        $waktuPanggil = \Carbon\Carbon::parse($data->called_at);
                                        $waktuSelesai = \Carbon\Carbon::parse($data->done_at) ?: '';
                                        $durasiLayanan = $waktuSelesai ? $waktuSelesai->diff($waktuPanggil) : null;
                                        
                                        if ($durasiLayanan && $durasiLayanan->h === 0 && $durasiLayanan->i === 0 && $durasiLayanan->s === 0) {
                                            // Jika durasinya adalah 0, gunakan call_time
                                            if ($data->call_time) {
                                                $waktuPanggil = \Carbon\Carbon::parse($data->call_time) ?: '';
                                                $waktuSelesai = \Carbon\Carbon::parse($data->done_at);
                                                $durasiLayanan = $waktuPanggil ? $waktuPanggil->diff($waktuSelesai) : null;
                                            }
                                        }

                                        $formattedDurasiLayanan = $durasiLayanan 
                                            ? sprintf('%02d:%02d:%02d', $durasiLayanan->h, $durasiLayanan->i, $durasiLayanan->s) 
                                            : '-';
                                    @endphp
                                    {{ $formattedDurasiLayanan }}
                                    @else
                                        -
                                    @endif
                                    </td>
                                    <td>{{ $data->Workstation ? $data->Workstation->name : '-' }}
                                    </td>
                                    <td>{{ $data->Service->name }}</td>
                                    <td>
                                        @if ($data->NewService)
                                            {{ $data->NewService->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>{{  $data->Vct ? $data->Vct->name : '-' }}
                                    </td>
                                    <td>{{ __(ucwords($data->status)) }}</td>
                                </tr>
                                {{-- <tr>
                                    <td colspan="14" class="text-center">Data tidak ditemukan.</td>
                                </tr> --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

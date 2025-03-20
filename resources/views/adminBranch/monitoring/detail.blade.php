@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-7">

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monitoring {{ __('Department') }}</h6>
                </div>

                <div class="card-body">
                  

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-4" id="table">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle">{{ __('Queue Number') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Booking Code') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Queue Taken') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Queue Called') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Start Service') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Finish Service') }}</th>
                                    <th colspan="3" class="text-center">{{ __('Queue Duration') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Workstation') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Service') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Service Transfer') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Virtual Counter') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Status') }}</th>
                                </tr>
    
                                <tr>
                                    {{-- Waktu Tunggu Child Header --}}
                                    <th class="text-center">{{ __('Waiting Duration') }} </th>
                                    <th class="text-center">{{ __('Service Duration (Call)') }} </th>
                                    <th class="text-center">{{ __('Service Duration') }} </th>
                                </tr>
                            </thead>
    
                            <tbody>
                                @forelse($data as $d)
                                <tr>
                                    <td>{{ $d->queue_no }}</td>
                                    <td>{{ $d->booking_code }}</td>
                                    <td>{{ date('Y M d H:i:s', strtotime($d->created_at)) }}</td>
                                    <td>
                                        @if($d->call_time)
                                            {{ date('Y M d H:i:s', strtotime($d->call_time)) }}
                                        @elseif($d->called_at)
                                        {{ date('Y M d H:i:s', strtotime($d->called_at)) }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($d->called_at)
                                            {{ date('Y M d H:i:s', strtotime($d->called_at)) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($d->done_at)
                                            {{ date('Y M d H:i:s', strtotime($d->done_at)) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($d->called_at)
                                        @php
                                            $waktuCreate = \Carbon\Carbon::parse($d->created_at);
                                            $cek = $d->call_time ? $d->call_time : $d->called_at;
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
                                        @if ($d->call_time)
                                        @php
                                            $waktuPanggil = \Carbon\Carbon::parse($d->call_time) ?: '';
                                            $waktuSelesai = \Carbon\Carbon::parse($d->done_at);
                                            $durasiLayanan = $waktuPanggil ? $waktuPanggil->diff($waktuSelesai) : null;
                                            $formattedDurasiLayanan = $durasiLayanan 
                                            ? sprintf('%02d:%02d:%02d', $durasiLayanan->h, $durasiLayanan->i, $durasiLayanan->s) 
                                            : '-';
                                        @endphp
                                        {{ $formattedDurasiLayanan }}
                                        @elseif(!$d->call_time && $d->called_at)
                                            @php
                                                $waktuPanggil = \Carbon\Carbon::parse($d->called_at);
                                                $waktuSelesai = \Carbon\Carbon::parse($d->done_at) ?: '';
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
                                    @if ($d->called_at)
                                        @php
                                        $waktuPanggil = \Carbon\Carbon::parse($d->called_at);
                                        $waktuSelesai = \Carbon\Carbon::parse($d->done_at) ?: '';
                                        $durasiLayanan = $waktuSelesai ? $waktuSelesai->diff($waktuPanggil) : null;
                                        
                                        if ($durasiLayanan && $durasiLayanan->h === 0 && $durasiLayanan->i === 0 && $durasiLayanan->s === 0) {
                                            // Jika durasinya adalah 0, gunakan call_time
                                            if ($d->call_time) {
                                                $waktuPanggil = \Carbon\Carbon::parse($d->call_time) ?: '';
                                                $waktuSelesai = \Carbon\Carbon::parse($d->done_at);
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
                                    <td>{{ $d->Workstation ? $d->Workstation->name : '-' }}
                                    </td>
                                    <td>{{ $d->Service->name }}</td>
                                    <td>
                                        @if ($d->NewService)
                                            {{ $d->NewService->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{  $d->Vct ? $d->Vct->name : '-' }}
                                    </td>
                                    <td>{{ __(ucwords($d->status)) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="14" class="text-center">{{ __('Data not Found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

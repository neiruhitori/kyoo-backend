@extends('layouts.appCS')

@section('content')
    <div id="app">
        <appointment-monitor-2
            :branch="{{ Auth::user()->Branch }}"
            :vct="{{ Auth::user() }}"
            :auth="{{ Auth::user() }}"
            :workstation="{{ Auth::user()->WorkstationVct->Workstation }}"
            :services="{{ $services }}"
            lang={{ app()->getLocale() }}
        />
    </div>
@endsection

@push('js')
    <script src="{{ mix('/js/app.js') }}"></script>
@endpush
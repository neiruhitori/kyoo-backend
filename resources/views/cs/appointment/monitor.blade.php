@extends('layouts.app')

@section('content')
    <div id="app">
        <appointment-monitor
            :branch="{{ Auth::user()->Branch }}"
            :vct="{{ Auth::user() }}"
            :workstation="{{ Auth::user()->WorkstationVct->Workstation }}"
            lang={{ app()->getLocale() }}
        />
    </div>
@endsection

@push('js')
    <script src="{{ mix('/js/app.js') }}"></script>
@endpush
@extends('layouts.app')

@section('content')
    <div id="app">
        <appointment-monitor
            :branch="{{ Auth::user()->Branch }}"
        />
    </div>
@endsection

@push('js')
    <script src="{{ mix('/js/app.js') }}"></script>
@endpush
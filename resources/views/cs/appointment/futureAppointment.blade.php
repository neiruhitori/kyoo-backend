@extends('layouts.app')

@section('content')
    <div id="app">
        <future-appointments-component
            :slots="{{ $slots }}"
            :schedules="{{ $schedules }}"
        />
    </div>
@endsection

@push('js')
    <script src="{{ mix('/js/app.js') }}"></script>
@endpush
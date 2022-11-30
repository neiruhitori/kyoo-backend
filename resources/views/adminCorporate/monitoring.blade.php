@extends('layouts.app')

@section('content')
    <div id="app">
        <centralized-monitoring-component
            :corporate="{{ $corporate }}"
        />
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ mix('css/app.css') }}">
@endpush

@push('js')
<script src="{{ mix('js/app.js') }}"></script>
@endpush
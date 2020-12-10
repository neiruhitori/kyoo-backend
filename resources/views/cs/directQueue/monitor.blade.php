@extends('layouts.app')
@section('content')
    <div id="app">
        <monitor-component />
    </div>
@endsection
@push('js')
    <script src="{{asset('js/app.js')}}"></script>
@endpush
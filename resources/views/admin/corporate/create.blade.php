@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ mix('css/app.css') }}">
@endpush

@section('content')
  <div id="app">
    <create-corporate-component />
  </div>
@endsection

@push('js')
  <script src="{{ mix('js/app.js') }}"></script>
@endpush
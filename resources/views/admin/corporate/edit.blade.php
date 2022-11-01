@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ mix('css/app.css') }}">
@endpush

@section('content')
  <div id="app">
    <edit-corporate-component :corporate="{{ $corporate }}" />
  </div>
@endsection

@push('js')
  <script src="{{ mix('js/app.js') }}"></script>
@endpush
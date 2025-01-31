@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ mix('css/app.css') }}">
@endpush

@section('content')
<div class="card mb-4 custom-info" data-open="open" role="alert">
  <div class="card-body">
      <div class="custom-info-head">
          <h6 class="font-weight-bold my-0">
              <span class="fas fa-info-circle text-primary mr-1"></span>
              Informasi
          </h6>

          <button class="custom-muted-btn font-weight-bold text-warning" data-toggle="alert">
              Tampilkan
          </button>
      </div>

      <div class="custom-info-body">
          <p>
              <ul style="padding-left: 2rem;">
                  <li style="margin-bottom: 0.25rem;">
                    Informasikan syarat dan ketentuan di portal anda ketika akan menggunakan 
                    antrian di kantor anda dan menggunakan support layanan Anda. 
                  </li>
          </p>
          <button class="btn btn-warning float-right" data-toggle="alert">Sembunyikan</button>
      </div>
  </div>
</div>

<div id="app">
  <terms-conditions-component 
    locale = "{{ app()->getLocale() }}"
  />
</div>
@endsection

@push('js')
  <script src="{{ mix('js/app.js') }}"></script>
@endpush
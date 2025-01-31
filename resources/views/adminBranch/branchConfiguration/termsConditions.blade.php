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
              {{ __('Information') }}
          </h6>

          <button class="custom-muted-btn font-weight-bold text-warning" data-toggle="alert">
            {{ __('Show') }}
          </button>
      </div>

      <div class="custom-info-body">
          <p>
              <ul style="padding-left: 2rem;">
                  <li style="margin-bottom: 0.25rem;">
                    {{ __('infobox.termsandcondition') }} 
                  </li>
          </p>
          <button class="btn btn-warning float-right" data-toggle="alert">{{ __('Hide') }}</button>
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
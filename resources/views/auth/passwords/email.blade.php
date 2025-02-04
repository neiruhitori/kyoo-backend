<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Kyoo Admin</title>
  <link rel="icon" href="{{ asset('img/favico.png') }}" type="image/icon type">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css"/>

  <!-- Inter Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet">

<!-- Bootstrap core JavaScript-->
<script type="text/javascript" src="{{asset('admin/vendor/jquery/jquery.min.js')}}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js" integrity="sha512-k6/Bkb8Fxf/c1Tkyl39yJwcOZ1P4cRrJu77p83zJjN2Z55prbFHxPs9vN7q3l3+tSMGPDdoH51AEU8Vgo1cgAA==" crossorigin="anonymous"></script>
<script src="{{asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
 
<!-- Template CSS -->
  <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>

<body style="display: flex; align-items: stretch;">
  <div class="page-container">
    <div class="content-container">
      <div class="page-header d-flex justify-content-between">
        <img src="{{ asset('img/logo-color.svg') }}" class="app-icon" />

        <div class="dropdown align-self-center lang">
          <button class="btn dropdown-toggle " type="button" data-toggle="dropdown">
            <span class="fi fi-{{ app()->getLocale() == 'en' ? 'gb' : 'id'}} fib border"></span>
             {{-- below is lang  --}}
            {{ strtoupper(app()->getLocale()) }}
          </button>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route('change.locale', 'en') }}">
              <span class="fi fi-gb fib border"></span> English
          </a>
          <a class="dropdown-item" href="{{ route('change.locale', 'id') }}">
              <span class="fi fi-id fib border"></span> Bahasa
          </a>
          </div>
        </div>
      </div>

      <div style="padding: 3rem 0 3rem 0;">
        <div style="margin-bottom: 1.5rem;">
          <h1 class="page-title" style="margin-bottom: 1rem;">Reset Password</h1>
          <p class="text-gray">{{ __("We'll help restore your account") }}</p>
        </div>

        @if (Session::get('error'))
        <div class="alert alert-danger">
          {{ Session::get('error') }}
        </div>
        @endif

        @if (Session::get('status'))
        <div class="alert alert-success">
          {{ Session::get('status') }}
        </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" style="margin-bottom: 1rem;">
          @csrf

          <div style="margin-bottom: 1.5rem;">
            <label class="font-weight-bold" for="email">Email</label>

            <div class="k-input">
              <div>
                <x-icon icon="letter" class="k-icon" />
              </div>

              <input type="email" name="email" id="email" placeholder="{{ __('Enter email') }}" value="{{ old('email') }}"
                required>
            </div>

            @error('email')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit" class="k-button">{{ __('Send Recovery Email') }}</button>
        </form>

        <p class="text-gray">
         {{ __('Back to') }} <a href="{{route('login')}}">Login</a>
        </p>
      </div>
    </div>
  </div>
  <div class="image-container">
    <div class="dropdown position-absolute" style="
    top: 55px;
    right: 135px;
    z-index: 4;
">
      <button class="btn dropdown-toggle " type="button" data-toggle="dropdown">
        <span class="fi fi-{{ app()->getLocale() == 'en' ? 'gb' : 'id'}} fib border"></span>
         {{-- below is lang  --}}
        {{ strtoupper(app()->getLocale()) }}
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="{{ route('change.locale', 'en') }}">
          <span class="fi fi-gb fib border"></span> English
      </a>
      <a class="dropdown-item" href="{{ route('change.locale', 'id') }}">
          <span class="fi fi-id fib border"></span> Bahasa
      </a>
      </div>
    </div>

    <img class="page-illustration" src="{{ asset('img/illustrations/reset-password.svg') }}">
  </div>
</body>

</html>
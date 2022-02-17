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

  <!-- Inter Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet">

  <!-- Template CSS -->
  <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
  <!-- Custom CSS -->
  <link href="{{ asset('css/authentication.css') }}" rel="stylesheet">
</head>

<body class="login-outer-container">
  <div class="px-4 login-container">
    <div class="login-content-container">
      <div class="login-header">
        <img src="{{ asset('img/logo-color.svg') }}" class="login-logo-app" />
      </div>

      <div class="login-headline">
        <h1 class="k-title">Login</h1>
        <p class="login-description">Selamat datang kembali di KYOO</p>
      </div>

      <form action="{{ route('login') }}" method="POST">
        @csrf

        @if (Session::get('error'))
        <div class="alert alert-danger" role="alert">
          {{ Session::get('error') }}
        </div>
        @endif

        <div class="k-form-group">
          <label for="email" class="k-label">Email</label>
          <div class="k-input">
            <x-icon icon="letter" class="k-icon" />
            <input type="text" name="email" id="email" placeholder="mail@website.com">
          </div>

          @error('email')
          <div class="text-danger mt-2">{{ $message }}</div>
          @enderror
        </div>

        <div class="k-form-group">
          <label for="password" class="k-label">Password</label>
          <div class="k-input">
            <x-icon icon="lock" class="k-icon" />
            <input type="password" name="password" id="password" placeholder="Min. 8 karakter">
          </div>

          @error('password')
          <div class="text-danger mt-2">{{ $message }}</div>
          @enderror
        </div>

        <div class="login-section mb-4">
          <div class="k-checkbox">
            <input type="checkbox" name="remember_me" id="remember_me">
            <label for="remember_me">Ingat saya</label>
          </div>

          <div>
            <a href="{{ route('password.request') }}">Lupa password?</a>
          </div>
        </div>

        <button type="submit" class="k-button">Login</button>
      </form>

      <p class="text-gray">
        Belum punya akun? <a href="{{route('register')}}">Layanan Anda di KYOO</a>
      </p>
    </div>
  </div>

  <div class="login-banner">
    <img class="login-banner-img" src="{{ asset('img/illustrations/in-line.svg') }}">
    <div class="circle circle1"></div>
    <div class="circle circle2"></div>
</body>

</html>
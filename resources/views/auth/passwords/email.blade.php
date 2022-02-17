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
        <h1 class="k-title">Reset Password</h1>
        <p class="login-description">Kami bantu pulihkan akunmu kembali</p>
      </div>

      <form action="{{ route('password.email') }}" method="POST">
        @csrf

        @if (Session::get('error'))
        <div class="alert alert-danger" role="alert">
          {{ Session::get('error') }}
        </div>
        @endif

        <div class="k-form-group mb-4">
          <label for="email" class="k-label">Email</label>
          <div class="k-input">
            <x-icon icon="letter" class="k-icon" />
            <input type="text" name="email" id="email" placeholder="Masukkan email">
          </div>

          @error('email')
          <div class="text-danger mt-2">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="k-button">Kirim Email Pemulihan</button>
      </form>

      <p class="text-gray">
        Kembali ke <a href="{{route('login')}}">Login</a>
      </p>
    </div>
  </div>

  <div class="login-banner">
    <img class="login-banner-img" src="{{ asset('img/illustrations/reset-password.svg') }}">
    <div class="circle circle1"></div>
    <div class="circle circle2"></div>
</body>

</html>
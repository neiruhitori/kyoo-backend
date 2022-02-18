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
  <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

  <script src="{{asset('admin/vendor/jquery/jquery.min.js')}}"></script>
</head>

<body style="display: flex; align-items: stretch;">
  <div class="page-container">
    <div class="content-container">
      <div class="page-header">
        <img src="{{ asset('img/logo-color.svg') }}" class="app-icon" />
      </div>

      <div style="padding: 3rem 0 3rem 0;">
        <div style="margin-bottom: 1.5rem;">
          <h1 class="page-title" style="margin-bottom: 1rem;">Login</h1>
          <p class="text-gray">Selamat datang kembali di KYOO</p>
        </div>

        @if (Session::get('error'))
        <div class="alert alert-danger" role="alert">
          {{ Session::get('error') }}
        </div>
        @endif

        <form action="{{ route('login') }}" method="POST" style="margin-bottom: 1rem;">
          @csrf

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="email">Email</label>

            <div class="k-input">
              <x-icon icon="letter" class="k-icon" style="margin-right: 0.5rem;" />
              <input type="email" name="email" id="email" placeholder="mail@website.com" value="{{ old('email') }}"
                required>
            </div>

            @error('email')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="password">Password</label>

            <div class="k-input">
              <x-icon icon="lock" class="k-icon" style="margin-right: 0.5rem;" />
              <input type="password" name="password" id="password" placeholder="Min. 8 Character" required>
              <x-icon icon="eyeClosed" class="k-icon k-password-icon" style="margin-left: 0.5rem;" />
            </div>

            @error('password')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
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
          Belum punya akun? <a href="{{ route('register') }}">Daftarkan layanan Anda di KYOO</a>
        </p>
      </div>
    </div>
  </div>
  <div class="image-container">
    <img class="page-illustration" src="{{ asset('img/illustrations/in-line.svg') }}">
  </div>

  <script>
    $(document).ready(function () {
      $('input[type="password"] + .k-password-icon').click(function () {
        const input = $(this).siblings('input')

        input.attr('type') === 'password'
          ? input.attr('type', 'text')
          : input.attr('type', 'password')
      })
    })
  </script>
</body>

</html>
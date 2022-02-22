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
</head>

<body style="display: flex; align-items: stretch;">
  <div class="page-container">
    <div class="content-container">
      <div class="page-header">
        <img src="{{ asset('img/logo-color.svg') }}" class="app-icon" />
      </div>

      <div style="padding: 3rem 0 3rem 0;">
        <div style="margin-bottom: 1.5rem;">
          <h1 class="page-title" style="margin-bottom: 1rem;">Reset Password</h1>
          <p class="text-gray">Kami bantu pulihkan akunmu kembali</p>
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

              <input type="email" name="email" id="email" placeholder="Masukkan email" value="{{ old('email') }}"
                required>
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
  </div>
  <div class="image-container">
    <img class="page-illustration" src="{{ asset('img/illustrations/reset-password.svg') }}">
  </div>
</body>

</html>
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

  <!-- Roboto Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">

  <!-- Template CSS -->
  <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">

  <style>
    body {
      font-family: 'Roboto', sans-serif;
      color: black;
    }

    .k-title {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 1rem;
    }

    .k-input {
      border: 1px solid gray;
      padding: 0.75rem;
      border-radius: 10px;
    }

    .k-input input {
      background: transparent;
      outline: none;
      width: 100%;
      border: none;
    }

    .k-checkbox {
      display: flex;
      align-items: center;
    }

    .k-checkbox input {
      width: 1.25rem;
      height: 1.25rem;
      margin-right: 0.75rem;
    }

    .k-checkbox label {
      margin: 0;
    }

    .k-form-group {
      margin-bottom: 1rem;
    }

    .k-label {
      font-weight: bold;
      margin-bottom: 0.5rem;
    }

    .k-login-section {
      display: flex;
      justify-content: between;
      margin-bottom: 1rem;
    }

    .k-button {
      padding: 0.75rem;
      font-weight: bold;
      color: #fff;
      background-color: #000;
      width: 100%;
      text-align: center;
      border-radius: 10px;
      border: none;
    }

    form {
      margin-bottom: 1rem;
    }

    .login-description {
      font-size: 1.125rem;
    }

    .login-container {
      min-height: 100vh;
      position: relative;
    }

    .login-section {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1rem;
    }

    .login-logo-app {
      width: 12rem;
      height: auto;
    }

    .login-header {
      position: absolute;
      top: 0;
      left: 0;
      padding: 1.125rem;
    }

    .login-headline {
      margin-bottom: 1.5rem;
    }

    .login-content-container {
      display: flex;
      flex-direction: column;
      justify-content: center;
      height: 100vh;
    }
  </style>
</head>

<body>
  <div class="px-4 login-container">
    <div class="login-header">
      <img src="{{ asset('img/logo-color.svg') }}" class="login-logo-app" />
    </div>

    <div class="login-content-container">
      <div class="login-headline">
        <h1 class="k-title">Login</h1>
        <p class="login-description">Selamat datang kembali di KYOOO</p>
      </div>

      <form action="{{ route('login') }}">
        @csrf

        <div class="k-form-group">
          <label for="email" class="k-label">Email</label>
          <div class="k-input">
            <!-- <img src="{{ asset('img/icons/letter.svg') }}" class="input-icon"> -->
            <input type="text" name="email" id="email" placeholder="mail@website.com">
          </div>
        </div>

        <div class="k-form-group">
          <label for="password" class="k-label">Password</label>
          <div class="k-input">
            <!-- <img src="{{ asset('img/icons/letter.svg') }}" class="input-icon"> -->
            <input type="password" name="password" id="password" placeholder="Min. 8 karakter">
          </div>
        </div>

        <div class="login-section">
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

      <p>
        Belum punya akun? <a href="">Layanan Anda di KYOO</a>
      </p>
    </div>
  </div>
</body>

</html>
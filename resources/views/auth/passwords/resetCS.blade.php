<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>{{ __('Kyoo Admin') }}</title>

  <!-- Custom fonts for this template-->
  <link href="{{asset('admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{asset('admin/css/sb-admin-2.min.css')}}" rel="stylesheet">
  <style>
    .bg-gradient-primary {
      background: linear-gradient(121.16deg, #189DCD 0.95%, #0A5194 97.59%);
    }
    .img-welcome {
      text-align: center;
      margin-top: 80px;
    }
    .img-welcome img {
      width: 500px;
    }
    @media only screen and (max-width: 768px) {
      /* For mobile phones: */
      .img-welcome img {
        width: 200px;
      }
    }
  </style>
</head>

<body class="bg-gradient-primary">

  <div class="container pt-5">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-6 img-welcome">
                <img src="{{ asset('/img/welcome.svg') }}" alt="welcome img">
              </div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <img src="{{asset('img/logo-color.svg')}}" alt="" class="img-fluid mb-4">
                  </div>
                  @include('layouts.alert')
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                  <form method="POST" action="{{ route('adminBranch.user.password.update', $token) }}" class="user">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="token" value="{{ $token }}">
                    <h4 class="text-center mb-3">{{ __('Reset Password VCT') }}</h4>
                      <small>
                          {{ __('Rules') }}:
                          <ul>
                              <li>{{ __('must be at least 8 characters in length') }}</li>
                              <li>{{ __('must contain at least one lowercase letter') }}</li>
                              <li>{{ __('must contain at least one uppercase letter') }}</li>
                              <li>{{ __('must contain at least one digit') }}</li>
                          </ul>
                      </small>
                    <div class="form-group">
                      <input type="text" name="username" class="form-control form-control-user @error('username') is-invalid @enderror" value="{{ $user->username ?? old('username') }}" id="exampleInputUsername" aria-describedby="usernameHelp" placeholder="{{ __('Enter Username') }}" required disabled>
                        @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                      <input type="password" name="password" class="form-control form-control-user @error('password') is-invalid @enderror" placeholder="{{ __('Type your new password') }}" required>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                      <input type="password" name="password_confirmation" class="form-control form-control-user @error('password') is-invalid @enderror" placeholder="{{ __('Type your new password confirmation') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-user btn-block">
                      Reset Password
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="{{asset('admin/vendor/jquery/jquery.min.js')}}"></script>
  <script src="{{asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{asset('admin/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

  <!-- Custom scripts for all pages-->
  <script src="{{asset('admin/js/sb-admin-2.min.js')}}"></script>

</body>

</html>

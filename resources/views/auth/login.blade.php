<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Kyoo Admin</title>
  <link rel="icon" href="{{ asset('img/logo-icon.svg') }}" type="image/icon type">
  <!-- Custom fonts for this template-->
  <link href="{{asset('admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{asset('admin/css/sb-admin-2.min.css')}}" rel="stylesheet">
  <style>
    .bg-gradient-primary {
      background: linear-gradient(121.16deg, #189DCD 0.95%, #0A5194 97.59%);
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
              <div class="col-lg-6 d-none d-lg-block bg-login-image" style="background-image: url({{asset('/img/queue.jpeg')}});"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <img src="{{asset('img/logo-color.svg')}}" alt="" class="img-fluid mb-4">
                  </div>
                  @include('layouts.alert')
                  <form method="POST" action="{{ route('login') }}" class="user">
                        @csrf
                    <div class="form-group">
                      <input type="text" name="email" class="form-control form-control-user @error('email') is-invalid @enderror" value="{{ old('email') }}" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Enter Email or Username...">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                      <input type="password" name="password" class="form-control form-control-user @error('password') is-invalid @enderror" id="exampleInputPassword" placeholder="Password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    {{-- <div class="form-group">
                      <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="customCheck">
                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                      </div>
                    </div> --}}
                    <button type="submit" class="btn btn-primary btn-user btn-block">
                      Login
                    </button>
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="{{route('password.request')}}">Forgot Password?</a>
                  </div>
                  <div class="text-center">
                    <a class="small" href="{{route('register')}}">Register your Branch here!</a>
                  </div>
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

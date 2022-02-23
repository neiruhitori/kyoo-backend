<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>{{ __('Kyoo Admin') }}</title>
  <link rel="icon" href="{{ asset('img/favico.png') }}" type="image/icon type">
  <!-- Custom fonts for this template-->
  <link href="{{asset('admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
  <link
    href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
    rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{asset('admin/css/sb-admin-2.min.css')}}" rel="stylesheet">
  <style>
    .bg-gradient-primary {
      background: linear-gradient(121.16deg, #189DCD 0.95%, #0A5194 97.59%);
    }

    .fullwidth {
      width: 100%;
    }
  </style>
</head>

<body class="bg-gradient-primary">

  <div class="container pt-5">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="row">
          <div class="col-md-12">
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Verification Success!') }}</h6>
              </div>
              <div class="card-body text-center">
                <img src="{{asset('img/logo-color.svg')}}" alt="" class="mb-3">
                <img src="{{asset('img/desk.svg')}}" alt="" class="mb-3" style="max-height: 200px">
                <div class="row">
                  <div class="col-md-12">
                    <p>{{ __('You email profile succesfully updated. Please re-login on KYOO Apps.') }}</p>
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
  @stack('name')
</body>

</html>
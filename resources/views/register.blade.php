<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Kyoo Admin</title>

  <!-- Custom fonts for this template-->
  <link href="{{asset('admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

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
                        <h6 class="m-0 font-weight-bold text-primary">Regist your Branch here</h6>
                    </div>
                    <div class="card-body">
                        <img src="{{asset('img/logo-color.svg')}}" alt="" class="mb-3">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" method="post">
                                    @csrf
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Branch Profile</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="location-tab" data-toggle="tab" href="#location" role="tab" aria-controls="location" aria-selected="false">Branch Location</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="admin-tab" data-toggle="tab" href="#admin" role="tab" aria-controls="admin" aria-selected="false">Branch Admin</a>
                                        </li>
                                        </ul>
                                        <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                            @include('admin.branch.create1')
                                        </div>
                                        <div class="tab-pane fade" id="location" role="tabpanel" aria-labelledby="location-tab">
                                            @include('admin.branch.create2')
                                        </div>
                                        <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                                            @include('admin.branch.create3')
                                        </div>
                                    </div>
                                </form>
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

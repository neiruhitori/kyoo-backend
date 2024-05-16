<!DOCTYPE html>
<html lang="en">

<head>
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0,shrink-to-fit=no, maximum-scale=1.0, user-scalable=yes">
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Kyoo Admin</title>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Custom fonts for this template-->
  <link href="{{asset('admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{asset('admin/css/sb-admin-2.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" integrity="sha512-3JRrEUwaCkFUBLK1N8HehwQgu8e23jTH4np5NHOmQOobuC4ROQxFwFgBLTnhcnQRMs84muMh0PnnwXlPq5MGjg==" crossorigin="anonymous" />

  <link rel="icon" href="{{ asset('img/favico.png') }}" type="image/icon type">

  <style>
    .bg-gradient-primary {
      background-image: linear-gradient(355.22deg, #189DCD 4.98%, #0A5194 121.39%);
    }
    .fullwidth {
      width: 100%;
    }
    .custom-icon {
      display: inline-flex;
      justify-content: center;
      align-items: center;
      width: 20px;
      height: 20px;
      font-weight: bold;
      background-color: black;
      color: white;
      border-radius: 100%;
      margin: 4px;
      line-height: 0;
    }

    .custom-info .custom-info-head {
      display: flex;
      align-items: center;
    }

    .custom-info-head h6 {
      flex: 1 1 0%;
    }

    .custom-info .custom-info-body {
      margin-top: 0.75rem;
    }

    .custom-info .custom-info-head button {
      display: none;
    }

    .custom-muted-btn {
      padding: 0;
      border: none;
      background: transparent;
    }
  </style>
  @stack('css')
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    @switch(Auth::user()->role)
        @case('admin_kyoo')
            @include('layouts.sidebarAdmin')
            @break

        @case('admin_branch')
            @include('layouts.sidebarAdminBranch')
            @break

        @case('cs')
        @case('spv')
            @include('layouts.sidebarCS')
            @break

        @case('admin_corporate')
            @include('layouts.sidebarAdminCorporate')
            @break
        @default

    @endswitch

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        @include('layouts.navbar')

        <!-- Begin Page Content -->
        <div class="container-fluid">
          @yield('content')
        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Kyoo.id 2020</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Bootstrap core JavaScript-->
  <script type="text/javascript" src="{{asset('admin/vendor/jquery/jquery.min.js')}}"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js" integrity="sha512-k6/Bkb8Fxf/c1Tkyl39yJwcOZ1P4cRrJu77p83zJjN2Z55prbFHxPs9vN7q3l3+tSMGPDdoH51AEU8Vgo1cgAA==" crossorigin="anonymous"></script>
  <script src="{{asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{asset('admin/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="{{asset('admin/js/sb-admin-2.min.js')}}"></script>

  <script src="{{asset('admin/vendor/chart.js/Chart.min.js')}}"></script>

  <script>
    window._asset = '{{ asset('') }}'

    $(document).ready(function () {
      initAuth()

      $('.datetimepicker-input').datetimepicker({
        format: 'HH:mm'
      })

      $('.custom-info button[data-toggle="alert"]').click(function (e) {
        const alertEl = $(this).parents('.custom-info[role="alert"]');

        if (alertEl.data('open') == 'open') {
          alertEl.data('open', 'close');
          alertEl.find('.custom-info-body').css('display', 'none');
          alertEl.find('.custom-info-head button').css('display', 'inline-block');
        } else {
          alertEl.data('open', 'open');
          alertEl.find('.custom-info-body').css('display', 'block');
          alertEl.find('.custom-info-head button').css('display', 'none');
        }
      })
    })

    async function initAuth() {
      const personalAccessToken = await axios.get('/oauth/personal-access-tokens')
        .then(res => res.data.pop())

      if (
        !localStorage.getItem('accessToken') ||
        !personalAccessToken ||
        Date.now() > new Date(personalAccessToken.expires_at) ||
        personalAccessToken.revoked
      ) {
          const accessToken = await axios.post('/oauth/personal-access-tokens', {
            name: 'API Gateway Token'
          }).then(res => res.data.accessToken)

          localStorage.setItem('accessToken', accessToken)
          return
      }

      localStorage.setItem('accessToken', '{{ Auth::user()->token_personal }}')
    }
  </script>

  @stack('js')

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
  <!-- Custom fonts for this template-->
  <link href="{{asset('admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{asset('admin/css/sb-admin-2.min.css')}}" rel="stylesheet">
  <style>
    .img-logo {
        width: 200px;
        margin-bottom: 20px;
    }
    .img-playstore {
        width: 200px;
    }
    .bg-gradient-primary {
      background: linear-gradient(121.16deg, #189DCD 0.95%, #0A5194 97.59%);
    }
    .fullwidth {
        width: 100%;
    }
    @media(min-width: 300px){
        p {
            margin-bottom: 0px;
        }
        .col-md-8 {
            margin-bottom: 16px;
        }
        .text-right {
            text-align: left !important;
        }
    }
    @media(min-width: 700px){
        .text-right {
            text-align: right !important;
        }
    }
  </style>
</head>

<body class="bg-gradient-primary">
    <div id="app">
        <branch-monitor-component 
            :branch="{{$branch}}"
            branch_id_encrypted="{{$branchIdEncrypted}}"
        />
    </div>
    <script src="{{asset('js/app.js')}}"></script>
</body>

</html>

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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }
  
        html, body {
          font-family: Inter, sans-serif;
          font-weight: 400;
        }
    </style>
</head>

<body>
    <div id="app">
        <Device-Signage-Custom-1-Component 
            :branch="{{$branch}}"
            :features="{{$features}}"
            :workstations="{{$workstations}}"
        />
    </div>
    <script src="{{asset('js/app.js')}}"></script>
</body>

</html>

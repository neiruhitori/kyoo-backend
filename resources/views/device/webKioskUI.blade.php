<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Kyoo Device</title>
    <link rel="icon" href="{{ asset('img/favico.png') }}" type="image/icon type">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="{{asset('admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('admin/css/sb-admin-2.css')}}" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css"
        integrity="sha512-3JRrEUwaCkFUBLK1N8HehwQgu8e23jTH4np5NHOmQOobuC4ROQxFwFgBLTnhcnQRMs84muMh0PnnwXlPq5MGjg=="
        crossorigin="anonymous" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            font-family: Inter, sans-serif;
            font-weight: 400;
        }

        body::-webkit-scrollbar{
            display: none;
        }
    </style>
</head>

<body>
    <div id="app">
        @if ($layoutCode == "layout_1")
        <default-web-kiosk :branch="{{$branch}}" :address="{{json_encode($address)}}" qr={{$qr}} />
        @endif

        @if ($layoutCode == "layout_2")
        <layout-2-web-kiosk
            :branch="{{ $branch }}"
            :auth="{{ Auth::user() }}"
            qr={{ $qr }}
            :layout_config="{{ $layoutConfig }}"
            :is_allow_wa={{ $isAllowWA }}
            :active_menus={{ $activeMenus }}
        />
        @endif

        @if($layoutCode == "layout_3")
        <layout-3-web-kiosk
            :branch="{{ $branch }}"
            :auth="{{ Auth::user() }}"
            :layout_config="{{ $layoutConfig }}"
            :is_allow_wa={{ $isAllowWA }}
            :active_menus={{ $activeMenus }}
        />
        @endif
    </div>
    <script src="{{asset('js/app.js')}}"></script>
</body>

</html>

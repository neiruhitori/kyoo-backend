<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta
            name="description"
            content="Kyoo is a web app for ordering queue ticket"
        />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Kyoo</title>

        <link rel="icon" href="{{ asset('img/favico.png') }}" type="image/icon type">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            html, body {
                font-size: 14px;
                font-family: Inter, sans-serif;
                background-color: #E2E2E2;
            }

            button, input {
                font-family: Inter, sans-serif;
            }

            a {
                text-decoration: none;
                color: #000000;
            }

            .main-container {
                max-width: 420px;
                margin: 0 auto;
                background-color: #FFFFFF;
                position: relative;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .content-wrapper {
                padding: 3rem 1.375rem;
            }

            .logo-container {
                text-align: center; 
                margin-bottom: 3rem;
            }

            .content-title {
                font-size: 1.5rem;
                font-weight: 700;
                text-align: center;
                margin-bottom: 1.125rem;
            }

            .content-description {
                text-align: center;
                font-weight: 400;
                width: 300px;
                margin: 0 auto;
            }

            label.k-input-label {
                font-size: .875rem;
                display: inline-block;
                margin-bottom: .375rem;
            }

            input.k-input {
                width: 100%;
                border-radius: 6px;
                overflow: hidden;
                background-color: #EFF2F5;
                box-sizing: border-box;
                padding: 1rem;
                border: none;
                outline: none;
                font-size: 1rem;
                font-family: Inter, sans-serif;
            }

            button.k-button {
                font-family: Inter, sans-serif;
                background-color: #007EC6;
                color: #FFFFFF;
                padding: 1rem 1.125rem;
                border: none;
                border-radius: 6px;
                outline: none;
                width: 100%;
                text-align: center;
            }

            .alert.alert-danger {
                padding: 1.125rem;
                background-color: rgb(22, 11, 11);
                border-radius: 6px;
                color: rgb(244, 199, 199);
                display: flex;
            }

            .alert .alert-content {
                flex: 1 1 0%;
                padding-left: 1rem;
            }
        </style>
    </head>

    <body>
        <div class="main-container">
            <div class="content-wrapper">
                <div class="logo-container">
                    <img src="img/logo-color.svg">
                </div>
                
                <div style="margin-bottom: 3.125rem;">
                    <h4 class="content-title">Login Kode Unik</h4>
                    <p class="content-description">Masukkan kode unik untuk melihat status antrian Anda.</p>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 1.125rem">
                    <span style="display: inline-block;">
                        <svg height="1.125rem" width="auto" viewBox="0 0 512 512" fill="rgb(244, 67, 54)">
                            <path d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256s256-114.6 256-256S397.4 0 256 0zM232 152C232 138.8 242.8 128 256 128s24 10.75 24 24v128c0 13.25-10.75 24-24 24S232 293.3 232 280V152zM256 400c-17.36 0-31.44-14.08-31.44-31.44c0-17.36 14.07-31.44 31.44-31.44s31.44 14.08 31.44 31.44C287.4 385.9 273.4 400 256 400z"/>
                        </svg>
                    </span>

                    <div class="alert-content">
                        {{ $errors->first() }}
                    </div>
                </div>
                @endif

                <form action="{{ route('search.search') }}" method="POST">
                    @csrf

                    <div style="margin-bottom: 1.125rem">
                        <label for="booking-code" class="k-input-label">Kode Unik</label>
                        <input type="text" name="booking_code" class="k-input" id="booking-code">
                    </div>

                    <button type="submit" class="k-button">Cari</button>
                </form>
            </div>
        </div>
    </body>
</html>

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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&display=swap" rel="stylesheet">

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

      a {
        text-decoration: none;
        color: #000000;
      }
    </style>
  </head>

  <body>
    <noscript>You need to enable JavaScript to run this app.</noscript>

    <div id="root"></div>
  </body>

  <script src="{{ mix('kyoo-ticket/js/app.js') }}"></script>
</html>
<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Polymer</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

        <!-- Styles -->
        <style>
            * {
                box-sizing: border-box;
            }
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Roboto', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
                padding: 0;
                overflow: hidden;
                user-select: none;
            }
        </style>
        <link rel="import" href="/elements/grid-app.html" />
    </head>
    <body>
        <grid-app>
            <grid-drawer></grid-drawer>
        </grid-app>
        <script type="text/javascript" href="/bower_components/webcomponentsjs/webcomponents.js"></script>
        <script>
        // var gMap = document.querySelector('google-map');

        // gMap.addEventListener('api-load', function(e) {
        //     document.querySelector('google-map-directions').map = this.map;
        // });
        </script>
    </body>
</html>

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
        <link rel="import" href="/grid-elements/grid-app" />
    </head>
    <body>
        @yield('content')
        
        {{-- <script src="/bower_components/webcomponentsjs/webcomponents.js"></script> --}}
        <script>
        (function() {
          if ('registerElement' in document
              && 'import' in document.createElement('link')
              && 'content' in document.createElement('template')) {
            // platform is good!
          } else {
            // polyfill the platform!
            var e = document.createElement('script');
            e.src = '/bower_components/webcomponentsjs/webcomponents-lite.min.js';
            document.body.appendChild(e);
          }
        })();
        // var gMap = document.querySelector('google-map');

        // gMap.addEventListener('api-load', function(e) {
        //     document.querySelector('google-map-directions').map = this.map;
        // });
        </script>
    </body>
</html>

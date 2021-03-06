<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>The Grid</title>

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
                /*user-select: none;*/
            }
            /*.pac-container {
                margin-top: -210px;
                max-height: 186px !important;
            }*/
        </style>
        <script>
        /*
            @if (Auth::check())
            window.Grid = {!! json_encode([
                'authenticated' => true,
                'user_id' => Auth::id(),
                'csrfToken' => csrf_token(),
                'session_id' => Session::getId(),
                'user' => [
                  'id' => Auth::id(),
                  'name' => Auth::user()->name,
                  'profile' => Auth::user()->profile ?: json_decode ("{}")
                ]
            ]) !!};
            @else
            window.Grid = {!! json_encode([
                'authenticated' => false,
                'csrfToken' => csrf_token(),
            ]) !!};
            @endif
          */
          window.Grid = {
            'authenticated': false
          }
          if ( localStorage.getItem('user') ) {
            window.Grid.user = JSON.parse(localStorage.getItem('user'));
            window.Grid.authenticated = true;
          }
        </script>
        <!-- <link rel="import" href="/bower_components/polymer/polymer.html"> -->
        <!-- <link rel="import" href="/elements/elements.html" /> -->
        <!-- <link rel="import" href="/elements/elements.html" /> -->
        <!-- <link rel="import" href="/bower_components/polymer/bundles" /> -->
        <link rel="import" href="/elements/grid-app.html" />
        <link rel="import" href="/elements/the-grid/grid-scripts/socket-io.html" />
        <link rel="import" href="/elements/the-grid/grid-scripts/axios.html" />
        <script type="text/javascript">
            var socket = new io.connect('{{env('SOCKET_HOST')}}:{{env('SOCKET_PORT')}}');
            socket.on('connect', function(){
                console.log('connected to socket');
                socket.emit('new-user', Grid);
            });
            socket.on('error', function() {
            	console.log('Error connecting');
            });
            socket.on('user-callback', function(data){
                console.log('user callback', data);
            });
            socket.on('testing', function(data){
                console.log('testing', data);
            });
        </script>
    </head>
    <body>
        @yield('content')
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

        </script>
        {{-- <script>
          window.intercomSettings = {
            app_id: "vr6zx7kr"
          };
        </script>
        <script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/vr6zx7kr';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script> --}}
    </body>
</html>

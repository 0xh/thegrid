<script src="/bower_components/socket.io-client/dist/socket.io.js"></script>
<script type="text/javascript">
    var socket = new io.connect('{{env('SOCKET_HOST')}}:{{env('SOCKET_PORT')}}', {
      query: "token=" + Grid.session_id
    });
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
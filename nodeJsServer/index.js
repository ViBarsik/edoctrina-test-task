var app = require('express')();
var http = require('http').Server(app);
var ws = require('socket.io')(http);

http.listen(3745, function(){
    console.log('Server start on *:3745');
});

app.get('/', function(req, res){
    res.sendFile(__dirname + '/index.html');
});

app.post('/reload', function(req, res){
    console.log('Test finised');
    ws.sockets.to(res.req.query.room).emit('reload', 'Test is save!');
    res.send('1');
});


ws.on('connection', function(socket){
    console.log('connect new socket ' + socket.id + '. Join to room '+ socket.handshake.query.room);
    socket.join(socket.handshake.query.room);

    socket.on('open', function(mess){
        ws.sockets.sockets[socket.id].emit('open', 'Socket allow!');
    });

    socket.on('test', function(mess){
        ws.sockets.sockets[socket.id].emit('test', 'Socket server is work!');
    });

    socket.on('selectAnswer', function(data){
        console.log('selectAnswer to room ' + socket.handshake.query.room);
        socket.broadcast.to(socket.handshake.query.room).emit('selectAnswer', data);
    });

    socket.on('disconnect', function(){
        console.log('Disconnect socket ' + socket.id + ' from room '+ socket.handshake.query.room);
    });
});
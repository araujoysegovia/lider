//var app = require('express')();
var http = require('http').Server();
var io = require('socket.io')(http);
//var querystring = require('querystring');


var socketg = null;

io.on('connection', function(socket){	
  socketg = socket; 

  socket.on('disconnect', function(){
    console.log('user disconnected');
  });
  
  socket.on('realTime', function(datos){  
      io.emit("realTime", datos); 
  });

  
});

/*function addusuario(usuario){
  socketg.on(usuario, function(msg){   
  
  console.log(msg); 
  console.log(usuario); 
   io.emit(usuario, msg);
  });
}*/
http.listen(3000, function(){
  console.log('listening on *:3000 ');
});


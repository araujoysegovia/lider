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
  
  socket.on('question', function(question, user){
      console.log(user);
      console.log(question);
      io.emit("question", question, user); 
  });

  socket.on('time', function(datos){
      console.log(datos)
      io.emit("time", datos); 
  });

  socket.on('answer', function(datos){
      console.log(datos)
      io.emit("answer", datos); 
  });

  socket.on('help', function(datos){
      console.log(datos)
      io.emit("help", datos); 
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


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
      io.emit("question", question, user); 
  });

  socket.on('time', function(time, user){
      io.emit("time", time, user); 
  });

  socket.on('answer', function(answer, user,answerId){
      io.emit("answer", answer, user,answerId); 
  });

  socket.on('help', function(help, user){
      io.emit("help", help, user); 
  });

  socket.on('load', function(user){
      io.emit("load", user); 
  });

  socket.on('goOut', function(user){
      io.emit("goOut", user); 
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


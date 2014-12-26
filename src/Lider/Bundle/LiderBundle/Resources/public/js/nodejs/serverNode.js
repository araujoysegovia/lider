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

  socket.on('time', function(time, user, question){
      io.emit("time", time, user, question); 
  });

  socket.on('answer', function(answer, user, question, pointsForQuestion){
      io.emit("answer", answer, user, question, pointsForQuestion); 
  });

  socket.on('help', function(help, user){
      io.emit("help", help, user); 
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


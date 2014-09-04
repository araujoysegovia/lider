var teamBuilder = function () {

	if(typeof this.constructor == "function"){
		this.constructor.apply(this, arguments);
	}

}

teamBuilder.prototype = {

	cities : [],
	teams: [],
	players: [],
	container: null,
	dragElement: null,

	constructor: function (container, min, max) {
		this.container = container;
		this.getData(min, max);
	},

	getData: function  (min, max) {
			
		var me = this;

		me.cities = [],
		me.teams = [],
		me.players = [],

		parameters = {
			type: "GET",     
            url: "http://localhost/lider/web/app_dev.php/admin/home/team/generate?max="+max+"&min="+min,		            
            contentType: 'application/json',
            dataType: "json",
            success: function(data){
            	cities = data['cities'];

            	me.generateCity(cities);
            },
            error: function(){},
		};
			
		var ajax = $.ajax(parameters);			
		
	},

	/**
	* Crear contenedor por ciudades
	*/
	generateCity:function (cities){
		
		var me = this;
		me.container.empty();
    	_.each(cities, function(value, key){
    		
    		var city = {};
    		city['name'] = key;
    		city['teams'] = [];
    		city['outTeam'] = null;
    		city['lastItem'] = 0;

    		var panel = $('<div id="panel-'+key+'" class="panel-city">'+
    						'<div class="panel-heading">'+
    							'<div>' + 
    								'<h2>'+key+'</h2>'+
    								'<button type="button" class="btn btn-success btn-xs add-team">+</button>'+
	    							'<p>Total de jugadores: <label class="total-players">'+value.totalPlayers+'</label></p>'+
	    							'<p>Total de equipos: <label class="total-team">'+value.totalTeam+'<label></p>'+	
	    							'<p>Total de jugadores sin equipo: <label class="total-out">'+value.totalOut+'</label></p>'+
	    						'</div>' + 
	    						'<hr>'+
	    					'</div>'+	
    						'<div class="panel-body panel-body-city">'+    							
    						'</div>'+ 
    					 '</div>');
    		
    		me.container.append(panel);
    		
    		city['htmlObject'] = panel;
    		me.cities[key] = city;
    		
    		me.generateTeamsByCity(value.teams, city);    		
    		if(value.out && value.out.length > 0){
    			me.generateOut(value.out, city);	
    		}
			panel.find(".add-team").click(function () {
				me.cities[key].lastItem++;
				var t = {
					name : "Equipo "+(me.cities[key].lastItem)
				};
	    		me.generateTeamsByCity([t], city, false);
	    		city.htmlObject.find(".total-team").html(city.teams.length);
	    	});

    	});

    
	},


	/**
	* Crear panel de equipos
	*/
	generateTeamsByCity: function(teams, city, clear){			

		var me = this;
		
		if(clear || clear === undefined){
			city.htmlObject.find("div.panel-body-city").empty();
		}
		

		_.each(teams, function(value, key){
			//ondrop="drop(event)" ondragover="allowDrop(event)"
			var panel = $('<div class="panel panel-primary panel-team" >'+
								'<div class="panel-heading title-team">'+
									value.name + 
									'<div class="destroy-group" style="float:right; margin-left:5px; cursor:pointer">X</div>' +
								'</div>'+
								'<div class="panel-body panel-body-team">'+
								'</div>'+ 
						   '</div>');
			
			city.htmlObject.find("div.panel-body-city").append(panel);

			var team = {
				'name': value.name,
				'city': city,
				'htmlObject' : panel,
				'out': false,
				'outRange': false
			};

			panel.find('div.destroy-group').click(function(e){
				me.destroyGroup(team);
			});

			if(!city.teams)
				city.teams = [];

			city.teams.push(team);
			city.lastItem++;

			panel.on("drop", function (event) {
				me.drop(event,team);
			});

			panel.on("dragover", function (ev) {
					ev.preventDefault();
			});

			me.generatePlayersList(value.players, team);

			me.validateNumberPlayers(team);
		});
		
	},

	/**
	* Panel de jugadores sin equipo
	*/
	generateOut: function (players, city) {

		var me = this;
		players = players || [];
		if(city){
			if(!(city.outTeam)){			
				//ondrop="drop(event)" ondragover="allowDrop(event)"
				var panelOut = $('<div class="panel panel-warning panel-team-out" >'+
								'<div class="panel-heading title-team">'+
									'Sin Equipo' + 
									'<div class="add-group" style="float:right; margin-left:5px; cursor:pointer">+</div>' +
								'</div>'+
								'<div class="panel-body panel-body-team">'+
								'</div>'+ 
						   '</div>');
				var team = {};
				team['name'] = "Sin equipo";
				team['htmlObject'] = panelOut;
				team['city'] = city;
				team['out'] = true;

				city.outTeam = team;

				panelOut.find('div.add-group').click(function(e){
					me.makeGroup(team);
				});

				panelOut.on("drop", function (event) {
					me.drop(event, team);
				});

				panelOut.on("dragover", function (ev) {
					ev.preventDefault();
				});

				city.htmlObject.find("div.panel-body-city").append(team.htmlObject);

			}else{
				var team = city.outTeam;
				// _.union(players, team.players);
			}

			// if(players.length == 0){				
			// 	team.htmlObject.remove();
			// 	city.outTeam = null;
			// }

			me.generatePlayersList(players, team);
		}
	},

	/**
	* Crear contenedor de jugadores
	*/
	generatePlayersList: function(players, team){
		
		var me = this;

		_.each(players, function(value, key){			

			if(value){
				var img = value.image;
				if(!img){
					img = 'http://soylider.sifinca.net/bundles/lider/images/avatar.png'
				}
				//ondragstart="drag(event)"
				var panel = $('<div id="player-'+value.id+'" class="panel-player" draggable="true" >'+
								'<div class="img-player">'+
									'<img src='+img+'>'+
								'</div>'+
								'<div class="name-player"><p>'+value.name.toLowerCase() +'</p></div>'+
							+'</div>');
				
				var t = {
					"id" : value.id,
					"name": value.name,
					"team": team,
					"htmlObject" : panel
				};

				panel.on("dragstart", function (event) {
					me.drag(event,t);

				});
				
				if(!team['players']){
					team['players'] = [];
				}


				team['players'].push(t);

				team.htmlObject.find("div.panel-body-team").append(panel);
			}

		});
	},


	
	drag: function(ev,obj) {

		this.dragElement = obj;
	},

	/*
	 * Ingresar
	 * */
	drop: function(ev, obj) {

	 	var me = this;

	    ev.preventDefault();

	    var player = this.dragElement;
	   
	    obj.htmlObject.children(".panel-body-team").append(player.htmlObject);
	    
	    _.each(player.team['players'], function (value, key) {
	    	if(value.id == player.id){
	    		player.team['players'].splice(key, 1);	 
	    		return ;
	    	}
	    });

		//console.log(player.team)	
	    var oldTeam = player.team;
	    player.team = obj;
	    if(!obj.players) obj.players=[];
	    obj.players.push(player);

	    // console.log(oldTeam.players.length)
	    // console.log(obj.players.length)

	  
		var numPlayersOld = oldTeam.players.length;

		me.validateNumberPlayers(obj);
		me.validateNumberPlayers(oldTeam);
	    		
	    	
	    
	    
	    //ev.target.appendChild(document.getElementById(data));
	},

	validateNumberPlayers: function (obj) {
		var me = this;
		var numPlayers = obj.players ? obj.players.length : 0;

		if((numPlayers < min) || (numPlayers > max)){
			obj.htmlObject.removeClass("panel-primary")
			obj.htmlObject.removeClass("panel-warning");
			obj.htmlObject.addClass("panel-danger");
			obj.outRange = true;
		}else if(!obj.out){			
			obj.htmlObject.removeClass("panel-warning");
			obj.htmlObject.removeClass("panel-danger");
			obj.htmlObject.addClass("panel-primary");
			obj.outRange = false;

			/*if(obj.out){
				//console.log(obj.city.teams)
				
			}*/
		}
	},

	destroyGroup: function(obj){
		console.log(obj)
		var me = this;
		me.generateOut(obj.players, obj.city);
		obj.htmlObject.remove();
		_.each(obj.city['teams'], function (value, key) {
	    	if(value.name == obj.name){
	    		obj.city['teams'].splice(key, 1);
	    		obj.city.htmlObject.find(".total-team").html(obj.city.teams.length);
	    		if(obj.city.outTeam){
	    			obj.city.htmlObject.find(".total-out").html(obj.city.outTeam.players.length);	
	    		}	    		
	    		return ;
	    	}
	    });
	},

	makeGroup: function(obj){
		var me = this;
		obj.city.teams.lastItem++;
		var name = obj.city.name+" - Equipo "+(obj.city.teams.lastItem);
		obj.name = name;
		obj.out = false;
		console.log(obj)
		//console.log(obj.city.teams);
		obj.city.htmlObject.find(".total-team").html(obj.city.teams.length + 1);
		obj.city.htmlObject.find(".total-out").html(obj.city.outTeam.length - 1);

		obj.city.teams.push(obj);
		obj.htmlObject.find(".title-team").html(name + '<div class="destroy-group" style="float:right; margin-left:5px; cursor:pointer">X</div>');
		obj.city.outTeam = null;
		obj.htmlObject.find('div.destroy-group').click(function(e){
			me.destroyGroup(obj);
		});
		me.validateNumberPlayers(obj);
	}

}

/***** Funciones de arrastrar y soltar *****/

// function allowDrop(ev) {
	
//     ev.preventDefault();
// }

// function drag(ev) {
	
//     ev.dataTransfer.setData("Text", ev.target.id);
// 	//console.log(ev.toElement)
// 	var p = $(ev.toElement).parent();
// 	var abu = p.parent();
// 	//console.log(p)
// 	var numPlayers = p[0].childElementCount;
// 	numPlayers = numPlayers-1;
// 	//console.log(numPlayers)
// 	//console.log(numPlayers+" < "+min+" || "+ numPlayers+" > "+ max)
// 	if((numPlayers < min) || (numPlayers > max)){
		
// 		abu.removeClass("panel-warning");
// 		abu.addClass("panel-danger");
// 	}else{
		
// 		abu.removeClass("panel-warning");
// 		abu.removeClass("panel-danger");
// 		abu.addClass("panel-primary");
// 	}	
	
// }

// /*
//  * Ingresar
//  * */
// function drop(ev, numTeam) {
//     ev.preventDefault();
//     var data = ev.dataTransfer.getData("Text");
//     var player = $("div#"+data);
//     var target =  $(ev.target);
//     //console.log(target)
//     if(target.hasClass("panel-body")){
//     	$(ev.target).append(player);
//     }else{
//     	var parent = target.parents("div.panel-body-team");
//     	if(parent.length >0 ){
//     		parent.append(player)
// //    		console.log(parent)
    		
//     		var numPlayers = parent[0].childElementCount;

// //    		console.log(numPlayers)
//     		//console.log(numPlayers+" < "+min+" || "+ numPlayers+" >"+ max)
//     		if((numPlayers < min) || (numPlayers > max)){
//     			//console.log("entro")
// //    			console.log(parent.parent().children(".title-team"))
//     			var t = parent.parent().children(".title-team");
// //    			t.css("background", "red");
//     			parent.parent().removeClass("panel-warning");
//     			parent.parent().addClass("panel-danger");
//     		}else{
    			
//     			var panel = parent.parent(); /* Panel del equipo */
//     			panel.removeClass("panel-warning");
//     			panel.removeClass("panel-danger");
//     			panel.addClass("panel-primary");

//     			if(panel.hasClass("panel-team-out")){
//     				panel.children(".title-team").empty();
    				

//     				var newTitle = $('<label>Equipo '+(numTeam+1)+'<label>');
//     				panel.children(".title-team").append(newTitle);
//     			}
    			
//     		}
    		
//     	}
//     }
    
//     //ev.target.appendChild(document.getElementById(data));
// }


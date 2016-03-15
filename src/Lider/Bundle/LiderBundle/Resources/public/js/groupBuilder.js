var groupBuilder = function () {

	if(typeof this.constructor == "function"){
		this.constructor.apply(this, arguments);
	}
}

var server = 'http://162.209.101.142/';

groupBuilder.prototype = {

	container: null,

	constructor: function (container, min, max) {
		
		this.container = container;
		this.getData(min, max);
	},

	getData: function  (min, max) {
			
		var me = this;

		me.groups = [],

		parameters = {
			type: "GET",     
            url: "home/group/generate?max="+max+"&min="+min,		            
            contentType: 'application/json',
            dataType: "json",
            success: function(data){
            	groups = data['groups'];

            	me.generateGroup(groups);
            },
            error: function(){},
		};
			
		var ajax = $.ajax(parameters);			
		
	},

	/**
	* Generar grupos
	*/
	generateGroup: function(groups){			

		var me = this;
		
		me.container.empty();

		// var btAdd = $('<div class="bt-add-group"><button type="button" class="btn btn-success btn-xs add-team">+</button></div>');
		
		// me.container.append(btAdd);
		var arrWord = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
		var cont=0;
		_.each(groups, function(value, key){
			
    		var group = {};
    		var groupName = "Grupo "+arrWord[cont];
    		group['name'] = groupName;
    		group['teams'] = [];    		

			var panel = $('<div class="panel panel-primary panel-group" >'+
							'<div class="panel-heading title-group">'+
								groupName + 								
							'</div>'+
							'<div class="panel-body panel-body-group">'+
							'</div>'+ 
					   '</div>');
			
			me.container.append(panel);

			group['htmlObject'] = panel;

			panel.on("drop", function (event) {
				me.drop(event,group);
			});

			panel.on("dragover", function (ev) {
					ev.preventDefault();
			});			

			me.generateTeamList(value.teams, group);

			me.groups.push(group);

			cont++;
		});
		
	},	

	/**
	* Asignar equipos a un grupo
	*/
	generateTeamList: function(teams, group){			

		var me = this;	


		_.each(teams, function(value, key){

			var img = value.image;
			if(!img){
				img = server + 'lider/web/bundles/lider/images/team_default.png'
			}

			var panel = $('<div id="team-'+value.id+'" class="g-panel-team" draggable="true" >'+
							'<div class="g-img-team">'+
								'<img src='+img+'>'+
							'</div>'+
							'<div class="g-name-team"><p>'+value.name.toLowerCase() +'</p></div>'+
						+'</div>');

			
			var team = {
				'id' : value.id,
				'name': value.name,	
				'group': group,
				'htmlObject' : panel
			};

			panel.on("dragstart", function (event) {
				
				me.drag(event,team);

			});
			
			if(!group['teams']){
				group['teams'] = [];
			}


			group['teams'].push(team);

			group.htmlObject.find("div.panel-body-group").append(panel);

		});
		
	},

	drag: function(ev,obj) {

		this.dragElement = obj;
	},

	drop: function(ev, obj) {

	 	var me = this;

	    ev.preventDefault();

	    var team = this.dragElement;
	   	console.log(obj)
	   	console.log(team)
	    obj.htmlObject.children(".panel-body-group").append(team.htmlObject);
	    
	    _.each(team.group['teams'], function (value, key) {
	    	if(value.id == team.id){
	    		team.group['teams'].splice(key, 1);	 
	    		return ;
	    	}
	    });
		
	    var oldTeam = team.group;
	    team.group = obj;
	    if(!obj.teams) obj.teams=[];
	    obj.teams.push(team);
	  
		var numPlayersOld = oldTeam.teams.length;

		me.validateNumberTeams(obj);
		me.validateNumberTeams(oldTeam);
	  
	},

	/**
	* Validar cantidad de equipos en un grupo
	*/	
	validateNumberTeams: function (obj) {
		var me = this;
		var numTeams = obj.teams ? obj.teams.length : 0;

		if((numTeams < min) || (numTeams > max)){
			obj.htmlObject.removeClass("panel-primary")
			obj.htmlObject.removeClass("panel-warning");
			obj.htmlObject.addClass("panel-danger");
			obj.outRange = true;
		}else if(!obj.out){			
			obj.htmlObject.removeClass("panel-warning");
			obj.htmlObject.removeClass("panel-danger");
			obj.htmlObject.addClass("panel-primary");
			obj.outRange = false;

		}
	},


}
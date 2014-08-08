var routerManager = Backbone.Router.extend({

	routes: {
		"" : "home",   
		"tournaments" : "tournaments", 
		"questions" : "questions",
		"players" : "players",
		"groups" : "groups",
		"categories" : "categories"
	},

	home: function() {
		this.removeContent();
		this.buildbreadcrumbs({
		  Home: "",
		});
	},
  
	buildbreadcrumbs: function(items){
		var obj = $(".breadcrumb", "#breadcrumbs").empty();	  
		var l = _.keys(items).length, c=1;
		_.each(items, function(url, name){
			var list = $("<li></li>");
			if(c == l){
			  	list.addClass("active");
			  	list.html(name);
			}else{
				var a = $("<a></a>").attr("href", "#" + url);
				a.text(name);
				list.append(a);
			}
			obj.append(list);
			c++;
		});
	},

	removeContent: function(){
		$("#entity-content").empty();
	},  
  
	tournaments: function() {
		this.removeContent();
		this.buildbreadcrumbs({
		  	Home: "",
		  	Tournament: "tournament"
		});
		var tournament = new Entity({
			container:  $("#entity-content"),
			url: "home/tournament/",
			title: "Torneos",
			model: {
				id: "id",
				fields: {
					id: { editable: false, nullable: true },
				    name: { type: "string" },
				    startdate: { type: "date", parse: parseDate},
				    enddate: { type: "date", parse: parseDate},
				    active: { type: "boolean" }
				}
			},
			columns: [
				{ 
					field:"name", 
					title: "Nombre" 
				},
				{ 
					field: "startdate",
					title:"Fecha de inicio", 
					template: "#= kendo.toString(kendo.parseDate(startdate, 'yyyy-MM-dd'), 'dd/MM/yyyy') #"
				},
				{ 
					field: "enddate", 
					title:"Fecha de fin",
					template: "#= kendo.toString(kendo.parseDate(startdate, 'yyyy-MM-dd'), 'dd/MM/yyyy') #"
				},
				{ 
					field: "active",
					title: "Activo" 
				}
			]     
		});
	},

	questions: function(){
		this.removeContent();
		this.buildbreadcrumbs({
		  	Home: "",
		  	Preguntas: "question"
		});
		var question = new Entity({
		  	container:  $("#entity-content"),
		 	url: "home/question/",
		 	title: "Preguntas",
		  	model: {
			    id: "id",
			    fields: {
			    	id: { 
			    		editable: false, 
			    		nullable: true 
			    	},
			    	question: { 
			    		type: "string" 
			    	},
			    	category: { 
			    		type: "string", 
			    		parse: function(rec){
			    			console.log(rec)
			    			if(_.isObject(rec)){
			    				return rec.name;
			    			}
			    			return rec;
			    		}
			    	},		    	
			    	
			    }
			},
			columns: [
			    { 
			    	field:"question", 
			    	title: "Nombre" 
			    },
			    { 
			    	field: "category",
			    	title:"Categoria",
			    	width: "150px",
					template:  "#: category #", 
					editor:	function DropDownEditor(container, options) {
						//console.log(options)
			    	    $('<input required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
			            .appendTo(container)
			            .kendoDropDownList({
			                autoBind: false,		                
			                dataSource: {		                	
			                    transport: {
			                        read: "home/category/"
			                    },
			                    schema: {
			        			  	total: "total",
			        		    	data: "data",
			        		        model: {
			        				    id: "id",
			        				    fields: {
			        				    	id: { editable: false, nullable: true },
			        				        name: { type: "string" },		        				        
			        				    }
			        		        }
			                    },
			                },
			                dataTextField: "name",
			                dataValueField:"id"
			            });
					}  
			    },                
			],		  
		  
		})	  
	},
  
	players: function() {
		this.removeContent();
		this.buildbreadcrumbs({
		  	Home: "",
		  	Player: "Player"
		});
		var player = new Entity({
			container:  $("#entity-content"),
			url: "home/player/",
			title: "Jugadores",
			model: {
				id: "id",
				fields: {
					id: { 
						editable: false,
						nullable: true 
					},
					name: { 
						type: "string" 
					},
					lastname: { 
						type: "string" 
					},
					email: { 
						type: "string" 
					},
					office: {
						type: "string", 
						parse: function(rec){
							console.log(rec)
							if(_.isObject(rec)){
								return rec.name;
							}
							return rec;
						}
					}			   
				}
		  	},
			columns: [
				{ 
					field:"name", 
					title: "Nombre" 
				},
				{ 
					field:"lastname", 
					title: "Apellido" 
				},
				{ 
					field:"email", 
					title: "Correo" 
				},
				{ 
					field: "office",
					title:"Oficina",
					width: "150px",
					template:  "#: office #", 
					editor:	function DropDownEditor(container, options) {
						//console.log(options)
					    $('<input required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
				        .appendTo(container)
				        .kendoDropDownList({
				            autoBind: false,		                
				            dataSource: {		                	
				                transport: {
				                    read: "home/office/"
				                },
				                schema: {
				    			  	total: "total",
				    		    	data: "data",
				    		        model: {
				    				    id: "id",
				    				    fields: {
				    				    	id: { editable: false, nullable: true },
				    				        name: { type: "string" },		        				        
				    				    }
				    		        }
				                },
				            },
				            dataTextField: "name",
				            dataValueField:"id"
				        });
					}  
				},
			]
		})
	},

	groups: function() {
		this.removeContent();
		this.buildbreadcrumbs({
		  	Home: "",
		  	Groups: "groups"
		});
		var group = new Entity({
			container:  $("#entity-content"),
			url: "home/group/",
			title: "Grupos",
			model: {
				id: "id",
				fields: {
					id: { editable: false, nullable: true },
				    name: { type: "string" },
				    tournament: {
						type: "string", 
						parse: function(rec){
//							console.log(rec)
							if(_.isObject(rec)){
								return rec.name;
							}
							return rec;
						}
					},				    
				    active: { type: "boolean" }
				}
			},
			columns: [
				{ 
					field:"name", 
					title: "Nombre" 
				},
				{ 
					field: "tournament",
					title:"Torneo",
					width: "150px",
					template:  "#: tournament #", 
					editor:	function DropDownEditor(container, options) {
						//console.log(options)
					    $('<input required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
				        .appendTo(container)
				        .kendoDropDownList({
				            autoBind: true,		                
				            dataSource: {		                	
				                transport: {
				                    read: "home/tournament/"
				                },
				                schema: {
				    			  	total: "total",
				    		    	data: "data",
				    		        model: {
				    				    id: "id",
				    				    fields: {
				    				    	id: { editable: false, nullable: true },
				    				        name: { type: "string" },		        				        
				    				    }
				    		        }
				                },
				            },
				            dataTextField: "name",
				            dataValueField:"id",				            
				        });
					}  
				},				
				{ 
					field: "active",
					title: "Activo" 
				}
			]     
		});
	},

	categories: function() {
		this.removeContent();
		this.buildbreadcrumbs({
		  	Home: "",
		  	Category: "categories"
		});
		var category = new Entity({
			container:  $("#entity-content"),
			url: "home/category/",
			title: "Categorias",
			model: {
				id: "id",
				fields: {
					id: { editable: false, nullable: true },
				    name: { type: "string" },				   
				}
			},
			columns: [
				{ 
					field:"name", 
					title: "Nombre" 
				},				
			]     
		});
	}, 	  
});


$(document).ready(function () {
	var router = new routerManager();
	Backbone.history.start();
});

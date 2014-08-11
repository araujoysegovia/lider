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
			    	category: {},	
			    	answers: {
			    		parse: function(rec){				    			
							if(_.isObject(rec)){
								d = rec[0];
							}
							return rec;
						},
			    	},
			    	answerOne: {
			    		type: "string",
//			    		parse: function(rec){				    			
//							if(_.isObject(rec)){
//								var d = rec[0];
//								return d.name;
//							}
//							return rec;
//						},							
			    	},
			    	answerTwo: {
			    		type: "string",	
			    	},
			    	answerThree: {
			    		type: "string",		
			    	},
			    	answerFour: {
			    		type: "string",	
			    	},
			    	selected: {},
			    	help: {}
			    }
			},
			onReadData: function (e){
				_.each(e.response.data, function(value){
					var ans = value.answers;
					var a = ans[0], b = ans[1], c = ans[2], d = ans[3];
					
					value["answerOne"] = a.answer;
					value["answerTwo"] = b.answer;
					value["answerThree"] = c.answer;
					value["answerFour"] = d.answer;
					
				})
			},
			parameterMap: function (data, type) {
				
		        if (type !== "read") {	      
		        	
		        	data.answers[0].answer = data.answerOne;
		        	delete data.answerOne;
		        	
		        	data.answers[1].answer = data.answerTwo;
		        	delete data.answerTwo;
		        	
		        	data.answers[2].answer = data.answerThree;
		        	delete data.answerThree;
		        	
		        	data.answers[3].answer = data.answerFour;
		        	delete data.answerFour;
		        	
		        	_.each(data.answers, function(value){
		        		value.selected = false;
		        		value.help = false;
		        	})
		        	
		        	data.answers[parseInt(data.selected) - 1].selected = true;
		        	data.answers[parseInt(data.help) - 1].help = true;
		        	
		            return kendo.stringify(data);
		        }
				
				
			},
			detailInit: function(e){
				
				var kdataSource = new kendo.data.DataSource({
					autoSync: true,
                    transport: {
                        read: "home/answer?question=" + e.data.id,
                        parameterMap: function (data, type) {
                 			//console.log(data)
                 	        if (type !== "read") {	        	
                 	        	
                 	            return kendo.stringify(data);
                 	        }
                     	}   
                        
                    },                        
                    schema: {
                    	total: "total",
                    	data: "data",
                    	model: {
                    		id: "id",
							fields: {
								id: { 
									editable: false,
									nullable: true 
								},
								answer: { 
									type: "string" 
								},
								selected:{
									type: "boolean"
								},
								help: {
									type: "boolean"
								}
						   }
				  	   }, 					  	
				    },
				    change: function(e) {
				    					  				        
				        if(e.field == "selected"){
				        	console.log(this)
				        	console.log(e)
//				        	 var data = this.data();
//				        	var id = e.items[0].id;
//				        	_.each(data, function(value){
//					        	if(value.id != id){
//					        		value[e.field] = false;
//					        	}else{
//					        		value[e.field] = true;
//					        	}
//					        })
				        }
				       
				    },
                    serverPaging: true,
                    serverSorting: true,
                    serverFiltering: true,
                    pageSize: 10,  
				});
				
				$("<div/>").appendTo(e.detailCell).kendoGrid({
                    dataSource: kdataSource,
                    scrollable: false,
                    sortable: true,
                    pageable: true,
                    columns: [
                        { 
                        	field: "answer",
                        	title: "Respuesta"
                        },
                        {
                        	field: "selected",
                        	title: "Correcta"
                        },
                        {
                        	field: "help",
                        	title: "Ayuda"
                        },                        
                        //{ command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }
                    ],
                    editable: true,
                   
                });
			},
			dataBound: function() {
                this.expandRow(this.tbody.find("tr.k-master-row").first());
            },
			columns: [
			    { 
			    	field:"question", 
			    	title: "Nombre" ,
			    	editor: function(container, options){
			    		$('<textarea data-bind="value: ' + options.field + '"></textarea>')
			    		.appendTo(container);
			    	}
			    },
			    { 
			    	field: "category",
			    	title:"Categoria",
			    	width: "150px",
					template:  "#: category.name #", 
					editor:	function (container, options) {
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
			    {
			    	field: "answerOne",			    							    
			    	title: "Respuesta Uno",
			    	hidden: true,
			    	editor:	function (container, options) {
			    		var ans = options.model.answers[0];
			    		//console.log(ans)
			    		$('<input type="text" class="k-input k-textbox" name="' + options.field + '" data-bind="value:' + options.field + '">')
			    		 .appendTo(container)
			    		 .attr("value", ans.answer);
			    	}
			    	
			    },
			    {
			    	field: "answerTwo",
			    	title: "Respuesta Dos",	
			    	hidden: true,
			    	editor:	function (container, options) {
			    		var ans = options.model.answers[1];
			    		$('<input type="text" class="k-input k-textbox" name="' + options.field + '" data-bind="value:' + options.field + '">')
			    		 .appendTo(container)
			    		 .attr("value", ans.answer);
			    	}
			    },
			    {
			    	field: "answerThree",
			    	title: "Respuesta Tres",
			    	hidden: true,
			    	editor:	function (container, options) {
			    		var ans = options.model.answers[2];
			    		$('<input type="text" class="k-input k-textbox" name="' + options.field + '" data-bind="value:' + options.field + '">')
			    		 .appendTo(container)
			    		 .attr("value", ans.answer);
			    	}
			    },
			    {
			    	field: "answerFour",
			    	title: "Respuesta Cuatro",
			    	hidden: true,
			    	editor:	function (container, options) {
			    		var ans = options.model.answers[3];
			    		$('<input type="text" class="k-input k-textbox" name="' + options.field + '" data-bind="value:' + options.field + '">')
			    		 .appendTo(container)
			    		 .val(ans.answer);
			    		
			    		//console.log(ans)
			    	}
			    },
			    {
			    	field: "selected",
			    	title: "Respuesta Correcta",
			    	hidden: true,
			    	editor:	function (container, options) {
						
			    	    $('<input required data-text-field="text" data-value-field="value" data-bind="value:' + options.field + '"/>')
			            .appendTo(container)
			            .kendoDropDownList({
			                autoBind: false,		                
			                dataSource: [
			                    {text: "Respuesta 1", value: "1" },
			                    {text: "Respuesta 2", value: "2"},
			                    {text: "Respuesta 3", value: "3"},
			                    {text: "Respuesta 4", value: "4"},
			                ],
			                dataTextField: "text",
			                dataValueField:"value"
			            });
					},
			    	
			    },
			    {
			    	field: "help",
			    	title: "50/50",
			    	hidden: true,
			    	editor:	function (container, options) {
						
			    	    $('<input required data-text-field="text" data-value-field="value" data-bind="value:' + options.field + '"/>')
			            .appendTo(container)
			            .kendoDropDownList({
			                autoBind: false,		                
			                dataSource: [
			                    {text: "Respuesta 1", value: "1" },
			                    {text: "Respuesta 2", value: "2"},
			                    {text: "Respuesta 3", value: "3"},
			                    {text: "Respuesta 4", value: "4"},
			                ],
			                dataTextField: "text",
			                dataValueField:"value"
			            });
					},
					
			    }			    
			    
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
//						type: "string", 
//						parse: function(rec){
//							console.log(rec)
//							if(_.isObject(rec)){
//								return rec.name;
//							}
//							return rec;
//						}
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
					template:  "#: office.name #", 
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
//						type: "string", 
//						parse: function(rec){
////							console.log(rec)
//							if(_.isObject(rec)){
//								return rec.name;
//							}
//							return rec;
//						},						
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
					template:  "#: tournament.name #", 
					editor:	function DropDownEditor(container, options) {
						//console.log(options)
					    $('<input required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
				        .appendTo(container)
				        .kendoDropDownList({
				            autoBind: false,		                
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

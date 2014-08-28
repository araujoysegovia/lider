var routerManager = Backbone.Router.extend({

	routes: {
		"" : "home",   
		"tournaments" : "tournaments", 
		"questions" : "questions",
		"players" : "players",
		"groups" : "groups",
		"categories" : "categories",
		"offices" : "offices",
		"teams" : "teams",
		"generateTeams": "generateTeams",
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
		  	Inicio: "",
		  	Torneos: "tournament"
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
					template: "#= kendo.toString(kendo.parseDate(enddate, 'yyyy-MM-dd'), 'dd/MM/yyyy') #"
				},
				{ 
					field: "active",
					title: "Activo",
			    	template: function(e){ 			    		
			    		var imgChecked = "<img src='http://soylider.sifinca.net/bundles/lider/images/icon-check.png'/>";
			    		var imgNoChecked = "<img src='http://soylider.sifinca.net/bundles/lider/images/icon-no-check.png'/>"; 
												
						if(e.active == false){
							return imgNoChecked;
						}else{
							return imgChecked;
						}						
					}
				}
			]     
		});
	},

	questions: function(){
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
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
			    		nullable: true,
			    		type: "number"
			    	},
			    	question: { 
			    		type: "string"
			    	},
			    	category: {},	
			    	checked: {
			    		type: "boolean",			    		
			    	},
			    	user: {
			    		editable: false
			    	},
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
			    	help: {},
				    image: {
				    	//type: "string"
				    	editable: false
				    },
			    }
			},
			onReadData: function (e){
				_.each(e.response.data, function(value){
					var ans = value.answers;
					var a = ans[0], b = ans[1], c = ans[2], d = ans[3];
					if(a){
						value["answerOne"] = a.answer;
					}
					
					if(b){
						value["answerTwo"] = b.answer;
					}
					
					if(c){
						value["answerThree"] = c.answer;
					}
					
					if(d){
						value["answerFour"] = d.answer;
					}									
					
					if(a && a.selected){
						value["selected"] = 1;
					}else if(b && b.selected){
						value["selected"] = 2;
					}else if(c && c.selected){
						value["selected"] = 3;
					}else if(d && d.selected){
						value["selected"] = 4;
					}

					if(a && a.help){
						value["help"] = 1;
					}else if(b && b.help){
						value["help"] = 2;
					}else if(c && c.help){
						value["help"] = 3;
					}else if( d && d.help){
						value["help"] = 4;
					}
					
				})
			},
			parameterMap: function (data, type) {
								
		        if(type != "read") {	      
		        	
		        	//console.log(data)
		        	
		        		if(!(_.isEmpty(data.answers))){
		        			data.answers[0].answer = data.answerOne;
				        	data.answers[1].answer = data.answerTwo;
				        	data.answers[2].answer = data.answerThree;
				        	data.answers[3].answer = data.answerFour;
				        	
		        		}else{
		        			data.answers = [];
		        			data.answers.push({answer : data.answerOne});
		        			data.answers.push({answer : data.answerTwo});
		        			data.answers.push({answer : data.answerThree});
		        			data.answers.push({answer : data.answerFour});
		        			
		        		}
			        			        		
		        		delete data.answerOne;
		        		delete data.answerTwo;
		        		delete data.answerThree;
		        		delete data.answerFour;
		        		delete data.user;
		        		
			        	_.each(data.answers, function(value){
			        		
			        		value["selected"] = false;
			        		value["help"] = false;
			        	})
			        	
			        	//console.log(data)
			        	
			        	data.answers[parseInt(data.selected) - 1].selected = true;
			        	data.answers[parseInt(data.help) - 1].help = true;			        				       
         	        	
			            return kendo.stringify(data);		        
		        }
			},
			width: "450px",
			command: [
           		{
			        name: "edit",
			        text: { 
			            edit: "Editar",  // This is the localization for Edit button
			            update: "Actualizar",  // This is the localization for Update button
			            cancel: "Cancelar"  // This is the localization for Cancel button
			        },				        
			    },
			    { 
			        name: "destroy", 
			        text: "Eliminar",				      
			    },
			    {
			    	 text: "Verificar",
			    	 click: function (e) {
			    		 console.log("verificar")
			    		 e.preventDefault();

		                 var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
		                 var id = dataItem.id;		                
		                 					
		                 config = {
				            type: "POST",           
				            url: "home/question/check/"+id,					            
				            contentType: "application/json",
				            dataType: "json",
				            //data: JSON.stringify(param),
							success: function(){
							   question.grid.data('kendoGrid').dataSource.read();
							   question.grid.data('kendoGrid').refresh();
							},
							error: function(){}
		                 }

		                 $.ajax(config);
							
		             } 
			    },
			    {
			    	 text: "Remover imagen",
			    	 click: function (e) {			    	
			    		 e.preventDefault();
			    		 console.log(e)
		                 var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
			    		 console.log(dataItem)
		                 var id = dataItem.id;		                
		                 					
		                 config = {
				            type: "POST",           
				            url: "home/question/image/"+id+"/remove",					            
				            contentType: "application/json",
				            dataType: "json",
				            //data: JSON.stringify(param),
							success: function(){
							   question.grid.data('kendoGrid').dataSource.read();
							   question.grid.data('kendoGrid').refresh();
							},
							error: function(){}
		                 }

		                 $.ajax(config);
							
		             } 
			    }			    
		    ],	        
			detailInit: function(e){
				var grid = null;
				
				if (!(_.isNull(e.data.id))){
					
					var kdataSource = new kendo.data.DataSource({
						//autoSync: true,
						//batch: true,
	                    transport: {
	                        read: "home/answer?question=" + e.data.id,
	                        update: {                        	
	                                url: function (e) {      
	                                	console.log(e)
	                                    return "home/answer/" + e.id;
	                                },
	                                type: "PUT",
	                                contentType: "application/json",
	                                dataType: "json"
	                            },
	                        parameterMap: function (data, type) {
	                 			//console.log(data)
	                 	        if (type !== "read") {	        	
	                 	        	
	                 	        	if(data.category && (_.isString(data.category))){		        		
	            	        			data.category = {
	            			        			id: data.category
	            			        	}		        		
	            		        	}
	                 	        	
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
	
					        if((e.field == "selected" || e.field == "help") && (e.action != "sync")) {
					        	var data = this.data();				        	
					        	var id = e.items[0].id;
					        	_.each(data, function(value){
						        	if(value.id != id){
						        		if(value[e.field] == true){
						        			value[e.field] = false;
						        			value.dirty = true;
						        		}else{
						        			value[e.field] = false;
						        		}
						        		
						        	}else{
						        		value[e.field] = true;
						        	}						        							        	
						        })
						        
						        kdataSource.sync();
						        grid.data('kendoGrid').dataSource.read();
			 					grid.data('kendoGrid').refresh();
			 								 					
			 					question.grid.data('kendoGrid').dataSource.read();
			 					question.grid.data('kendoGrid').refresh();
					        }
					        
					    },
	                    serverPaging: true,
	                    serverSorting: true,
	                    serverFiltering: true,
	                    pageSize: 10,  
					});
					
					var imgChecked = "<img src='http://soylider.sifinca.net/bundles/lider/images/icon-check.png'/>";
		    		var imgNoChecked = "<img src='http://soylider.sifinca.net/bundles/lider/images/icon-no-check.png'/>";
		    		
					grid = $("<div/>").appendTo(e.detailCell).kendoGrid({
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
	                        	title: "Correcta",                         	
	        			    	template: function(e){ 	        			    			        			    		 	        							        						
        							if(e.selected == false){
        								return imgNoChecked;
        							}else{
        								return imgChecked;
        							}
	        						
	        					}
	                        },
	                        {
	                        	field: "help",
	                        	title: "Ayuda",                        	
	        			    	template: function(e){	
        							if(e.help == false){
        								return imgNoChecked;
        							}else{
        								return imgChecked;
        							}	        						
	        					}
	                        },                        
	                        //{ command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }
	                    ],
	                    editable: true,	                   
	                });
				}
			},
//			dataBound: function() {
//                this.expandRow(this.tbody.find("tr.k-master-row").first());
//            },
			columns: [
			    {
			    	field: "id",
			    	title: "Id",
			    	width: "50px"
			    },
			    { 
			    	field:"question", 
			    	title: "Pregunta" ,
			    	editor: function(container, options){
			    		$('<textarea data-bind="value: ' + options.field + '"></textarea>')
			    		.appendTo(container);
			    	}
			    },
				{ 
					field: "category",
					title:"Categoria",					
					template:  "#: category.name #",
					editor:	function (container, options) {
						var input =  $('<input required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
					        .appendTo(container)
					        .kendoDropDownList({
					            autoBind: true,	
					            dataBound: function(e) {
					            	input.data("kendoDropDownList").trigger("change");
					            },
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
					            dataValueField:"id",				            
					        });
					} 
				},	
			    {
			    	field: "checked",
			    	title: "Revisada",
			    	width: "120px",			    	
			    	template: function(e){ 			    	
						if(e.checked == false){
							return "No";
						}else{
							return "Si";
						}					
					}
			    },
			    {
			    	field: "user",
			    	title: "Usuario",
			    	hidden: true,
			    	template:  function (e){				    		
			    		if(e.user){			    			
			    			return e.user.name;
			    		}else{
			    			return "";
			    		}  		 
			    	}
			    },
			    {
			    	field: "answerOne",			    							    
			    	title: "Respuesta Uno",
			    	hidden: true,
			    	editor:	function (container, options) {
			    		var ans = options.model.answers[0];
			    		if (typeof ans != 'undefined'){
			    			$('<input type="text" class="k-input k-textbox" name="' + options.field + '" data-bind="value:' + options.field + '">')
				    		 .appendTo(container)
				    		 .attr("value", ans.answer);
			    		}else{
			    			$('<textarea data-bind="value: ' + options.field + '"></textarea>')
				    		.appendTo(container);
			    		}			    		
			    	}			    	
			    },
			    {
			    	field: "answerTwo",
			    	title: "Respuesta Dos",	
			    	hidden: true,
			    	editor:	function (container, options) {
			    		var ans = options.model.answers[1];
			    		if (typeof ans != 'undefined'){
			    			$('<input type="text" class="k-input k-textbox" name="' + options.field + '" data-bind="value:' + options.field + '">')
				    		 .appendTo(container)
				    		 .attr("value", ans.answer);
			    		}else{
			    			$('<textarea data-bind="value: ' + options.field + '"></textarea>')
				    		.appendTo(container);
			    		}			    		
			    	}
			    },
			    {
			    	field: "answerThree",
			    	title: "Respuesta Tres",
			    	hidden: true,
			    	editor:	function (container, options) {
			    		var ans = options.model.answers[2];
			    		if (typeof ans != 'undefined'){
			    			$('<input type="text" class="k-input k-textbox" name="' + options.field + '" data-bind="value:' + options.field + '">')
				    		 .appendTo(container)
				    		 .attr("value", ans.answer);
			    		}else{
			    			$('<textarea data-bind="value: ' + options.field + '"></textarea>')
				    		.appendTo(container);
			    		}			    		
			    	}
			    },
			    {
			    	field: "answerFour",
			    	title: "Respuesta Cuatro",
			    	hidden: true,
			    	editor:	function (container, options) {
			    		var ans = options.model.answers[3];
			    		if (typeof ans != 'undefined'){
			    			$('<input type="text" class="k-input k-textbox" name="' + options.field + '" data-bind="value:' + options.field + '">')
				    		 .appendTo(container)
				    		 .val(ans.answer);	
			    		}	else{
			    			$('<textarea data-bind="value: ' + options.field + '"></textarea>')
				    		.appendTo(container);
			    		}		    		
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
			    },
				{ 
					field:"image", 
					title: "Imagen" ,
					width: "150px",	
					filterable: false,
					template: function(e){
						var src = 'http://soylider.sifinca.net/bundles/';
						if(_.isEmpty(e.image)){
							src = src + "/bundles/lider/images/none.png";
						}else{
							src = src + "/app.php/image/"+e.image;
						}
						var img = "<div class='img-question'>"+
								     	"<img  data-id='"+e.id+"' src='"+src+"' width = '40px' height= '40px'/>"+
								     	"<input id='input-file-question-"+e.id+"' type='file' style = 'display: none;'/>"+
								     "</div>";

						return img;
					}
				},	    			    
			],	
			dataBound: function(e) {
			    //console.log("dataBound");
			    //console.log(e)
				 this.expandRow(this.tbody.find("tr.k-master-row").first());
				
			    $('.img-question').children("img").click(function(){
			    	
			    	var id = $(this).attr("data-id");
					console.log($(this).attr("data-id"))
					
					//$('.input-file-team')
					
					var input = $(this).parent("div.img-question").children("input");
					
					input.click();
					
					input.change(function(){
						//console.log("change")
						var filename = $(this).val();
						//console.log(filename)
						if(filename){
							
							var formData = new FormData();
							formData.append("imagen", $(this).get(0).files[0]);
							//console.log(formData)
							config = {
					            type: "POST",           
					            url: "home/question/image/"+id,
					            data: formData,
					            contentType: false,
					            processData: false,
								success: function(){
									question.grid.data('kendoGrid').dataSource.read();
									question.grid.data('kendoGrid').refresh();
								},
								error: function(){}
							}

							$.ajax(config);
						}
					});
					
				});
			},

		})	  
	},
  
	players: function() {
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Jugadores: "Player"
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
						//editable: false,
						type: "string" 
					},
					office: {
						//type: "string"
					},
					roles: {						
					},
					team:{ },
					active: {
						type: "boolean"
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
					editable: false,
					title: "Correo" 
				},				
				{ 
					field: "office",
					title:"Oficina",					
					//template:  "#: office.name #",
					template:  function(e){						
						
						if(e.office){
							return e.office.name;
						}
					},					
					editor:	function (container, options) {
						var input =  $('<input required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
					        .appendTo(container)
					        .kendoDropDownList({
					            autoBind: true,	
					            dataBound: function(e) {
					            	input.data("kendoDropDownList").trigger("change");
					            },
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
					            dataValueField:"id",				            
					        });
					} 
				},
				{ 
					field: "roles",
					title:"Role",
					width: "150px",					
					template:  function(e){						
						if(e.roles[0]){
							return e.roles[0].name;
						}
						
					},
					editor:	function (container, options) {
						var input =  $('<input required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
					        .appendTo(container)
					        .kendoDropDownList({
					            autoBind: true,	
					            dataBound: function(e) {
					            	input.data("kendoDropDownList").trigger("change");
					            },
					            dataSource: {		                	
					                transport: {
					                    read: "home/role/"
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
					field: "team",
					title:"Equipo",					
					template:  function(e){						
						if(e.team){
							return e.team.name;
						}
					},					
					editor:	function (container, options) {
						var input =  $('<input required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
					        .appendTo(container)
					        .kendoDropDownList({
					            autoBind: true,	
					            dataBound: function(e) {
					            	input.data("kendoDropDownList").trigger("change");
					            },
					            dataSource: {		                	
					                transport: {
					                    read: "home/team/"
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
					title: "Activo",
					width: "100px",
			    	template: function(e){
						if(e.active == false){
							return "No";
						}else{
							return "Si";
						}																		
					}
				}
			],
	        parameterMap : function (data, type) {
//	        	console.log(type)
//	        	console.log(data)
		        if (type == "create" || type == "update") {	        	
		        	if(data.roles){
		        		data.roles = [{
		        			id: data.roles
		        		}]
		        	}
		        	if(data.office && (_.isString(data.office))){		        		
	        			data.office = {
			        			id: data.office
			        	}		        		
		        	}		        
		        	
		        				        	
		            return kendo.stringify(data);
		        }
			},			
		})
	},

	groups: function() {
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Grupos: "groups"
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
				    tournament: { },				    
				    active: { type: "boolean" }
				}
			},
			detailInit: function(e){				
				var grid = null;
				
				var kdataSource = new kendo.data.DataSource({
					//autoSync: true,
					//batch: true,
                    transport: {
                        read: "home/team?group=" + e.data.id,
                        update: {
                                url: function (e) {            	                                
                                    return "home/team/" + e.id;
                                },
                                type: "PUT",
                                contentType: "application/json",
                                dataType: "json"
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
								name: { 
									editable: false,
									type: "string" 										
								}								
						   }
				  	   }, 					  	
				    },				    
                    serverPaging: true,
                    serverSorting: true,
                    serverFiltering: true,
                    pageSize: 10,  
				});
				
				grid = $("<div/>").appendTo(e.detailCell).kendoGrid({
                    dataSource: kdataSource,
                    scrollable: false,
                    sortable: true,
                    pageable: true,
                    columns: [
                        { 
                        	field: "name",
                        	title: "Grupo",
                        	template: function (e){
                        		console.log(e)
                        		return e.name;
                        	}
                        }                                            
                        //{ command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }
                    ],
                    editable: true,
                   
                });
			},					
			columns: [
				{ 
					field:"name", 
					title: "Nombre" 
				},
				{ 
					field: "tournament",
					title:"Torneo",
					
					template:  "#: tournament.name #",									
					editor:	function (container, options) {
						var input =  $('<input required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
					        .appendTo(container)
					        .kendoDropDownList({
					            autoBind: true,	
					            dataBound: function(e) {
					            	input.data("kendoDropDownList").trigger("change");
					            },
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
					title: "Activo",
					width: "100px",
			    	template: function(e){ 			    		
			    		var imgChecked = "<img src='http://soylider.sifinca.net/bundles/lider/images/icon-check.png'/>";
			    		var imgNoChecked = "<img src='http://soylider.sifinca.net/bundles/lider/images/icon-no-check.png'/>"; 
												
						if(e.active == false){
							return imgNoChecked;
						}else{
							return imgChecked;
						}						
					}
				}
			],
	        parameterMap : function (data, type) {
	        	
		        if (type == "create" || type == "update") {	        	

		        	if(data.tournament && (_.isString(data.tournament))){		        		
	        			data.tournament = {
			        			id: data.tournament
			        	}		        		
		        	}		        
		        	
		        				        	
		            return kendo.stringify(data);
		        }
			}		  	
		});
	},

	categories: function() {
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Categorias: "categories"
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
	
	offices: function() {
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Oficinas: "offices"
		});
		var tournament = new Entity({
			container:  $("#entity-content"),
			url: "home/office/",
			title: "Oficinas",
			model: {
				id: "id",
				fields: {
					id: { editable: false, nullable: true },
				    name: { 
				    	type: "string" 
				    },
				    city: {
				    	type: "string"
				    }				    
				}
			},
			columns: [
				{ 
					field:"name", 
					title: "Nombre" 
				},
				{ 
					field: "city",
					title: "Ciudad"
				},				
			]     
		});
	},
	
	teams: function() {
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		    Equipos: "teams"
		});
		
		var office = new Entity({
			container:  $("#entity-content"),
			url: "home/team/",
			title: "Equipos",
			model: {
				id: "id",
				fields: {
					id: { editable: false, nullable: true },
				    name: { 
				    	type: "string" 
				    },
				    group: {
				    	
				    },
				    image: {
				    	//type: "string"
				    	editable: false
				    },
				    active: {
				    	type: "boolean"
				    }
				}
			},
			detailInit: function(e){
				var grid = null;
				
				var kdataSource = new kendo.data.DataSource({
					//autoSync: true,
					//batch: true,
                    transport: {
                        read: "home/player?team=" + e.data.id,
                        update: {
                                url: function (e) {            	                                
                                    return "home/player/" + e.id;
                                },
                                type: "PUT",
                                contentType: "application/json",
                                dataType: "json"
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
								name: { 
									editable: false,
									type: "string" 										
								}								
						   }
				  	   }, 					  	
				    },				    
                    serverPaging: true,
                    serverSorting: true,
                    serverFiltering: true,
                    pageSize: 10,  
				});
				
				grid = $("<div/>").appendTo(e.detailCell).kendoGrid({
                    dataSource: kdataSource,
                    scrollable: false,
                    sortable: true,
                    pageable: true,
                    columns: [
                        { 
                        	field: "name",
                        	title: "Jugador",
                        	template: function (e){
                        		//console.log(e)
                        		return e.name+" "+e.lastname;
                        	}
                        }                                            
                        //{ command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }
                    ],
                    editable: true,
                   
                });
			},			
			columns: [
				{ 
					field:"image", 
					title: "Imagen" ,
					width: "150px",
					
					template: function(e){
						var src = 'http://10.102.1.22/lider/web';
						if(_.isEmpty(e.image)){
							src = src + "/bundles/lider/images/team.png";
						}else{
							src = src + "/app.php/image/"+e.image;
						}
						var img = "<div class='img-team'>"+
								     	"<img  data-id='"+e.id+"' src='"+src+"' width = '40px' height= '40px'/>"+
								     	"<input id='input-file-team-"+e.id+"' type='file' style = 'display: none;'/>"+
								     "</div>";

						return img;
					}
				},			          
				{ 
					field:"name", 
					title: "Nombre" 
				},
				{ 
					field: "group",
					title:"Grupo",					
					template:  "#: group.name #",
					editor:	function (container, options) {
						var input =  $('<input required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
					        .appendTo(container)
					        .kendoDropDownList({
					            autoBind: true,	
					            dataBound: function(e) {
					            	input.data("kendoDropDownList").trigger("change");
					            },
					            dataSource: {		                	
					                transport: {
					                    read: "home/group/"
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
			],
	        parameterMap : function (data, type) {
	        	
		        if (type == "create" || type == "update") {	        	

		        	if(data.tournament && (_.isString(data.tournament))){		        		
	        			data.tournament = {
			        			id: data.tournament
			        	}		        		
		        	}		        
		        	
		        				        	
		            return kendo.stringify(data);
		        }
			},
			dataBound: function(e) {
			    //console.log("dataBound");
			    //console.log(e)
			    $('.img-team').children("img").click(function(){
			    	
			    	var id = $(this).attr("data-id");
					console.log($(this).attr("data-id"))
					
					//$('.input-file-team')
					
					var input = $(this).parent("div.img-team").children("input");
					
					input.click();
					
					input.change(function(){
						//console.log("change")
						var filename = $(this).val();
						//console.log(filename)
						if(filename){
							
							var formData = new FormData();
							formData.append("imagen", $(this).get(0).files[0]);
							//console.log(formData)
							config = {
					            type: "POST",           
					            url: "home/team/image/"+id,
					            data: formData,
					            contentType: false,
					            processData: false,
								success: function(){
								   office.grid.data('kendoGrid').dataSource.read();
								   office.grid.data('kendoGrid').refresh();
								},
								error: function(){}
							}

							$.ajax(config);
						}
					});
					
				});
			},			
			toolbar: [
  			    { name: "create", text: "Agregar registro" },
  			    { 
  			    	name: "generateGroups",
  			    	template: function(){  			    		
  			    		var btn = '<a class="k-button"  onclick="generateTeam()">Generar</a>';  			    		
  			    		return btn;
  			    	}
  			    }
		  	],
		});
	},	
	
	generateTeams: function(){
		
		this.removeContent();

//		var panel = $('<div class="panel panel-default"></div>');
//		var panelHeading = $('<div class="panel-heading"></div>';
//		var panelBody = $('<div class="panel-body"></div>');
		
		//console.log(data)	
		
		var navBar = $('<nav class="navbar navbar-default" role="navigation">'+						
					 	'<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">'+
						 	'<form class="navbar-form navbar-left" role="search">'+
						        '<div class="form-group">'+						          						         
						          '<input id="max"  type="number" class="form-control" placeholder="Máximo">'+
						        '</div>'+
						        '<div class="form-group">'+						         						          
						          '<input id="min"  type="number" class="form-control" placeholder="Minimo">'+
						        '</div>'+
						        '<button type="submit" id="btn-generate" class="btn btn-default">Generar</button>'+
					        '</form>'+
				        '</div>'+
			        '</nav>');
		
		$("#entity-content").append(navBar);
		
		navBar.find("form").submit(function(e){
			e.preventDefault();
			
			var max = $("#max").val();
			var min = $("#min").val();
			
			parameters = {
					type: "GET",     
		            url: "http://soylider.sifinca.net/admin/home/team/generate?max="+max+"&min="+min,
		            //data: data,
		            contentType: 'application/json',
		            dataType: "json",
		            success: function(data){
		            	//console.log(data)
		            	cities = data['cities'];
		            	_.each(cities, function(value, key){
		            	//	console.log(value.teams)
		            		var panel = $('<div id="panel-'+key+'" class="panel-city">'+
		            						'<div class="panel-heading"><h4>'+key+'</h4><hr></div>'+
		            						'<div class="panel-body">'+
		            						'</div>'+ 
		            					 '</div>');
		            		
		            		$("#entity-content").append(panel);
		            	
		            		teamsCity(value.teams, panel.find("div.panel-body"), value.out);
		            	})
		            },
		            error: function(){},
				};
			
			var ajax = $.ajax(parameters);
			
			//console.log(ajax)
		});
		

		
	}
	
});


$(document).ready(function () {
	var router = new routerManager();
	Backbone.history.start();
});

function generateTeam(){
	
	var router = new routerManager();	
	//router.generateTeams();
	Backbone.history.navigate("generateTeams", true);
}

function teamsCity(teams, content, out){
	
	_.each(teams, function(value, key){
		
		var panel = $('<div class="panel panel-default panel-team" ondrop="drop(event)" ondragover="allowDrop(event)">'+
							'<div class="panel-heading title-team">'+value.name+'</div>'+
							'<div class="panel-body panel-body-team">'+
							'</div>'+ 
					   '</div>');
		
		content.append(panel);

		playersTeam(value.players, panel.find("div.panel-body"));
	});
	
	
	if(out.length > 0){		
		var panelOut = $('<div class="panel panel-default panel-team-out" ondrop="drop(event)" ondragover="allowDrop(event)">'+
							'<div class="panel-heading title-team">Sin equipo</div>'+
							'<div class="panel-body panel-body-team">'+
							'</div>'+ 
					   '</div>');
		
		panelOut.children('.title-team').addClass("team-out");
		content.append(panelOut);

		playersTeam(out, panelOut.find("div.panel-body"));
	}
}

function playersTeam(players, content){
	
	_.each(players, function(value, key){
		if(value){
			var img = value.image;
			if(!img){
				img = 'http://soylider.sifinca.net/bundles/lider/images/avatar.png'
			}
			var panel = $('<div id="player-'+value.id+'" class="panel-player" draggable="true" ondragstart="drag(event)">'+
							'<div class="img-player">'+
								'<img src='+img+'>'+
							'</div>'+
							'<div class="name-player"><p>'+value.name.toLowerCase() +'</p></div>'+
						+'</div>');

			
			content.append(panel);
		}

	})
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("Text", ev.target.id);
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("Text");
    var player = $("div#"+data);
    var target =  $(ev.target);
    //console.log(target)
    if(target.hasClass("panel-body")){
    	$(ev.target).append(player);
    }else{
    	var parent = target.parents("div.panel-body-team");
    	if(parent.length >0 ){
    		parent.append(player)
//    		console.log(parent)
    		
    		var numPlayers = parent[0].childElementCount;
    		console.log(numPlayers)
    		if(numPlayers > 3){
    			console.log(parent.parent().children(".title-team"))
    			var t = parent.parent().children(".title-team");
//    			t.css("background", "red");
    			t.removeClass("team-out");
    			t.addClass("team-error");
    		}
    		
    	}
    }
    
    //ev.target.appendChild(document.getElementById(data));
}

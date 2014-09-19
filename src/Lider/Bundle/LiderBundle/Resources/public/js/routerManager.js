var max, min;

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
		"generateGroups": "generateGroups",
		"reportquestions": "reportquestions",
		"parametersConfig": "parametersConfig",
		"images": "images",
		"sendNotifications": "sendNotifications"
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
			    	selected: {
			    		defaultValue: "1"
			    	},
			    	help: {
			    		defaultValue: "2"
			    	},
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
         	        	
			        	//console.log(data)
			        	if(data.selected == data.help){
			        		alert("La respuesta correcta no puedo ser igual a la de ayuda");
			        		throw "La respuesta correcta no puedo ser igual a la de ayuda";
			        	}

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
						console.log("entro")
			    	    var input = $('<input required data-text-field="text" data-value-field="value" data-bind="value:' + options.field + '"/>')
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
			                dataValueField:"value",
			                // select: function () {
			                // 	input.data("kendoDropDownList").trigger("change");
			                // }
			            });
			            input.data("kendoDropDownList").value("1");
					},			    	
			    },
			    {
			    	field: "help",
			    	title: "50/50",
			    	hidden: true,
			    	editor:	function (container, options) {
						
			    	    var input = $('<input required data-text-field="text" data-value-field="value" data-bind="value:' + options.field + '"/>')
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
			                dataValueField:"value",
			                // select: function (e) {
			                // 	input.data("kendoDropDownList").trigger("change");
			                // }
			            });
			            input.data("kendoDropDownList").value("1");
					},					
			    },
				{ 
					field:"image", 
					title: "Imagen" ,
					width: "150px",	
					filterable: false,
					template: function(e){
						var src = 'http://soylider.sifinca.net';
						if(_.isEmpty(e.image)){
							src = src + "/bundles/lider/images/none.png";
						}else{
							src = src + "/app.php/image/"+e.image;
						}
						var img = "<div class='img-question'>"+
								     	"<img data-id='"+e.id+"' src='"+src+"' width = '40px' height= '40px'/>"+
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
				    image: {				    	
				    	editable: false
				    },
					name: { 
						type: "string",
						validation: { required: true}
					},
					lastname: { 
						type: "string",
						validation: { required: true}
					},
					email: { 
						//editable: false,
						type: "string",
						validation: { required: true}
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
					field:"image", 
					title: "Imagen" ,
					width: "150px",					
					template: function(e){
						var src = 'http://10.102.1.22/lider/web';
						if(_.isEmpty(e.image)){
							src = src + "/bundles/lider/images/avatar.png";
						}else{
							src = src + "/app.php/image/"+e.image;
						}
						var img = "<div class='img-player'>"+
								     	"<img  data-id='"+e.id+"' src='"+src+"?width=40&height=40' width = '40px' height= '40px'/>"+
								     	"<input id='input-file-player-"+e.id+"' type='file' style = 'display: none;'/>"+
								     "</div>";

						return img;
					}
				},	
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
			dataBound: function(e) {
			    //console.log("dataBound");
			    //console.log(e)
			    $('.img-player').children("img").click(function(){
			    	
			    	var id = $(this).attr("data-id");
					console.log($(this).attr("data-id"))
					
					//$('.input-file-team')
					
					var input = $(this).parent("div.img-player").children("input");
					
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
					            url: "home/player/image/"+id,
					            data: formData,
					            contentType: false,
					            processData: false,
								success: function(){
								   player.grid.data('kendoGrid').dataSource.read();
								   player.grid.data('kendoGrid').refresh();
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
			},
			toolbar: [
  			    { name: "create", text: "Agregar registro" },
  			    { 
  			    	name: "generateGroup",
  			    	template: function(){  			    		
  			    		var btn = '<a class="k-button"  onclick="generateGroup()">Generar</a>';  			    		
  			    		return btn;
  			    	}
  			    }
		  	],	  	
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
		var me = this;
		var team = new Entity({
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
				    tournament: {

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
					field: "tournament",
					title: "Torneo",
					template: function (e) {
						return e.tournament.name;
					},
					editor: function (container, options){
						
						var input =  $('<input id="tournamentField" required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
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
					field: "group",
					title:"Grupo",					
					template:  function (e) {
						return e.group ? e.group.name : "No definido";
					},
					editor:	function (container, options) {
						var sw= true;
						var val = options.model.group.get('id');
						var input =  $('<input id="group-field" required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
					        .appendTo(container)
					        .kendoDropDownList({
					            autoBind: false,	
					            cascadeFrom: "tournamentField",
					            cascadeFromField: "tournament.id",
					            value: options.model.group.get('id'),
					            dataBound: function(e) {
									input.data("kendoDropDownList").trigger("change");
					            },	
					            change: function(){
					            	if(sw){
					            		console.log("Entro: " + val);
					            		this.value(val);
					            		sw = false;
					            	}
					            },				            					         
					            dataSource: {	
					            	serverFiltering: true,	                	
					                transport: {
					                    read: "home/group/",
						                parameterMap: function(data, type) {

						                  if (type == "read") {               

						                    //Modificar filtro(s) kendo para compatibilidad con sifinca (backend)
						                    if(data.filter){
						  
						                        dataFilter = data.filter["filters"];

						                        _.each(dataFilter, function (value, key) {                            

						                            value["property"] = value["field"];                            
						                            value["operator"] = me.validationOperator(value["operator"]);

						                            delete dataFilter[key].field;
						                                                
						                        });
						                        
						                        data.filter = JSON.stringify(dataFilter);

						                    }                                      
						                    return data;
						                  }

						                },
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
								   team.grid.data('kendoGrid').dataSource.read();
								   team.grid.data('kendoGrid').refresh();
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
  			    	name: "generateTeams",
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
						          '<input id="min"  type="number" class="form-control" placeholder="Minimo">'+
						        '</div>'+
						        '<div class="form-group">'+						          						         
						          '<input id="max"  type="number" class="form-control" placeholder="Máximo">'+
						        '</div>'+						        
						        '<button type="submit" id="btn-generate" class="btn btn-default">Generar</button>'+						        
					        '</form>'+		
					        '<ul class="nav navbar-nav navbar-left nav-totales-team">'+
        						'<li>Total de equipos: <label class="total-global-teams">0</label></li>'+
        						'<li>Total de jugadores: <label class="total-global-players">0</label></li>'+
        					'</ul>'+
					        '<ul class="nav navbar-nav navbar-right" style="margin-top:10px; margin-right: 10px;">'+
					        	'<li><button type="button" class="btn btn-success save-popup" >Guardar</button></li>'+
						    '</ul>'+
				        '</div>'+
			        '</nav>');
		
		$("#entity-content").append(navBar);


		

		var content = $("<div></div>");
		$("#entity-content").append(content);

		var tb = null;

		navBar.find("form").submit(function(e){
			e.preventDefault();
			
			max = $("#max").val();
			min = $("#min").val();
						
			tb = new teamBuilder(content, min, max);
		});
		

		var modal = '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'+
						  '<div class="modal-dialog">'+
						    '<div class="modal-content">'+
						      '<div class="modal-header">'+
						        '<button type="button" class="close" data-dismiss="modal">'+
						        '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'+
						        '<h4 class="modal-title" id="myModalLabel">Guardar equipos</h4>'+
						      '</div>'+
						      '<div class="modal-body">'+	
						      	'<form>'+
						      		'<div class="form-group">'+
						      			'<label>Torneo</label>'+
						      			'<select class="form-control select-tournaments"></select>'+
						      		'</div>'+
						      		'<div class="form-group">'+
						      			'<label>Nombre de los equipos(separados por coma)</label>'+
						      			'<textarea class="form-control name-teams" rows="3"></textarea>'+
						      		'</div>'+
						      	'</form>'+
						      '</div>'+
						      '<div class="modal-footer">'+
						        '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>'+
						        '<button type="button" class="btn btn-primary save-teams">Guardar</button>'+
						      '</div>'+
						    '</div>'+
						  '</div>'+
						'</div>';

		//Buscar torneos activos
		navBar.find(".save-popup").click(function () {
			var modalObj = $(modal);
			modalObj.modal("show");

			parameters = {
				type: "GET",     
	            url: "home/tournament/active",		            
	            contentType: 'application/json',
	            dataType: "json",
	            success: function(data){	            		
	            		$(modal).find(".select-tournaments")
	            		var select = $(modal).find(".select-tournaments");	            		
	            		_.each(data, function (value, key) {
	            			var option = $('<option data-team='+value.teams.length+' value='+value.id+'>'+value.name+'</option>');
	            			
	            			$(".select-tournaments").append(option);
	            		});            		
	            },
	            error: function(){},            
			};
			
			$.ajax(parameters);	

			//Guardar equipos generados en BD
			modalObj.find(".save-teams").click(function () {	

				var dataTeam = modalObj.find("select.select-tournaments option:selected").attr("data-team");
				
				if(dataTeam > 0){
					var r = confirm("Este torneo ya tiene equipos registrados.\n Desea remplazarlos");
					if(!r){
						return false;
					}
				}

				if(tb != null){

					var tournament = $(".select-tournaments").val();
					var nameTeams = $(".name-teams").val();				
					
					
					if(typeof nameTeams != 'undefined'){
						var names = nameTeams.split(",");					
					}
					
					var n = names;
					var json = {};

					_.each(tb.cities, function (value, cityKey) {
						city = {
							"name": value.name,
							"teams": {}
						};
						_.each(value.teams, function (v, k) {
							if(n.length > 0){
								var r = Math.random();
								var pos =parseInt(r * n.length)
								if(pos<0)pos++;
								if(pos>(n.length))pos--;
								var name = n[pos];
								if(name.substring(0,1) == " "){
									name = name.substring(1);
								}
								//console.log("Cambio de nombre al equipo "+v.name+" por "+ name +" de la ciudad de " + value.name)	
								v.name = name;

								n.splice(pos, 1);		
							}

							
							var team = {
								"name": v.name,
								"players": {}
							};
							// delete v.city;
							_.each(v.players, function (val, key) {	
								//team['players'].push({"id" : val['id']});			
							 	team['players'][key] = {
							 		"id" : val.id
							 	};
							});

							city['teams'][k] = team;
							//city['teams'].push(team);
						});	
						json[cityKey] = city;
						//json.push(city);
					});

					

					var data = {
						"cities" : json,
						"tournament": tournament
					};

					//console.log(data)

					parameters = {
						type: "POST", 
						data: JSON.stringify(data),
						//data: data,
			            url: "home/team/save",
			            contentType: 'application/json',
			            dataType: "json",
			            success: function(data){
			            	
			            },
			            error: function(){},
					};
											
					var ajax = $.ajax(parameters);										
				}

				modalObj.modal('hide')
			});			
		});


		
	},

	reportquestions: function () {
		
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Categorias: "reportquestions"
		});

		var imgChecked = "<img src='http://soylider.sifinca.net/bundles/lider/images/icon-check.png'/>";
		var imgNoChecked = "<img src='http://soylider.sifinca.net/bundles/lider/images/icon-no-check.png'/>";

		var questionReport = new Entity({
			container:  $("#entity-content"),
			url: "home/question/report",
			title: "Preguntas Reportadas",
			model: {
				id: "id",
				fields: {
					id: { editable: false, nullable: true },
				    reportText: { type: "string" },	
				    player: {},
				    question: {},
				    checked:{
				    	type: "checked"
				    }
				}
			},
			columns: [
				{ 
					field:"reportText", 
					title: "Causa del reporte" 
				},		
				{ 
					field: "player",
					title:"Jugador",										
					template:  function(e){												
						if(e.player){
							return e.player.name+" "+e.player.lastname;
						}
					},									
				},		
				{ 
					field: "question",
					title:"Pregunta",										
					template:  function(e){												
						if(e.question){
							return "<b># "+e.question.questionId+": </b>"+e.question.question;
						}
					},									
				}

			],
			command: [
			    {
			    	 text: "Solucionar",
			    	 click: function (e) {
			    		 console.log("verificar")
			    		 e.preventDefault();

		                 var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
		                 var id = dataItem.id;		                
		                 console.log(id)		
		                 config = {
				            type: "PUT",           
				            url: "home/question/report/solve/" + id,					            
				            contentType: "application/json",
				            dataType: "json",
				            //data: JSON.stringify(param),
							success: function(){
							   questionReport.grid.data('kendoGrid').dataSource.read();
							   questionReport.grid.data('kendoGrid').refresh();
							},
							error: function(){}
		                 }

		                 $.ajax(config);
							
		             } 
			    },			
			],

		});
	},

	/**
	 * Obtener y guardar parametros de configuración
	 */
	parametersConfig: function () {
	
		this.removeContent();		

		var container = $('<div class="panel panel-default parameters"><h4>Par&aacute;metros del Juego</h4><hr/></div>');
		var form = $('<form id="form-params" role="form">'+						  
						  '<div class="form-group col-sm-4">'+
						    '<label for="timeQuestionPractice" class="required">Tiempo de pregunta en practica (<i>segundos</i>)</label>'+
						    '<input type="number" class="form-control" id="timeQuestionPractice" required="required" >'+
						  '</div>'+	
						  '<div class="form-group col-sm-4">'+
						    '<label for="timeQuestionDuel">Tiempo de pregunta en duelo (<i>segundos</i>)</label>'+						  
						    '<input type="number" class="form-control" id="timeQuestionDuel">'+						  
						  '</div>'+
						  '<div class="form-group col-sm-4">'+
						    '<label for="timeGame">Tiempo de juego (<i>d&iacute;as</i>)</label>'+
						    '<input type="number" class="form-control" id="timeGame">'+
						  '</div>'+				
						  '<div class="form-group col-sm-4">'+
						    '<label for="timeDuel">Tiempo de duelo(<i>d&iacute;as</i>)</label>'+
						    '<input type="number" class="form-control" id="timeDuel">'+
						  '</div>'+
						  '<div class="form-group col-sm-4">'+
						    '<label >Mostrar respuesta correcta en modo practica</label>'+						    
					    	'<select id="answerShowPractice" class="form-control">'+
					    		'<option value=true>Si</option>'+
					    		'<option value=false>No</option>'+
					    	'</select>'+
						  '</div>'+
						  '<div class="form-group col-sm-4">'+
						    '<label >Mostrar respuesta correcta en modo juego</label>'+						    
					    	'<select id="answerShowGame" class="form-control">'+
					    		'<option value=true>Si</option>'+
					    		'<option value=false>No</option>'+
					    	'</select>'+
						  '</div>'+							  
						  '<div class="form-group col-sm-12">'+
						  	'<button type="submit" class="btn btn-primary btn-save-parameters">Guardar</button>'+
						  '</div>'+
					 '</form>');

		container.append(form);	
		$("#entity-content").append(container);
		

		//Setear valores de los parametros de configuracion
		parameters = {
			type: "GET", 			
		    url: "params",
	        contentType: 'application/json',
	        dataType: "json",
	        success: function(data){
	        	if(!(_.isNull(data))){
	        		$("#timeQuestionPractice").val(data['gamesParameters']['timeQuestionPractice']);
		        	$("#timeQuestionDuel").val(data['gamesParameters']['timeQuestionDuel'])
		        	$("#timeGame").val(data['gamesParameters']['timeGame'])
		        	$("#timeDuel").val(data['gamesParameters']['timeDuel'])	
		        	$("#answerShowPractice").val(data['gamesParameters']['answerShowPractice'])	
		        	$("#answerShowGame").val(data['gamesParameters']['answerShowGame'])	
	        	}	        	
	        },
	        error: function(){},
		};
		$.ajax(parameters);


		var validator = $("#form-params").kendoValidator().data("kendoValidator"),
        status = $(".status");
        
		//Enviar datos de parametrizacion				
		$(".btn-save-parameters").click(function (e) {
					
			e.preventDefault();

		 	if (validator.validate()) {
		           
					var data = {
						"timeQuestionPractice" : $("#timeQuestionPractice").val(),
						"timeQuestionDuel": $("#timeQuestionDuel").val(),
						"timeGame": $("#timeGame").val(),
						"timeDuel": $("#timeDuel").val(),
						"answerShowPractice": $('#answerShowPractice').val(),
						"answerShowGame": $('#answerShowGame').val()
					};	

		            parameters = {
						type: "POST", 
						data: JSON.stringify(data),
					    url: "params",
				        contentType: 'application/json',
				        dataType: "json",
				        success: function(){
				        	alert("Parametros guardados exitosamente");
				        },
				        error: function(){},
					};
					$.ajax(parameters);
		    }
		    else {
				alert("Uno o varios campos no cumplen con el formato")            
		    }					
		});		
	},
	
	/**
	 * Generar y guardar grupos
	 */
	generateGroups: function(){
		
		this.removeContent();

		var navBar = $('<nav class="navbar navbar-default" role="navigation">'+	
					 	'<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">'+
						 	'<form class="navbar-form navbar-left" role="search">'+
						 		'<div class="form-group">'+						         						          
						          '<input id="min"  type="number" class="form-control" placeholder="Minimo">'+
						        '</div>'+
						        '<div class="form-group">'+						          						         
						          '<input id="max"  type="number" class="form-control" placeholder="Máximo">'+
						        '</div>'+						        
						        '<button type="submit" id="btn-generate" class="btn btn-default">Generar</button>'+						        
					        '</form>'+
					         '<ul class="nav navbar-nav navbar-right" style="margin-top:10px; margin-right: 10px;">'+
					        	'<li><button type="button" class="btn btn-success save-popup" >Guardar</button></li>'+
						    '</ul>'+
				        '</div>'+
			        '</nav>');
		
		$("#entity-content").append(navBar);

		var content = $("<div></div>");
		$("#entity-content").append(content);

		var tb = null;

		navBar.find("form").submit(function(e){
			e.preventDefault();
			
			max = $("#max").val();
			min = $("#min").val();
						
			tb = new groupBuilder(content, min, max);
		});
		
		var modal = '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'+
						  '<div class="modal-dialog">'+
						    '<div class="modal-content">'+
						      '<div class="modal-header">'+
						        '<button type="button" class="close" data-dismiss="modal">'+
						        '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'+
						        '<h4 class="modal-title" id="myModalLabel">Guardar grupos</h4>'+
						      '</div>'+
						      '<div class="modal-body">'+	
						      	'<form>'+
						      		'<div class="form-group">'+
						      			'<label>Torneo</label>'+
						      			'<select class="form-control select-tournaments"></select>'+
						      		'</div>'+						      		
						      	'</form>'+
						      '</div>'+
						      '<div class="modal-footer">'+
						        '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>'+
						        '<button type="button" class="btn btn-primary save-groups">Guardar</button>'+
						      '</div>'+
						    '</div>'+
						  '</div>'+
						'</div>';

		//Buscar torneos activos
		navBar.find(".save-popup").click(function () {
			var modalObj = $(modal);
			modalObj.modal("show");

			parameters = {
				type: "GET",     
	            url: "home/tournament/active",		            
	            contentType: 'application/json',
	            dataType: "json",
	            success: function(data){	            		
	            		$(modal).find(".select-tournaments")
	            		var select = $(modal).find(".select-tournaments");	            		
	            		_.each(data, function (value, key) {
	            			var option = $('<option data-team='+value.teams.length+' value='+value.id+'>'+value.name+'</option>');
	            			
	            			$(".select-tournaments").append(option);
	            		});            		
	            },
	            error: function(){},            
			};
			
			$.ajax(parameters);	

			//Guardar equipos generados en BD
			modalObj.find(".save-groups").click(function () {	

				var dataTeam = modalObj.find("select.select-tournaments option:selected").attr("data-team");
				
				if(dataTeam > 0){
					var r = confirm("Este torneo ya tiene equipos registrados.\n Desea remplazarlos");
					if(!r){
						return false;
					}
				}

				if(tb != null){

					var tournament = $(".select-tournaments").val();
				
					var json = {};

					_.each(tb.groups, function (value, gKey) {
						group = {
							"name": value.name,
							"teams": {}
						};
						_.each(value.teams, function (v, k) {
														
							var team = {
								"id": v.id,
								"name": v.name,								
							};
							
							group['teams'][k] = team;
							//city['teams'].push(team);
						});	
						json[gKey] = group;
						//json.push(city);
					});
				
					var data = {
						"groups" : json,
						"tournament": tournament
					};

					parameters = {
						type: "POST", 
						data: JSON.stringify(data),
						//data: data,
			            url: "home/group/save",
			            contentType: 'application/json',
			            dataType: "json",
			            success: function(data){			            	
			            },
			            error: function(){},
					};
											
					var ajax = $.ajax(parameters);										
				}
				modalObj.modal('hide')
			});			
		});
	},	

	/**
	 * Gurdar imagenes generales
	 */
	images: function () {

		this.removeContent();
		var container = $('<div class="panel panel-default panel-imagenes"><h4>Subir im&aacute;genes</h4><hr/></div>');
		var form = $('<form id="form-images" role="form">'+
						  '<div class="form-group">'+
						    '<label  class="required">Nombre</label>'+
						    '<input type="text" class="form-control" id="image-name" required="required" >'+
						  '</div>'+							  
						  '<div class="form-group">'+
						    '<label  class="required">Im&aacute;gen</label>'+
						    '<input type="file" class="form-control" id="file-image" required="required" >'+
						  '</div>'+							  				  		 
						  '<button type="submit" class="btn btn-primary btn-save-image">Guardar</button>'+
					 '</form>');

		container.append(form);	
		$("#entity-content").append(container);

		var well = $("<div></div>");
		container.append(well);
		//Guardar imagen en BD mongo
		form.submit(function (e) {		
			e.preventDefault();	
			var img = form.find('#file-image');
			var imageName = form.find('#image-name').val();			
			var file = img[0].files[0];

			var formData = new FormData();
			formData.append("name", imageName);
			formData.append("image", file);

			parameters = {
				type: "POST", 
				data: formData,
			    url: "image",
		        contentType: false,	  
		        processData: false,      
		        success: function(data){	 		        	
		        	//alert("Imagen gurdada exitosamente: "+data.id);
		        	// var well = container.find(".well");
		        	well.empty();
					var r = '<div class="well">'+
								'<p>Imagen gurdada exitosamente: '+data.id+
							'</div>';

					well.append(r);
		        },
		        error: function(){},
			};

			$.ajax(parameters);
		})	
	},

	sendNotifications: function () {

		this.removeContent();
		var container = $('<div class="panel panel-default panel-notifications"><h4>Notificaciones</h4><hr/></div>');
		var form = $('<form id="form-notification" role="form">'+
						  '<div class="form-group">'+
						    '<label  class="col-sm-2 control-label">Notificación de grupos</label>'+
						    '<div class="col-sm-10">'+
						    	'<button type="button" class="btn btn-primary send-not-groups">Enviar</button>'+
						    '</div>'+
						  '</div>'+							  	
						  '<div class="form-group">'+
						    '<label  class="col-sm-2 control-label">Notificación de equipos</label>'+
						    '<div class="col-sm-10">'+
						    	'<button type="button" class="btn btn-primary send-not-teams">Enviar</button>'+
						    '</div>'+
						  '</div>'+							  					  
						  
					 '</form>');

		container.append(form);	
		$("#entity-content").append(container);

		//Enviar notificaciones del grupo
		form.find(".send-not-groups").click(function () {
			$.confirm({
			    text: "Desea enviar una  notificaci&oacute;n a los jugadores informandoles del grupo al que pertenece su equipo ?",
			    confirm: function(button) {
			        parameters = {
						type: "POST", 					
					    url: "notification/group",
				        contentType: 'application/json',
		            	dataType: "json",    
				        success: function(data){	 			        	
				        	alert("Notificaciones enviadas");
				        },
				        error: function(){}
					};

					$.ajax(parameters);
			    },
			    cancel: function(button) {
			        // do something
			    }
			});
		});

		//Enviar notificaciones del equipo
		form.find(".send-not-teams").click(function () {
			$.confirm({
			    text: "Desea enviar una  notificaci&oacute;n a los jugadores informandoles el equipo al que pertenecen ?",
			    confirm: function(button) {
        			parameters = {
						type: "POST", 					
					    url: "notification/team",
				        contentType: 'application/json',
		            	dataType: "json",    
				        success: function(data){	 			        	
				        	alert("Notificaciones enviadas");
				        },
				        error: function(){}
					};

					$.ajax(parameters);
			    },
			    cancel: function(button) {
			        // do something
			    }
			});
		});

		form.submit(function (e) {		
			e.preventDefault();	
		
		})
	},	

	validationOperator: function (operator) {
        
        if(operator == "startswith") {
            operator = "start_with";
        }
        
        if(operator == "eq") {
            operator = "equal";
        }
        
        if(operator == "contains") {
            operator = "has";
        }

        return operator;

    },
});


$(document).ready(function () {
	var router = new routerManager();
	Backbone.history.start();
});

/**
 * Acceder a la ruta para generar los equipos
 */
function generateTeam(){
	
	var router = new routerManager();		
	Backbone.history.navigate("generateTeams", true);
}

function generateGroup(){
	
	var router = new routerManager();		
	Backbone.history.navigate("generateGroups", true);
}


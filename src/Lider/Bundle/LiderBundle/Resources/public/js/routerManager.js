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
		"sendNotifications": "sendNotifications",
		"games": "games",
		"reportPlayerAnalysis": "reportPlayerAnalysis",
		"reportTeamByGroup": "reportTeamByGroup",
		"reportByCategory": "reportByCategory",
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
				    active: { type: "boolean" },
				    enabledLevel: { editable: false, type: "boolean"}
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
				},
				{ 
					field: "enabledLevel",
					title: "Activar Nivel",
					width: 140,
			    	template: function(e){ 			    		
			    		var imgChecked = "<img src='http://soylider.sifinca.net/bundles/lider/images/icon-check.png'/>";
			    		var imgNoChecked = "<img src='http://soylider.sifinca.net/bundles/lider/images/icon-no-check.png'/>"; 
												
						if(e.enabledLevel == false){
							return '<button type="button" class="btn btn-success btn-sm btn-enabled-level">Activar</button>';
						}else{
							return '<button type="button" class="btn btn-success btn-sm" disabled="disabled">Activar</button>';
						}						
					}
				}
			],
			dataBound: function(){
				$('.btn-enabled-level', tournament.grid).on("click", function() {
                    var row = $(this).closest("tr");
                    var grid = tournament.grid.data("kendoGrid");
                    var item = grid.dataItem(row);
                    if(item.level == 1)
                    {
                    	var data = {
							"tournamentId": item.id
						}
						var loader = $(document.body).loaderPanel();
						loader.show();
						var config = {
							type: 'POST',
							url: "home/tournament/enablelevel",							
				            contentType: "application/json",
				            dataType: "json",
				            data: JSON.stringify(data),
	            	     	statusCode: {
						      401:function() { 
						      	window.location = '';
						      }		   
						    },
				            success: function(response){
				            	var n = noty({
						    		text: "Juegos Generados",
						    		timeout: 1000,
						    		type: "success"
						    	});
						    	grid.dataSource.read();
						    	grid.refresh();
				            },
				            error: function(xhr, status, error){
				            	try{
							    	var obj = jQuery.parseJSON(xhr.responseText);
							    	var n = noty({
							    		text: obj.message,
							    		timeout: 1000,
							    		type: "error"
							    	});
						    	}catch(ex){
						    		var n = noty({
							    		text: "Error",
							    		timeout: 1000,
							    		type: "error"
							    	});
						    	}
				            },
				            complete: function(){
				            	loader.hide();
				            }
						}
						$.ajax(config);
                    }
                    else{
                    	var modal = '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'+
									  '<div class="modal-dialog">'+
									    '<div class="modal-content">'+
									      '<div class="modal-header">'+
									        '<button type="button" class="close" data-dismiss="modal">'+
									        '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'+
									        '<h4 class="modal-title" id="myModalLabel">Activar Nivel '+item.level+'</h4>'+
									      '</div>'+
									      '<div class="modal-body">'+	
									      	'<form>'+
									      		'<div class="form-group">'+
									      			'<label>Fecha de inicio del nivel</label>'+
									      			'<input type="date" class="form-control date-level"></input>'+
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
						var modalObj = $(modal);
						modalObj.modal("show");
						modalObj.find(".save-teams").click(function () {})
						var date = new Date();
						var dateString = date.format('Y-m-d');
						modalObj.find('.date-level').attr('min', dateString).attr('value', dateString);
						modalObj.find('.save-teams').click(function(){
							var d = modalObj.find('.date-level').val();
							var data = {
								"date": d,
								"tournamentId": item.id
							}
							var loader = $(document.body).loaderPanel();
							loader.show();
							var config = {
								type: 'POST',
								url: "home/tournament/enablelevel",
					            contentType: "application/json",
					            dataType: "json",
					            data: JSON.stringify(data),
		            	     	statusCode: {
							      401:function() { 
							      	window.location = '';
							      }		   
							    },
					            success: function(response){
					            	var n = noty({
							    		text: "Juegos Generados",
							    		timeout: 1000,
							    		type: "success"
							    	});
							    	grid.dataSource.read();
							    	grid.refresh();
					            },
					            error: function(xhr, status, error){
					            	try{
								    	var obj = jQuery.parseJSON(xhr.responseText);
								    	var n = noty({
								    		text: obj.message,
								    		timeout: 1000,
								    		type: "error"
								    	});
							    	}catch(ex){
							    		var n = noty({
								    		text: "Error",
								    		timeout: 1000,
								    		type: "error"
								    	});
							    	}
					            },
					            complete: function(){
					            	loader.hide();
					            }
							}
							$.ajax(config);
						})
                    }
     //                
                   // action.action.call(me,item);
                })
			}     
		});
	},

	questions: function(){
		var me = this;
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Preguntas: "question"
		});
		var question = new Entity({
		  	container:  $("#entity-content"),
		 	url: "home/question/",
		 	title: "Preguntas",
		 	associationNames: {
		 		category: 'name'
		 	},
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
				    level: {
				    	type: 'string',
				    	editable: false
				    }
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
		        }else{
	                if(data.filter){	                	
	                    dataFilter = data.filter["filters"];

	                    _.each(dataFilter, function (value, key) {                            

	                    	if(question.associationNames[value['field']]){
                    		value["property"] = value["field"]+'.'+question.associationNames[value['field']];	
	                    	}else{
	                    		value["property"] = value["field"];
	                    	}	                                            
	                        value["operator"] = me.validationOperator(value["operator"]);

	                        delete dataFilter[key].field;
	                                            
	                    });
	                    
	                    data.filter = JSON.stringify(dataFilter);

	                }    
	                return data;			        	
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
	            	     	statusCode: {
						      401:function() { 
						      	window.location = '';
						      }		   
						    },				            
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
	            	     	statusCode: {
						      401:function() { 
						      	window.location = '';
						      }		   
						    },				            
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
	                                dataType: "json",
			            	     	statusCode: {
								      401:function() { 
								      	window.location = '';
								      }		   
								    }
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
				{
					field: 'level',
					title: 'Nivel',
					width: '100px'
				}
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
		var me = this;
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Jugadores: "Player"
		});
		var player = new Entity({
			container:  $("#entity-content"),
			url: "home/player/",
			title: "Jugadores",
			associationNames: {
		 		roles: 'name',
		 		team: 'name',
		 		office: 'name'
		 	},
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
		        }else{
	                if(data.filter){	                	
	                    dataFilter = data.filter["filters"];

	                    _.each(dataFilter, function (value, key) {                            

	                    	if(player.associationNames[value['field']]){
                    		value["property"] = value["field"]+'.'+player.associationNames[value['field']];	
	                    	}else{
	                    		value["property"] = value["field"];
	                    	}	                                            
	                        value["operator"] = me.validationOperator(value["operator"]);

	                        delete dataFilter[key].field;
	                                            
	                    });
	                    
	                    data.filter = JSON.stringify(dataFilter);

	                }    
	                return data;			        	
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
		            	     	statusCode: {
							      401:function() { 
							      	window.location = '';
							      }		   
							    },
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
			width: "350px",
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
			    	 text: "Resetear contraseña",
			    	 click: function (e) {			    		
						e.preventDefault();
						mec = this;
						var dataItem = mec.dataItem($(e.currentTarget).closest("tr"));
						var id = dataItem.id;
						$.confirm({
						    text: 'Desea resetear la contraseña de  '+dataItem.name+'?',
						    confirm: function() {
						    			                
													
								config = {
									type: "GET",           
									url: "home/player/password/reset/"+id,					            
									contentType: "application/json",
									dataType: "json",
									//data: JSON.stringify(param),
									statusCode: {
								  		401:function() { 
									  		window.location = '';
									  	}		   
									},				            
									success: function(){
									   player.grid.data('kendoGrid').dataSource.read();
									   player.grid.data('kendoGrid').refresh();
									},
									error: function(xhr, status, error){
						            	try{
									    	var obj = jQuery.parseJSON(xhr.responseText);
									    	var n = noty({
									    		text: obj.message,
									    		timeout: 1000,
									    		type: "error"
									    	});
								    	}catch(ex){
								    		var n = noty({
									    		text: "Error",
									    		timeout: 1000,
									    		type: "error"
									    	});
								    	}
					            	},
								}

								$.ajax(config);
						    },
						    cancel: function(button) {
						        // do something
						    }
						});

						
		    		 
							
		             } 
			    }
			],								
		})
	},

	groups: function() {
		var me = this;
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Grupos: "groups"
		});
		var group = new Entity({
			container:  $("#entity-content"),
			url: "home/group/",
			title: "Grupos",	
			associationNames: {
		 		tournament: 'name'		 		
		 	},		
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
		            	     	statusCode: {
							      401:function() { 
							      	window.location = '';
							      }		   
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
		        }else{
	                if(data.filter){	                	
	                    dataFilter = data.filter["filters"];

	                    _.each(dataFilter, function (value, key) {                            

	                    	if(group.associationNames[value['field']]){
                    		value["property"] = value["field"]+'.'+group.associationNames[value['field']];	
	                    	}else{
	                    		value["property"] = value["field"];
	                    	}	                                            
	                        value["operator"] = me.validationOperator(value["operator"]);

	                        delete dataFilter[key].field;
	                                            
	                    });
	                    
	                    data.filter = JSON.stringify(dataFilter);

	                }    
	                return data;			        	
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
		var me = this;
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
			associationNames: {
		 		tournament: 'name',
		 		group: 'name'	 		
		 	},	
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
                               	statusCode: {
							      401:function() { 
							      	window.location = '';
							      }		   
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
						var val = null;
						if(options.model.group)
							val = options.model.group.get('id');
						
						var input =  $('<input id="group-field" required data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
					        .appendTo(container)
					        .kendoDropDownList({
					            autoBind: false,	
					            cascadeFrom: "tournamentField",
					            cascadeFromField: "tournament.id",
					            value: val,
					            dataBound: function(e) {
									input.data("kendoDropDownList").trigger("change");
					            },	
					            change: function(){
					            	if(sw && val){
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

		        }else{
	                if(data.filter){	                	
	                    dataFilter = data.filter["filters"];

	                    _.each(dataFilter, function (value, key) {                            

	                    	if(team.associationNames[value['field']]){
                    		value["property"] = value["field"]+'.'+team.associationNames[value['field']];	
	                    	}else{
	                    		value["property"] = value["field"];
	                    	}	                                            
	                        value["operator"] = me.validationOperator(value["operator"]);

	                        delete dataFilter[key].field;
	                                            
	                    });
	                    
	                    data.filter = JSON.stringify(dataFilter);

	                }    
	                return data;			        	
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
		            	     	statusCode: {
							      401:function() { 
							      	window.location = '';
							      }		   
							    },					            
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
    	     	statusCode: {
			      401:function() { 
			      	window.location = '';
			      }		   
			    },	            
	            success: function(data){
            		var select = modalObj.find(".select-tournaments");
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
            	     	statusCode: {
					      401:function() { 
					      	window.location = '';
					      }		   
					    },			            
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
			associationNames: {
		 		player: 'name',
		 		question: 'question'	 		
		 	},	
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
			    		 e.preventDefault();

		                var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
		                var id = dataItem.id;		                
		                
						var modal = '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'+
						  '<div class="modal-dialog">'+
						    '<div class="modal-content">'+
						      '<div class="modal-header">'+
						        '<button type="button" class="close" data-dismiss="modal">'+
						        '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'+
						        '<h4 class="modal-title" id="myModalLabel">Solucionar reporte</h4>'+
						      '</div>'+
						      '<div class="modal-body">'+	
						      	'<form>'+
						      		'<div class="form-group">'+
						      			'<label>Descripción</label>'+
						      			'<textarea id="descriptionSolve" class="form-control"></textarea>'+
						      		'</div>'+						      		
						      	'</form>'+
						      '</div>'+
						      '<div class="modal-footer">'+
						        '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>'+
						        '<button type="button" class="btn btn-primary btn-solve-report">Enviar</button>'+
						      '</div>'+
						    '</div>'+
						  '</div>'+
						'</div>';

						var modalObj = $(modal);
						modalObj.modal("show");



						modalObj.find(".btn-solve-report").click(function (e) {
							
							var data = {
								'descriptionSolve' : $('#descriptionSolve').val()
							};

			                config = {
					            type: "PUT",           
					            url: "home/question/report/solve/" + id,					            
					            contentType: "application/json",
					            data: JSON.stringify(data),
					            dataType: "json",				            
		            	     	statusCode: {
									401:function() { 
										window.location = '';
									}		   
							    },
								success: function(){
								   questionReport.grid.data('kendoGrid').dataSource.read();
								   questionReport.grid.data('kendoGrid').refresh();
								   modalObj.modal("hide");
								},
								error: function(){}
			                }

			                $.ajax(config);
		            	});

		               
			
		             } 
			    },			
			],
			toolbar: null
		});
	},


	reportPlayerAnalysis: function () {
		
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Reporte: "reportPlayerAnalysis"
		});

		var imgChecked = "<img src='http://soylider.sifinca.net/bundles/lider/images/icon-check.png'/>";
		var imgNoChecked = "<img src='http://soylider.sifinca.net/bundles/lider/images/icon-no-check.png'/>";

		var reportPlayerAnalysis = new Entity({
			container:  $("#entity-content"),
			url: "home/player/positions",
			title: "Analisis por jugador",
			model: {
				id: "id",
				fields: {
					id: { editable: false, nullable: true },				   
					fullname: {
				    	type: 'string'
				    },
				    teamname: {
				    	type: 'string'
				    },				    
				    total: {
				    	type: 'int'
				    },
				    win: {
				    	type: 'int' 
				    },
				    winHelp: {
				    	type: 'int' 
				    },
				    lost: {
				    	type: 'int' 
				    },
				    totalPoint: {
				    	type: 'int'
				    }					    
				}
			},
			columns: [				
				{ 
					field: "fullname",
					title:"Jugador",
					width: "250px"									
				},
				{
					field: "teamname",
					title:"Equipo"
				},						
				{ 
					field: "total",
					title:"TP"
				},
				{
					field: "win",
					title:"PC"
				},
				{
					field: "winHelp",
					title:"PCA"
				},
				{
					field: "lost",
					title:"PI"
				},	
				{
					field: "totalPoint",
					title:"Puntos"
				}										
			],
			toolbar: [
				 { 
  			    	name: "selectTournament",
  			    	template: function(){  			    		
  			    		var btn = '<select class="form-control select-tournaments-p" style="width: 200px; height: 20px;"></select>';
  			    		return btn;
  			    	}
  			    }
			],
			command: null,			
			serverFiltering: false,
			serverSorting: false,
		    pageable: {
				    refresh: true,
				    pageSizes: false,               
				    buttonCount: 0,
				    info: true,
				    numeric: false,
				    previousNext: false,
				    messages: {
					      display: "Mostrando {2} datos"
					    }
		 	}		
		});

		var select = $(".select-tournaments-p");
		var getParameters = function () {
			select.empty();
			parameters = {
				type: "GET",     
	            url: "home/tournament/active",		            
	            contentType: 'application/json',
	            dataType: "json",
    	     	statusCode: {
			      401:function() { 
			      	window.location = '';
			      }		   
			    },	            
	            success: function(data){

            		select.html('<option value=0>Seleccione un torneo</option>');
            		_.each(data, function (value, key) {            			
            			var s = select.get(0);
            			select.append($('<option>', {
            			 	value: value.id,
            			 	text: value.name
            			}));            		            			
            		});

            		
	            },
	            error: function(){},            
			};
			
			$.ajax(parameters);	
		};

		select.change(function () {
			reportPlayerAnalysis.url = "home/player/positions/"+select.val();
            reportPlayerAnalysis.grid.data('kendoGrid').dataSource.read();		            
			reportPlayerAnalysis.grid.data('kendoGrid').refresh();
        });
		getParameters();
	},


	reportTeamByGroup: function () {
		var me = this;
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Reporte: "reportTeamByGroup"
		});

		var container = $('<div></div>').addClass('panel panel-default');
		var panelHeading = $('<div></div>').addClass('panel-heading');
		var panelBody = $('<div></div>').addClass('panel-body').attr('data-id', 'general');
		var form = $('<form></form>').addClass('form-inline').attr('role', 'form');
		
		var div = $('<div></div>').addClass('form-group');
		var title = $('<h3></h3>').html('Grupos').css({
			float: 'left',
			lineHeight: '0px'
		});
		panelHeading.append(title).append(form.append(form.append(div)));
		container.append(panelHeading).append(panelBody);		
		$("#entity-content").append(container);

		var select = $('<select></select>').addClass('form-control select-tournament');
		var option = $('<option></option>').attr('value', '0').html('Seleccione un torneo');
		var loader = $(document.body).loaderPanel();
		loader.show();
		var configTorunament = {
			type: "GET",
            url: "home/tournament/active",
            contentType: "application/json",
            dataType: "json",
            //data: JSON.stringify(param),
	     	statusCode: {
		      401:function() { 
		      	window.location = '';
		      }		   
		    },            
			success: function(response){
				var data = response;
				select.append(option);
				_.each(data, function(tournament){
					var option = $('<option></option>').attr('value', tournament.id).html(tournament.name);
					select.append(option)
				})
				div.prepend(select);
			},
			error: function(){},
	    	complete: function(){
	    		loader.hide();
	    	}
        }
        $.ajax(configTorunament);


        select.change(function(op){
        	panelBody.empty();
        	var loader1 = $(document.body).loaderPanel();
			loader1.show();
        	var config = {
				type: 'GET',
	            url: 'home/group/positions/'+select.val(),
	            contentType: 'application/json',
	            dataType: 'json',            
		     	statusCode: {
			      401:function() { 
			      	window.location = '';			      	
			      }		   
			    },            
				success: function(response){				
					data = response['data'];
					_.each(data, function (value, key) {
						container = $('<div></div>');
						table = $('<table class="table"></table');
						table.append('<thead>'+
										'<th>Equipo</th>'+
										'<th>PJ</th>'+
										'<th>PG</th>'+
										'<th>PP</th>'+
										'<th>%DG</th>'+
										'<th>%PC</th>'+
										'<th>P</th>'+
									 '</thead>');
						
						_.each(value.teams, function (team) {
							table.append('<tr>'+
										 '<td>'+team.name+'</td>'+
										 '<td>'+team.total+'</td>'+
										 '<td>'+team.win+'</td>'+
										 '<td>'+team.loose+'</td>'+
										 '<td>'+team.duelWin+'%</td>'+
										 '<td>'+team.questionWin+'%</td>'+
										 '<td>'+team.points+'</td>'+										 										 
								         '</tr>');							
						});

						container.append(table);
						
						me.createPanel(value.name, container);
					})
				},
				error: function(){},
				complete: function (argument) {
					loader1.hide();
				} 
	        }
	        $.ajax(config);
		});

	},

	reportByCategory: function () {
		
		console.log("Entroooo")
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Reporte: "reportByCategory"
		});		

		var chart = $('<div id="chart"></div>');
		$('#entity-content').append(chart);


        config = {
            type: 'GET',           
            url: 'home/question/category/report',
            contentType: "application/json",            
            dataType: "json",				            
	     	statusCode: {
				401:function() { 
					window.location = '';
				}		   
		    },
			success: function(response){
			   data = response['data'];
			   console.log(data)
			   var series = [{
			   		name: 'Ganados',
			   		color: '#0a76b9',
			   		data: []
			   },{
			   		name: 'Perdidos',
			   		
			   		data: []
			   }];
			   var categoriesAxis= [];
			   _.each(data, function (value, key) {
			   		
			   		categoriesAxis.push(value['question.categoryName']);
			   		series[0]['data'].push(value['win']);
			   		series[1]['data'].push(value['lost']);
			   })

		        $("#chart").kendoChart({
		            title: {
		                text: "Reporte por Categorias"
		            },
		            legend: {
		                position: "bottom"
		            },
		            seriesDefaults: {
		                type: "column"
		            },
		            series: series,
		            valueAxis: {
		                line: {
		                    visible: false
		                }
		            },
		            categoryAxis: {
		                categories: categoriesAxis,
		                majorGridLines: {
		                    visible: false
		                }
		            },
		            tooltip: {
		                visible: true,
		                format: "{0}"
		            }
		        });		

			},
			error: function(){}
        }

        $.ajax(config);

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
						  '<div class="form-group col-sm-4">'+
						    '<label >Tiempo del duelo extra (desempate)</label>'+						    
					    	'<input type="number" class="form-control" id="timeDuelExtra">'+
						  '</div>'+
						  '<div class="form-group col-sm-4">'+
						    '<label >Cantidad de preguntas para el duelo</label>'+						    
					    	'<input type="number" class="form-control" id="countQuestionDuel">'+
						  '</div>'+	
						  '<div class="form-group col-sm-4">'+
						    '<label >Cantidad de preguntas para el duelo extra</label>'+						    
					    	'<input type="number" class="form-control" id="countQuestionDuelExtra">'+
						  '</div>'+	
						  '<div class="form-group col-sm-4">'+
						    '<label>Puntos por pregunta sin ayuda</label>'+						    
					    	'<input type="number" class="form-control" id="questionPoints">'+
						  '</div>'+	
						  '<div class="form-group col-sm-4">'+
						    '<label>Puntos por pregunta con ayuda</label>'+						    
					    	'<input type="number" class="form-control" id="questionPointsHelp">'+
						  '</div>'+
						  '<div class="form-group col-sm-4">'+
						    '<label>Puntos por juego</label>'+						    
					    	'<input type="number" class="form-control" id="gamePoints">'+
						  '</div>'+
						  '<div class="form-group col-sm-4">'+
						    '<label>Sumar Puntos en Duelo Extra</label>'+						    
					    	'<select id="pointExtraDuel" class="form-control">'+
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
	     	statusCode: {
		      401:function() { 
		      	window.location = '';
		      }		   
		    },	        
	        success: function(data){
	        	if(!(_.isNull(data))){
	        		$("#timeQuestionPractice").val(data['gamesParameters']['timeQuestionPractice']);
		        	$("#timeQuestionDuel").val(data['gamesParameters']['timeQuestionDuel'])
		        	$("#timeGame").val(data['gamesParameters']['timeGame'])
		        	$("#timeDuel").val(data['gamesParameters']['timeDuel'])	
		        	$("#answerShowPractice").val(data['gamesParameters']['answerShowPractice'])	
		        	$("#answerShowGame").val(data['gamesParameters']['answerShowGame'])	
		        	$("#timeDuelExtra").val(data['gamesParameters']['timeDuelExtra'])	
		        	$("#countQuestionDuel").val(data['gamesParameters']['countQuestionDuel'])	
		        	$("#countQuestionDuelExtra").val(data['gamesParameters']['countQuestionDuelExtra'])	
		        	$("#questionPoints").val(data['gamesParameters']['questionPoints'])	
		        	$("#questionPointsHelp").val(data['gamesParameters']['questionPointsHelp'])	
		        	$("#gamePoints").val(data['gamesParameters']['gamePoints'])		        	
		        	$("#pointExtraDuel").val(data['gamesParameters']['pointExtraDuel'])
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
						"answerShowGame": $('#answerShowGame').val(),
						"timeDuelExtra": $("#timeDuelExtra").val(),
						"countQuestionDuel": $("#countQuestionDuel").val(),
						"countQuestionDuelExtra": $("#countQuestionDuelExtra").val(),
						"questionPoints": $("#questionPoints").val(),
						"questionPointsHelp": $("#questionPointsHelp").val(),
						"gamePoints": $("#gamePoints").val(),						
						"pointExtraDuel": $("#pointExtraDuel").val(),
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
			modalObj.on('shown.bs.modal', function(){
				parameters = {
					type: "GET",
		            url: "home/tournament/active",
		            contentType: 'application/json',
		            dataType: "json",
		            success: function(data){
	            		var select = modalObj.find(".select-tournaments");
	            		select.get(0).add(new Option('Seleccione un Torneo', '0'));
	            		_.each(data, function (value, key) {
	            			var option = $('<option data-team='+value.teams.length+' value='+value.id+'>'+value.name+'</option>');
	            			select.append(option);
	            		});
		            },
		            error: function(){},
				};
				
				$.ajax(parameters);
			})
			modalObj.modal("show");

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
            	     	statusCode: {
					      401:function() { 
					      	window.location = '';
					      }		   
					    },			            
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
    	     	statusCode: {
			      401:function() { 
			      	window.location = '';
			      }		   
			    },		        
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

	games: function(){
		var me = this;
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Juegos: "games"
		});
		var container = $('<div></div>').addClass('panel panel-default');
		var panelHeading = $('<div></div>').addClass('panel-heading');
		var panelBody = $('<div></div>').addClass('panel-body').attr('data-id', 'general');
		var form = $('<form></form>').addClass('form-inline').attr('role', 'form');
		
		var div = $('<div></div>').addClass('form-group');
		var title = $('<h3></h3>').html('Juegos').css({
			float: 'left',
			lineHeight: '0px'
		});
		panelHeading.append(title).append(form.append(form.append(div)));
		container.append(panelHeading).append(panelBody);
		$("#entity-content").append(container);
		var select = $('<select></select>').addClass('form-control select-tournament');
		var option = $('<option></option>').attr('value', '0').html('Seleccione un torneo');
		var loader = $(document.body).loaderPanel();
		loader.show();
		var configTorunament = {
			type: "GET",
            url: "home/tournament/active",
            contentType: "application/json",
            dataType: "json",
            //data: JSON.stringify(param),
	     	statusCode: {
		      401:function() { 
		      	window.location = '';
		      }		   
		    },            
			success: function(response){
				var data = response;
				select.append(option);
				_.each(data, function(tournament){
					var option = $('<option></option>').attr('value', tournament.id).html(tournament.name);
					select.append(option)
				})
				div.prepend(select);
			},
			error: function(){},
	    	complete: function(){
	    		loader.hide();
	    	}
        }
        $.ajax(configTorunament);

        select.change(function(op){
        	panelBody.empty();
        	var loader1 = $(document.body).loaderPanel();
			loader1.show();
        	var config = {
				type: "GET",
	            url: "home/game/group/"+select.val(),
	            contentType: "application/json",
	            dataType: "json",
	            //data: JSON.stringify(param),
    	     	statusCode: {
			      401:function() { 
			      	window.location = '';
			      }		   
			    },	            
				success: function(response){
					var data = response.data;
					me.data = data;
					me.viewOne();
				},
				error: function(){},
				complete: function(){
		    		loader1.hide();
		    	}
	        }
	        $.ajax(config);
		})
	},

	viewOne: function(){
		var me = this;
		var groups = me.orderV1();
		_.each(groups, function(group){
			var container = $('<div></div>');
			_.each(group.rounds, function(round, key){
				var fieldset = $('<fieldset></fieldset>').append($('<legend></legend>').html('Ronda '+key+" : "+round.date)).css("padding", "0 20px 40px 0");


				var table = $('<table></table>');
				_.each(round.games, function(game){
					//console.log(game)
					var tr = $('<tr class="tr-game"></tr>').css('cursor', 'pointer').css('margin-top', '10px');
					var status = $('<td style="vertical-align: middle;"><div style="width:5px; height: 50px; margin-top:10px; margin-bottom: 10px;" class="div-game"></div></td>').css('width', '15px');
					tr.append(status);					

					var img1 = $('<td style="vertical-align: middle;"><img class="img-circle" src="image/'+game.team_one.image+'?width=50&height=50"/></td>').css('width', '70px').css('text-align', 'center');
					tr.append(img1);

					var name1 = $('<td style="vertical-align: middle;"><span>'+game.team_one.name+'</span></td>');
					tr.append(name1);

					var vs = $('<td style="vertical-align: middle;">VS</td>').css('width', '50px').css('text-align', 'center');
					tr.append(vs);

					var name2 = $('<td style="vertical-align: middle;"><span>'+game.team_two.name+'</span></td>');
					tr.append(name2);

					var img2 = $('<td style="vertical-align: middle;"><img class="img-circle" src="image/'+game.team_two.image+'?width=50&height=50"/></td>').css('width', '70px').css('text-align', 'center');
					tr.append(img2);

					tr.click(function(){
						me.showDuelFromGame(game);
					});

					if(game.active){
						status.children('div').css('background', '#8BFFA7');
					}
					else if(!game.active && !game.finished){
						status.children('div').css('background', '#E2E2E2');
						status.unbind("click");
					}
					else if(game.finished){
						status.children('div').css('background', '#A0394A');
					}


					table.append(tr);

				});
				fieldset.append(table);
				container.append(fieldset);
			})
			me.createPanel(group.name, container);
		})
	},

	showDuelFromGame: function(game){
		var me = this;
		var loader = $(document.body).loaderPanel();
		loader.show();
		var configTorunament = {
			type: "GET",
            url: "home/duel/game/"+game.id,
            contentType: "application/json",
            dataType: "json",
            //data: JSON.stringify(param),
	     	statusCode: {
		      401:function() { 
		      	window.location = '';
		      }
		    },
			success: function(response){
				var data = response.data;
				var modal = $("<div></div>").addClass("modal fade");
				var modalDialog = $("<div></div>").addClass("modal-dialog").css('width', '900px');
				var modalHeader = $("<div></div>").addClass("modal-header");
				var btnClose = $("<button></button>").attr("type", "button").attr("data-dismiss", "modal").addClass("close");
				var spanClose = $("<span></span>").attr("aria-hidden", "true").html("&times;");
				var spanClose2 = $("<span></span>").addClass("sr-only").html("Close");
				btnClose.append(spanClose).append(spanClose2);
				var titleHeading = $("<h4></h4>").addClass("modal-title").html("Duelos del juego").css('display', 'inline');
				var buttonStop = $('<button class="btn btn-danger"">Cerrar Manualmente</button>').css('margin-left', '40px');
				var buttonSendNotification = $('<button class="btn btn-info"">Notificar a los ganadores</button>').css('margin-left', '40px');
				
				modalHeader.append(btnClose).append(titleHeading);
				if(!game.finished){
					modalHeader.append(buttonStop);
					
					buttonStop.click(function(){
						config = {
								type: "GET",
					            url: "home/game/stop/"+game.id,
					            contentType: "application/json",
					            dataType: "json",
					            //data: JSON.stringify(param),
						     	statusCode: {
							      401:function() { 
							      	window.location = '';
							      }
							    },
								success: function(response){
									var n = noty({
							    		text: response.message,
							    		timeout: 1000,
							    		type: "success"
							    	});
								},
								error: function(xhr, status, error){
					            	try{
								    	var obj = jQuery.parseJSON(xhr.responseText);
								    	var n = noty({
								    		text: obj.message,
								    		timeout: 1000,
								    		type: "error"
								    	});
							    	}catch(ex){
							    		var n = noty({
								    		text: "Error",
								    		timeout: 1000,
								    		type: "error"
								    	});
							    	}
					            },
					        };
					       $.ajax(config);
					});
					
				}else{
					modalHeader.append(buttonSendNotification);
					
					buttonSendNotification.click(function(){
						config = {
								type: "GET",
					            url: "home/game/notification/"+game.id,
					            contentType: "application/json",
					            dataType: "json",
					            //data: JSON.stringify(param),
						     	statusCode: {
							      401:function() {
							      	window.location = '';
							      }
							    },
								success: function(response){
									var n = noty({
							    		text: response.message,
							    		timeout: 1000,
							    		type: "success"
							    	});
								},
								error: function(xhr, status, error){
					            	try{
								    	var obj = jQuery.parseJSON(xhr.responseText);
								    	var n = noty({
								    		text: obj.message,
								    		timeout: 1000,
								    		type: "error"
								    	});
							    	}catch(ex){
							    		var n = noty({
								    		text: "Error",
								    		timeout: 1000,
								    		type: "error"
								    	});
							    	}
					            },
					        };
					       $.ajax(config);
					});
				}
				
				var modalBody = $("<div></div>").addClass("modal-body").css('text-align', 'center');

				var tableDuels = $('<table></table>').css('width', '100%');
				
				var trTeamDuels = $('<thead>'+
									 '<tr>'+
									 
								    	'<td colspan="5" style="vertical-align: middle; text-align: center;"><img class="img-circle" src="image/'+game.team_one.image+'?width=50&height=50"/><br/><h4 style="margin-bottom:40px;">'+game.team_one.name+'</h4></td>'+
								    	
								    	'<td style="vertical-align: middle; width: 50px; text-align: center;"><h4>VS</h4></td>'+
								    	
								    	'<td colspan="5" style="vertical-align: middle; text-align: center;"><img class="img-circle" src="image/'+game.team_two.image+'?width=50&height=50"/><br/><h4 style="margin-bottom:40px;">'+game.team_two.name+'</h4></td>'+

								    '</tr>'+
							   '</thead>');
				tableDuels.append(trTeamDuels);
				var tableExtraDuels = $('<table></table>').css('width', '100%');

				var duels = $("<div></div>").addClass("table-duels").append(tableDuels);
				var extraDuels = $("<div></div>").addClass("table-extra-duels").css({
					"border-top": "solid 1px #CCC",
					"margin-top": "25px"
				}).append(tableExtraDuels);
				var title = $("<h4></h4>").html("Extra Duelos");
				extraDuels.prepend(title);
				var modalContent = $("<div></div>").addClass("modal-content").css('width', '900px');
				modal.append(modalDialog.append(modalContent.append(modalHeader).append(modalBody)));
				$(document.body).append(modal);
				modal.modal("show");
				
				_.each(data, function(duel){
					var p1, p2, pp1, pp2;
					if(duel.player_one.teamId == game.team_one.id){
						p1 = duel.player_one;
						p2 = duel.player_two;
						pp1 = duel.point_one;
						pp2 = duel.point_two;
					}else{
						p1 = duel.player_two;
						p2 = duel.player_one;
						pp1 = duel.point_two;
						pp2 = duel.point_one;
					}
					
					var tr = $('<tr></tr>').css({
						height: '40px',
						'cursor': 'pointer'
					}).addClass('tr-game');

					var status = $('<td class="click-duel" style="vertical-align: middle;"><div style="width:5px; height: 50px; margin-top: 5px; margin-bottom: 5px;" class="div-game"></div></td>').css('width', '15px');
					tr.append(status);

					var img1 = $('<td class="click-duel" style="vertical-align: middle;"><img class="img-circle" src="image/'+p1.image+'?width=50&height=50"/></td>').css('width', '70px').css('text-align', 'center');
					tr.append(img1);

					var name1 = $('<td class="click-duel" style="vertical-align: middle;"><span>'+p1.name.toLowerCase()+' '+p1.lastname.toLowerCase()+'</span></td>');
					tr.append(name1);

					var updatePoint1 = $('<td style="vertical-align: middle;"></td>');		
					var buttonUpdatePoint1 = $('<span class="glyphicon glyphicon-repeat"></span>');

					updatePoint1.append(buttonUpdatePoint1);
					tr.append(updatePoint1);

					buttonUpdatePoint1.click(function () {
						var config = {
									type: "GET",
						            url: "home/player/update/points/"+p1.id+"/"+duel.id,
						            contentType: "application/json",
						            dataType: "json",						            
							     	statusCode: {
								      401:function() { 
								      	window.location = '';
								      }		   
								    },            
									success: function(response){
										modal.modal("hide");
										me.showDuelFromGame(game);
								    	// modal.modal("show");
									},
									error: function(xhr, status, error){
						            	try{
									    	var obj = jQuery.parseJSON(xhr.responseText);
									    	var n = noty({
									    		text: obj.message,
									    		timeout: 1000,
									    		type: "error"
									    	});
								    	}catch(ex){
								    		var n = noty({
									    		text: "Error",
									    		timeout: 1000,
									    		type: "error"
									    	});
								    	}
						            },
							    	complete: function(){
							    		loader.hide();
							    	}
						        }
						        $.ajax(config);						
					});

					var point1 = $('<td class="click-duel" style="vertical-align: middle; width:30px; text-align:center;"><span>'+pp1+'</span></td>');
					tr.append(point1);
					
					var vs = $('<td class="click-duel" style="vertical-align: middle;">VS</td>').css('width', '50px').css('text-align', 'center');
					tr.append(vs);

					var point2 = $('<td class="click-duel" style="vertical-align: middle; width:30px; text-align:center;"><span>'+pp2+'</span></td>');
					tr.append(point2);
					

					var updatePoint2 = $('<td style="vertical-align: middle;"></td>');		
					var buttonUpdatePoint2 = $('<span class="glyphicon glyphicon-repeat"></span>');

					updatePoint2.append(buttonUpdatePoint2);
					tr.append(updatePoint2);

					buttonUpdatePoint2.click(function () {
						var config = {
									type: "GET",
						            url: "home/player/update/points/"+p2.id+"/"+duel.id,
						            contentType: "application/json",
						            dataType: "json",						            
							     	statusCode: {
								      401:function() { 
								      	window.location = '';
								      }		   
								    },            
									success: function(response){		
										modal.modal("hide");
										me.showDuelFromGame(game);
								    	// modal.modal("show");
									},
									error: function(xhr, status, error){
						            	try{
									    	var obj = jQuery.parseJSON(xhr.responseText);
									    	var n = noty({
									    		text: obj.message,
									    		timeout: 1000,
									    		type: "error"
									    	});
								    	}catch(ex){
								    		var n = noty({
									    		text: "Error",
									    		timeout: 1000,
									    		type: "error"
									    	});
								    	}
						            },
							    	complete: function(){
							    		loader.hide();
							    	}
						        }
						        $.ajax(config);						
					});

					var name2 = $('<td class="click-duel" style="vertical-align: middle;"><span>'+p2.name.toLowerCase()+' '+p2.lastname.toLowerCase()+'</span></td>');
					tr.append(name2);

					var img2 = $('<td class="click-duel" style="vertical-align: middle;"><img class="img-circle" src="image/'+p2.image+'?width=50&height=50"/></td>').css('width', '70px').css('text-align', 'center');
					tr.append(img2);

					var status2 = $('<td class="click-duel" style="vertical-align: middle;"><div style="width:5px; height: 50px; margin-top: 5px; margin-bottom: 5px;" class="div-game"></div></td>').css('width', '15px');
					tr.append(status2);

					tr.find(".click-duel").click(function()
					{
						me.showModalByDuel(duel);
					});
					

					if(duel.player_one.teamId == game.team_one.id){
						if(duel['player_one']['questionMissing'] > 0 && !duel['finished'] && duel['active'])
						{
							status.children('div').css('background', '#8BFFA7');
							
						}else{
							status.children('div').css('background', '#A0394A');
						}

						if(duel['player_two']['questionMissing'] > 0 && !duel['finished'] && duel['active'])
						{
							status2.children('div').css('background', '#8BFFA7');
							
						}else{
							status2.children('div').css('background', '#A0394A');
						}

					}else{
						if(duel['player_one']['questionMissing'] > 0 && !duel['finished'] && duel['active'])
						{
							status2.children('div').css('background', '#8BFFA7');
							
						}else{
							status2.children('div').css('background', '#A0394A');
						}

						if(duel['player_two']['questionMissing'] > 0 && !duel['finished'] && duel['active'])
						{
							status.children('div').css('background', '#8BFFA7');
							
						}else{
							status.children('div').css('background', '#A0394A');
						}
	
					}
					if(!duel.extraDuel)
					{
						tableDuels.append(tr);
					}
					else{
						tableExtraDuels.append(tr);
					}
					
					
				});
				modalBody.append(duels).append(extraDuels);

				modal.on("hidden.bs.modal", function(){
		    		modal.remove();
		    	})
			},
			error: function(){},
	    	complete: function(){
	    		loader.hide();
	    	}
        }
        $.ajax(configTorunament);
		
	},
	
	showModalByDuel: function(duel)
	{
		var me = this;
		var loader = $(document.body).loaderPanel();
		loader.show();
		var configTorunament = {
			type: "GET",
	        url: "home/duel/questions/"+duel.id,
	        contentType: "application/json",
	        dataType: "json",
	        //data: JSON.stringify(param),
		    statusCode: {
			   401:function() { 
				   window.location = '';
			   }
			},
			success: function(data){
				loader.hide();
				var modal = $("<div></div>").addClass("modal fade");
				var modalDialog = $("<div></div>").addClass("modal-dialog").css('width', '1200px');
				var modalHeader = $("<div></div>").addClass("modal-header");
				var btnClose = $("<button></button>").attr("type", "button").attr("data-dismiss", "modal").addClass("close");
				var spanClose = $("<span></span>").attr("aria-hidden", "true").html("&times;");
				var spanClose2 = $("<span></span>").addClass("sr-only").html("Close");
				btnClose.append(spanClose).append(spanClose2);
				var titleHeading = $("<h4></h4>").addClass("modal-title").html("Preguntas del duelo").css('display', 'inline');
				
				modalHeader.append(btnClose).append(titleHeading);
				
				var modalBody = $("<div></div>").addClass("modal-body").css('text-align', 'center');

				var tableDuels = $('<table></table>').css('width', '100%');
				
				var trTeamDuels = $('<thead>'+
									 '<tr>'+
								    	'<td colspan="2" style="vertical-align: middle; text-align: center;"><img class="img-circle" src="image/'+data.playerTwo.image+'?width=50&height=50"/><br/><h4>'+data.playerTwo.name+'</h4><button class="btn" style="margin-bottom:40px;" data-id="p1" disabled="disabled">Resetear Duelo</button</td>'+
								    	'<td style="vertical-align: middle; text-align: center;"></td>'+
								    	'<td colspan="2" style="vertical-align: middle; text-align: center;"><img class="img-circle" src="image/'+data.playerOne.image+'?width=50&height=50"/><br/><h4>'+data.playerOne.name+'</h4><button class="btn" style="margin-bottom:40px;" data-id="p2" disabled="disabled">Resetear Duelo</button</td>'+
								    '</tr>'+
							   '</thead>');
				if(data.playerTwo.duel)
				{
					trTeamDuels.find('button[data-id=p1]').addClass('btn-success').attr("disabled", false);
				}
				if(data.playerOne.duel)
				{
					trTeamDuels.find('button[data-id=p2]').addClass('btn-success').attr("disabled", false);
				}
				tableDuels.append(trTeamDuels);
				
				var duels = $("<div></div>").addClass("table-duels").append(tableDuels);
				var modalContent = $("<div></div>").addClass("modal-content").css('width', '1200px');
				modal.append(modalDialog.append(modalContent.append(modalHeader).append(modalBody)));
				$(document.body).append(modal);
				modal.modal("show");
				
				_.each(data.questions, function(question){
					var tr = $('<tr></tr>').css({
						height: '40px',
					}).addClass('tr-game');
					var resetOne = $('<td style="vertical-align: middle;"></td>').css('width', '70px');
					console.log(question.answers);
					if(question.answers && question.answers.playerTwo  && _.isObject(question.answers.playerTwo) && question.answers.playerTwo.answer !== undefined)
					{
						var buttonOne = $('<button>Resetear</button>').addClass('btn');
						if(question.answers.playerTwo.find) buttonOne.addClass('btn-success');
						else buttonOne.addClass('btn-danger');
						buttonOne.click(function(){
							$.confirm({
							    text: "Desea resetear esta pregunta ?",
							    confirm: function(button) {
							    	me.resetQuestion(question.answers.playerTwo.token, duel, modal);
							    },
							    cancel: function(button) {
							        // do something
							    }
							});
						})
						resetOne.append(buttonOne);
					}
					else{
						var buttonOne = $('<button>Resetear</button>').addClass('btn btn-default').attr("disabled", "disabled");
						resetOne.append(buttonOne);
					}
					tr.append(resetOne);
					if(question.answers && question.answers.playerTwo && _.isObject(question.answers.playerTwo) && question.answers.playerTwo.answer !== undefined)
					{
						var answerOne = $('<td style="vertical-align: middle;"><p>'+question.answers.playerTwo.answer+'</p></td>').css('width', '200px').css('text-align', 'center');
						tr.append(answerOne);
					}
					else{
						var answerOne = $('<td style="vertical-align: middle;"><p></p></td>').css('width', '200px').css('text-align', 'center');
						tr.append(answerOne);
					}
					
					
					var q = $('<td style="vertical-align: middle;"><p>'+question.question+'</p></td>').css('width', '400px').css('text-align', 'center');
					tr.append(q);
					if(question.answers && question.answers.playerOne  && _.isObject(question.answers.playerOne) && question.answers.playerOne.answer !== undefined)
					{
						var answerTwo = $('<td style="vertical-align: middle;"><p>'+question.answers.playerOne.answer+'</p></td>').css('width', '200px').css('text-align', 'center');
						tr.append(answerTwo);
					}
					else{
						var answerTwo = $('<td style="vertical-align: middle;"><p></p></td>').css('width', '200px').css('text-align', 'center');
						tr.append(answerTwo);
					}
					
					var resetTwo = $('<td style="vertical-align: middle;"></td>').css('width', '70px');
					
					if(question.answers && question.answers.playerOne  && _.isObject(question.answers.playerOne) && question.answers.playerOne.answer !== undefined)
					{
						var buttonTwo = $('<button>Resetear</button>').addClass('btn');
						if(question.answers.playerOne.find) buttonTwo.addClass('btn-success');
						else buttonTwo.addClass('btn-danger');
						buttonTwo.click(function(){
							$.confirm({
							    text: "Desea resetear esta pregunta ?",
							    confirm: function(button) {
							    	me.resetQuestion(question.answers.playerOne.token, duel, modal);
							    },
							    cancel: function(button) {
							        // do something
							    }
							});
						})
						resetTwo.append(buttonTwo);
					}
					else{
						var buttonTwo = $('<button>Resetear</button>').addClass('btn btn-default').attr("disabled", "disabled");
						resetTwo.append(buttonTwo);
					}

					tr.append(resetTwo);
					tableDuels.append(tr);
				});
				modalBody.append(duels);

				modal.on("hidden.bs.modal", function(){
		    		modal.remove();
		    	})
			},
			error: function(){},
	    	complete: function(){
	    		loader.hide();
	    	}
        }
        $.ajax(configTorunament);
	},

	resetQuestion: function(token, duel, modal)
	{
		var me = this;
		var configReverse = {
			type: "GET",
	        url: "home/question/reverse/"+token,
	        contentType: "application/json",
	        dataType: "json",
	        //data: JSON.stringify(param),
		    statusCode: {
			   401:function() { 
				   window.location = '';
			   }
			},
			success: function(repsonse)
			{
				modal.modal("hide");
				me.showModalByDuel(duel);
			}
		}
		$.ajax(configReverse);
	},

	createPanel: function(title, content){
		var panel = $('<div></div>').addClass('panel panel-info');
		var head = $('<div></div>').addClass('panel-heading').html(title);
		var body = $('<div></div>').addClass('panel-body').append(content);
		panel.append(head).append(body).css({
				float: 'left',
				width:500,
				marginLeft: 20
			});
		$('div[data-id=general]').append(panel);
	},

	orderV1: function(){
		var me = this;
		var group = {};
		_.each(me.data, function(g){
			var round = {};
			_.each(g.games, function(game){
				if(!round[game.round]){
					round[game.round] = {
						"date": game.startdate.date,
						"games": []
					};
				}
				round[game.round]['games'].push(game);
			})
			var temp = {
				"name": g.name,
				"rounds": round
			}
			group[g.id] = temp;
		})
		return group;
	},

	sendNotifications: function () {

		this.removeContent();
		var container = $('<div class="panel panel-default panel-notifications"><h4>Notificaciones</h4><hr/></div>');
		var form = $('<form id="form-notification" role="form">'+
						  '<div class="form-group">'+
						    '<label  class="col-sm-2 control-label">Torneo</label>'+
						    '<div class="col-sm-10">'+
						    	'<select class="form-control select-tournament">'+
						    		'<option value=0>Seleccione Un torneo></option>'+
						    	'</select>'+
						    '</div>'+
						  '</div>'+
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
						  '<div class="form-group">'+
						    '<label  class="col-sm-2 control-label">Notificación de duelos</label>'+
						    '<div class="col-sm-10">'+
						    	'<button type="button" class="btn btn-primary send-not-duels">Enviar</button>'+
						    '</div>'+
						  '</div>'+
					 '</form>');

		container.append(form);
		$("#entity-content").append(container);
		var loader = $(document.body).loaderPanel();
		loader.show();
		var configTorunament = {
			type: "GET",
            url: "home/tournament/active",
            contentType: "application/json",
            dataType: "json",
            //data: JSON.stringify(param),
	     	statusCode: {
		      401:function() { 
		      	window.location = '';
		      }		   
		    },            
			success: function(response){
				var data = response;
				_.each(data, function(tournament){
					var option = $('<option></option>').attr('value', tournament.id).html(tournament.name);
					form.find('.select-tournament').append(option)
				})
			},
			error: function(){},
	    	complete: function(){
	    		loader.hide();
	    	}
        }
        $.ajax(configTorunament);
		//Enviar notificaciones del grupo
		form.find(".send-not-groups").click(function () {
			$.confirm({
			    text: "Desea enviar una  notificaci&oacute;n a los jugadores informandoles del grupo al que pertenece su equipo ?",
			    confirm: function(button) {
			    	var data = {
			    		"tournamentId": form.find('.select-tournament').val()
			    	}
			        parameters = {
						type: "GET",
					    url: "home/group/notification",
				        contentType: 'application/json',
		            	dataType: "json",
		            	data: data,
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
			    	var data = {
			    		"tournamentId": form.find('.select-tournament').val()
			    	}
        			parameters = {
						type: "GET",
					    url: "home/team/notification",
				        contentType: 'application/json',
		            	dataType: "json",
		            	data: data,
            	     	statusCode: {
					      401:function() { 
					      	window.location = '';
					      }		   
					    },		            	
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

		//Enviar notificaciones de los duelos
		form.find(".send-not-duels").click(function () {
			$.confirm({
			    text: "Desea enviar una  notificaci&oacute;n a los jugadores informandoles el duelos que les corresponde ?",
			    confirm: function(button) {
			    	var data = {
			    		"tournamentId": form.find('.select-tournament').val()
			    	}
        			parameters = {
						type: "GET",
					    url: "home/player/notification",
				        contentType: 'application/json',
		            	dataType: "json",
		            	data: data,
            	     	statusCode: {
					      401:function() { 
					      	window.location = '';
					      }		   
					    },		            	
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

// function selectTournament(){
	
// 	var router = new routerManager();
// 	Backbone.history.navigate("selectTournament", true);
// }



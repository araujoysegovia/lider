var max, min;
//var server = 'http://162.209.101.142/';

var server = 'http://lider.sifinca.net/'
//var server = 'http://www.sifinca.net/lider/web/app.php/';
//var serverFolder = 'http://www.sifinca.net/lider/';
var serverFolder = 'http://lider.sifinca.net/';
var parametros = null;
	


var routerManager = Backbone.Router.extend({
	marginTopGame: 10,
	heightGame: 110,
	modalMinHeight : '700px',
	modalMaxHeight : '700px',
	questionFontSize : '70%',
	answerFontSize : '60%',
	socket:null,
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
		"realTime": "realTime",
		"reportPlayerAnalysis": "reportPlayerAnalysis",
		"reportTeamByGroup": "reportTeamByGroup",
		"reportByCategory": "reportByCategory",
		"reportByPractice": "reportByPractice",
		'generateDuel': 'generateDuel',
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

	removeContent: function(value){
		var me = this;
		$("#entity-content").empty();
		if(value == "realTime"){
			//console.log(me.socket);
		}else{
			if(!(_.isNull(me.socket))){
				me.socket.removeListener('question');
			}
<<<<<<< HEAD
			
=======
>>>>>>> 80574c842ff02a7ff141fca6e79404b20b2c87bf
		}
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
			    		var imgChecked = "<img src='"+serverFolder+"web/bundles/lider/images/icon-check.png'/>";
			    		var imgNoChecked = "<img src='"+serverFolder+"web/bundles/lider/images/icon-no-check.png'/>"; 
			    					    		
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
			    		
			    		if(e.enabledLevel == false){
							return '<button type="button" class="btn btn-success btn-sm btn-enabled-level">Activar</button>';
						}else{
							
							if(e.active){
								return '<button type="button" class="btn btn-success btn-sm" >Activar</button>';
							}else{
								return '<button type="button" class="btn btn-success btn-sm" disabled="disabled">Activar</button>';
							}
							
						}
					}
				}
			],
			dataBound: function(){
				$('.btn-enabled-level', tournament.grid).on("click", function() {
                    var row = $(this).closest("tr");
                    var grid = tournament.grid.data("kendoGrid");
                    console.log(grid)
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
                   // action.action.call(me,item);
                })
			},
			toolbar: [
  			    { name: "create", text: "Agregar registro" },
  			    { 
  			    	name: "generateDuel",
  			    	template: function(){  			    		
  			    		var btn = '<a class="k-button"  onclick="generateDuel()">Generar duelos</a>';  			    		
  			    		return btn;
  			    	}
  			    }
		  	],
		});
	},


	generateDuel: function () {

		
		parameters = {
			type: "POST",
            url: "home/tournament/generate/duels",
            contentType: 'application/json',
            dataType: "json",
	     	statusCode: {
		      401:function() { 
		      	window.location = '';
		      }		   
		    },	            
            success: function(data){
        		alert("Duelos generados");
            },
            error: function(){},            
		};
		
		$.ajax(parameters);	
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
			    	dateLastChecked: { 
			    		type: "date", 
			    		parse: parseDate
			    	},
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
			    		editable: false,
			    		type: "number"
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
			        	
			        	//console.log(data.selected)
			        	
			        	if(data.selected){
			        		if(!(_.isObject(data.selected))){
		        			data.answers[parseInt(data.selected) - 1].selected = true;
			        		data.answers[parseInt(data.help) - 1].help = true;	
			        		}
	         	        	
				        	//console.log(data)
				        	if(data.selected == data.help){
				        		alert("La respuesta correcta no puedo ser igual a la de ayuda");
				        		throw "La respuesta correcta no puedo ser igual a la de ayuda";
				        	}
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
		                 var dataItem = this.dataItem($(e.currentTarget).closest("tr"));			    		 
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
					
//					var imgChecked = "<img src='../images/icon-check.png'/>";
//		    		var imgNoChecked = "<img src='../images/icon-no-check.png'/>";
		    		
					var imgChecked = "<img src='"+serverFolder+"web/bundles/lider/images/icon-check.png'/>";
		    		var imgNoChecked = "<img src='"+serverFolder+"web/bundles/lider/images/icon-no-check.png'/>";
					
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
			    	width: "250px",
			    	editor: function(container, options){
			    		$('<textarea data-bind="value: ' + options.field + '"></textarea>')
			    		.appendTo(container);
			    	}
			    },
				{ 
					field: "category",
					title:"Categoria",
					template:  "#: category.name #",
					width: "120px",
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
			    	field: "dateLastChecked",
			    	title: "Ultima revision",
			    	width: "150px",
			    	template: "#= kendo.toString(kendo.parseDate(dateLastChecked, 'yyyy-MM-dd'), 'dd/MM/yyyy') #"
//			    	template: function(e){
//						return 'pro';
//					}
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
						
						//console.log("aca");
						src = null;
						
						if(_.isEmpty(e.image)){
							src = serverFolder+'web/bundles/lider/images/none.png';
							//src = 'web/bundles/lider/images/none.png';
						}else{
							//console.log("adijadjai")
							//src = src + "/app.php/image/"+e.image;
							//src = server+"web/admin/image/"+e.image;
							src = src + "image/"+e.image;
						}
						var img = "<div class='img-question'>"+
								     	"<img data-id='"+e.id+"' src='"+src+"' width = '40px' height= '40px'/>"+
								     	"<input id='input-file-question-"+e.id+"' type='file' style = 'display: none;'/>"+
								     "</div>";

						return img;
					}
				},
				{
					field: "level",
			    	title: "Nivel",
			    	width: "90px"				
			    }
			],			
			serverSorting: false,
			dataBound: function(e) {
			    //console.log("dataBound");
			    //console.log(e)
				 this.expandRow(this.tbody.find("tr.k-master-row").first());
				
			    $('.img-question').children("img").click(function(){
			    	
			    	var id = $(this).attr("data-id");
					
					
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
					active: {
						type: "boolean"
					}
				}
		  	},
			columns: [
				{ 
					field:"image", 
					title: "Imagen" ,
					width: "90px",					
					template: function(e){
						//var src = 'http://10.102.1.22/lider/web';
						var src = server;
						if(_.isEmpty(e.image)){
							src = src + "web/bundles/lider/images/avatar.png";
						}else{
							//src = src + "web/admin/image/"+e.image;
							src = src + "image/"+e.image;
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
					title: "Nombre",
					width: "150px"
				},				
				{ 
					field:"lastname", 
					title: "Apellido",
					width: "150px"
				},
				{ 
					field:"email", 
					editable: false,
					title: "Correo",
					width: "250px"
				},				
				{ 
					field: "office",
					title:"Oficina",					
					width: "150px",
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
					width: "90px",					
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
//				{ 
//					field: "team",
//					title:"Equipo",	
//					
//					template:  function(e){						
//						if(e.team){
//							return e.team.name;
//						}
//					},					
//					editor:	function (container, options) {
//						var input =  $('<input data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '"/>')
//					        .appendTo(container)
//					        .kendoDropDownList({
//					            autoBind: false,
//					            dataBound: function(e) {
//					            	input.data("kendoDropDownList").trigger("change");
//					            },
//					            dataSource: {
//					            	batch: false,	                	
//					                transport: {
//					                    read: "home/team/"
//					                },
//					                schema: {
//					    			  	total: "total",
//					    		    	data: "data",
//					    		        model: {
//					    				    id: "id",
//					    				    fields: {
//					    				    	id: { editable: false, nullable: true },
//					    				        name: { type: "string" },		        				        
//					    				    }
//					    		        }
//					                },
//					            },
//								dataTextField: "name",
//								dataValueField: "id",
//								optionLabel: "Selecciona un equipo"
//					        });
//					} 
//				},
				{ 
					field: "active",
					title: "Activo",
					width: "90px",
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
			    },
			    {
			    	 text: "Notificar",
			    	 click: function (e) {
						e.preventDefault();
						me = this;
						var dataItem = me.dataItem($(e.currentTarget).closest("tr"));
						var id = dataItem.id;
						var modal = $("<div></div>").addClass("modal fade").attr({"tabindex": "-1", "role":"dialog"});
						var modalDialog = $("<div></div>").addClass("modal-dialog");
						var modalContent = $("<div></div>").addClass("modal-content");
						var modalHeader = $("<div></div>").addClass("modal-header");
						var btnClose = $("<button></button>").attr({"type": "button", "data-dismiss": "modal", "aria-label": "Close"}).addClass("Close.");
						var spanClose = $("<span></span>").attr("aria-hidden", "true").html("&times;");
						btnClose.append(spanClose);
						var titleHeading = $("<h4></h4>").addClass("modal-title").html("Notificar a todos los jugadores");
						modalHeader.append(titleHeading);
						var modalBody = $("<div></div>").addClass("modal-body");
						
						var formBody = $("<form></form>");
						
						var divSubject = $("<div></div>").addClass("form-group");
						var labelSubject = $("<label></label>").attr({"for": "subjectMessage"}).html("Asunto");
						var subject = $("<input></input").attr({"type": "text", "placeholder": "Asunto", "id": "subjectMessage"}).addClass("form-control");
						divSubject.append(labelSubject).append(subject);
						
						var divBody = $("<div></div>").addClass("form-group");
						var labelBody = $("<label></label>").attr({"for": "bodyMessage"}).html("Mensaje");
						var body = $("<textarea></textarea").attr({"placeholder": "Mensaje", "id": "bodyMessage", "rows": "5"}).addClass("form-control");
						divBody.append(labelBody).append(body);
						
						formBody.append(divSubject).append(divBody);
						modalBody.append(formBody);

						var modalFooter = $("<div></div>").addClass("modal-footer");
						var btnCancel = $("<button></button>").addClass("btn btn-default").attr("data-dismiss", "modal").html("Cancelar");
						var btnSend = $("<button></button>").addClass("btn btn-primary").html("Enviar");
						modalFooter.append(btnCancel).append(btnSend);

						modal.append(modalDialog.append(modalContent.append(modalHeader).append(modalBody).append(modalFooter)));
						$(document.body).append(modal);
						modal.modal("show");

						btnSend.click(function(){
							var su = subject.val();
							var bo = body.val();
							if(!_.isNull(su) && !_.isNull(bo))
							{
								$.confirm({
								    text: "Desea enviar este mensaje a "+dataItem.name+" "+dataItem.lastname+"?",
								    confirm: function(button) {

								    	var data = {
								    		"player": id,
								    		"subject": su,
								    		"message": bo
								    	}
					        			parameters = {
											type: "GET",
										    url: "home/player/player/notification",
									        contentType: 'application/json',
							            	dataType: "json",
							            	data: data,
					            	     	statusCode: {
										      401:function() { 
										      	window.location = '';
										      }
										    },
									        success: function(data){
									        	modal.modal("hide");
									        },
									        error: function(){}
										};

										$.ajax(parameters);
								    },
								    cancel: function(button) {
								        // do something
								    }
								});
							}
							else{
								alert("Por favor rellene los campos Asunto y Mensaje");
							}
							
						})
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
			    		var imgChecked = "<img src='"+serverFolder+"web/bundles/lider/images/icon-check.png'/>";
			    		var imgNoChecked = "<img src='"+serverFolder+"web/bundles/lider/images/icon-no-check.png'/>"; 
												
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
						var src = server;
						if(_.isEmpty(e.image)){
							src = srcFolder + "web/bundles/lider/images/team.png";
						}else{
							//src = src + "web/admin/image/"+e.image;
							src = src + "image/"+e.image;
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

		var imgChecked = "<img src='../images/icon-check.png'/>";
		var imgNoChecked = "<img src='../images/icon-no-check.png'/>";

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
				    causal: { type: 'string'},
				    checked:{
				    	type: "checked"
				    }
				}
			},
			columns: [
				{ 
					field:"reportText", 
					title: "Causa del reporte",
					width: "100px"
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
				},
				{
					field:"causal", 
					title: "Descripción" 
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

		//var imgChecked = "<img src='http://172.99.68.200/lider/src/Lider/Bundle/Resources/public/images/icon-check.png'/>";
		//var imgNoChecked = "<img src='http://172.99.68.200/lider/src/Lider/Bundle/Resources/public/images/icon-no-check.png'/>";
		var imgChecked = "<img src='"+serverFolder+"web/bundles/lider/images/icon-check.png'/>";
		var imgNoChecked = "<img src='"+serverFolder+"web/bundles/lider/images/icon-no-check.png'/>";
		
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
				    	type: 'number'
				    },
				    win: {
				    	type: 'number' 
				    },
				    percentageCorrect: {
				    	type: 'number'
				    },
				    winHelp: {
				    	type: 'number' 
				    },
				    lost: {
				    	type: 'number' 
				    },
				    percentageIncorrect: {
				    	type: 'number'
				    },
				    totalPoint: {
				    	type: 'number'
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
					field: "percentageCorrect",
					title: "PC %",
					width: "100px",
					template:  "#: percentageCorrect +' %' #",					
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
					field: "percentageIncorrect",
					title: "PIC %",
					width: "100px",
					template:  "#: percentageIncorrect +' %' #",					
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
			command: [],
			// command: [
			//     {
			// 		text: "Verificar",
			// 		click: function (e) {
			// 		 console.log("verificar")
			// 		 e.preventDefault();

			// 		 var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
			// 		 var id = dataItem.id;		                
					 					
			// 		 config = {
			// 		    type: "POST",           
			// 		    url: "home/question/check/"+id,					            
			// 		    contentType: "application/json",
			// 		    dataType: "json",
			// 		    //data: JSON.stringify(param),
			// 		 	statusCode: {
			// 		      401:function() { 
			// 		      	window.location = '';
			// 		      }		   
			// 		    },				            
			// 			success: function(){
			// 			   question.grid.data('kendoGrid').dataSource.read();
			// 			   question.grid.data('kendoGrid').refresh();
			// 			},
			// 			error: function(){}
			// 		 }

			// 		 $.ajax(config);
						
			// 		} 
			//     },
			// ],			
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


	reportByPractice: function () {
		
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Reporte: "reportByPractice"
		});

		var imgChecked = "<img src='http://172.99.68.200/lider/src/Lider/Bundle/Resources/public/images/icon-check.png'/>";
		var imgNoChecked = "<img src='http://172.99.68.200/lider/src/Lider/Bundle/Resources/public/images/icon-no-check.png'/>";

		var reportByPractice = new Entity({
			container:  $("#entity-content"),
			url: "home/player/practice",
			title: "Analisis de jugadores en practica",
			model: {
				id: "id",
				fields: {
					id: { editable: false, nullable: true },
					fullname: {
				    	type: 'string'
				    },			    
				    total: {
				    	type: 'number'
				    },
				    win: {
				    	type: 'number' 
				    },
				    percentageCorrect: {
				    	type: 'number'
				    },
				    winHelp: {
				    	type: 'number' 
				    },
				    lost: {
				    	type: 'number' 
				    },
				    percentageIncorrect: {
				    	type: 'number'
				    }

				}
			},
			columns: [			
				{ 
					field: "fullname",
					title:"Jugador",
					width: "350px"									
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
					field: "percentageCorrect",
					title: "PC %",
					width: "100px",
					template:  "#: percentageCorrect +' %' #",					
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
					field: "percentageIncorrect",
					title: "PI %",
					width: "100px",
					template:  "#: percentageIncorrect +' %' #",
				}								
			],
			toolbar: false,
			command: [],
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
		
		console.log("Entroooo2")
		this.removeContent();
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	Reporte: "reportByCategory"
		});


		var form = $('<form id="form-notification" role="form">'+
					  '<div class="form-group">'+
					    '<label  class="col-sm-2 control-label">Torneo</label>'+
					    '<div class="col-sm-10">'+
					    	'<select class="form-control select-tournament">'+
					    		'<option value=0>Seleccione Un torneo></option>'+
					    	'</select>'+
					    '</div>'+
					  '</div>'+
					'</form>');
		
		var select = form.find(".select-tournament");
		var chart = $('<div id="chart"></div>');
		$('#entity-content').append(form).append(chart);

		select.change(function(){
			if(form.find('.select-tournament').val() != 0){
	        	var data = {"tournament": form.find('.select-tournament').val()};
	        }
	        else{
	        	var data = {};
	        }
	        config = {
	            type: 'GET',
	            url: 'home/question/category/report',
	            contentType: "application/json",
	            dataType: "json",
	            data: data,
		     	statusCode: {
					401:function() { 
						window.location = '';
					}
			    },
				success: function(response){
				   data = response['data'];
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
		})
		var loader = $(document.body).loaderPanel();
		loader.show();
		var configTorunament = {
			type: "GET",
            url: "home/tournament/tournament",
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
			url: "home/parameters/",
	        contentType: 'application/json',
	        dataType: "json",
	     	statusCode: {
		      401:function() { 
		      	window.location = '';
		      }
		    },
	        success: function(response){
	        	
	        	var data = response['data'];
	        	
	        	parametros = data;
	        	
	        	//console.log(data)
	        	
	        	if(!(_.isNull(data))){
	        		
	        		for (i = 0; i < 13; i++){
	        			
	        			if(data[i]['name'] == 'timeQuestionPractice'){
	        				$("#timeQuestionPractice").val(data[i]['value']);
	        			}
	        			
	        			if(data[i]['name'] == 'timeQuestionDuel'){
	        				$("#timeQuestionDuel").val(data[i]['value']);
	        			}
	        			
	        			if(data[i]['name'] == 'timeGame'){
	        				$("#timeGame").val(data[i]['value']);
	        			}
	        			
	        			if(data[i]['name'] == 'timeDuel'){
	        				$("#timeDuel").val(data[i]['value']);
	        			}
	        			
	        			if(data[i]['name'] == 'answerShowPractice'){
	        				$("#answerShowPractice").val(data[i]['value']);
	        			}
	        			
	        			if(data[i]['name'] == 'answerShowGame'){
	        				$("#answerShowGame").val(data[i]['value']);
	        			}
	        			
	        			if(data[i]['name'] == 'timeDuelExtra'){
	        				$("#timeDuelExtra").val(data[i]['value']);
	        			}
	        			
	        			if(data[i]['name'] == 'countQuestionDuel'){
	        				$("#countQuestionDuel").val(data[i]['value']);
	        			}
	        			
	        			if(data[i]['name'] == 'countQuestionDuelExtra'){
	        				$("#countQuestionDuelExtra").val(data[i]['value']);
	        			}
	        			
	        			if(data[i]['name'] == 'questionPoints'){
	        				$("#questionPoints").val(data[i]['value']);
	        			}
	        			
	        			if(data[i]['name'] == 'questionPointsHelp'){
	        				$("#questionPointsHelp").val(data[i]['value']);
	        			}
	        			
	        			if(data[i]['name'] == 'gamePoints'){
	        				$("#gamePoints").val(data[i]['value']);
	        			}
	        			
	        			if(data[i]['name'] == 'pointExtraDuel'){
	        				$("#pointExtraDuel").val(data[i]['value']);
	        			}
	        		}
	        				        	
	        	}
	        	
//	        	if(!(_.isNull(data))){
//	        		$("#timeQuestionPractice").val(data['gamesParameters']['timeQuestionPractice']);
//		        	$("#timeQuestionDuel").val(data['gamesParameters']['timeQuestionDuel'])
//		        	$("#timeGame").val(data['gamesParameters']['timeGame'])
//		        	$("#timeDuel").val(data['gamesParameters']['timeDuel'])
//		        	$("#answerShowPractice").val(data['gamesParameters']['answerShowPractice'])
//		        	$("#answerShowGame").val(data['gamesParameters']['answerShowGame'])
//		        	$("#timeDuelExtra").val(data['gamesParameters']['timeDuelExtra'])
//		        	$("#countQuestionDuel").val(data['gamesParameters']['countQuestionDuel'])
//		        	$("#countQuestionDuelExtra").val(data['gamesParameters']['countQuestionDuelExtra'])
//		        	$("#questionPoints").val(data['gamesParameters']['questionPoints'])
//		        	$("#questionPointsHelp").val(data['gamesParameters']['questionPointsHelp'])
//		        	$("#gamePoints").val(data['gamesParameters']['gamePoints'])
//		        	$("#pointExtraDuel").val(data['gamesParameters']['pointExtraDuel'])
//	        	}
	        },
	        error: function(){},
		};
		$.ajax(parameters);


		var validator = $("#form-params").kendoValidator().data("kendoValidator"),
        status = $(".status");
        
		//Enviar datos de parametrizacion
		$(".btn-save-parameters").click(function (e) {
			//alert('jajajja');	
			
			e.preventDefault();

			

			
			if(parametros){
				
				for (i = 0; i < parametros.length; i++){
									
					var p ={
						'id': parametros[i].id,
						'name': parametros[i].name,
						'value': $("#"+parametros[i].name).val()
					};
					
					var parameters = {
						type: "PUT", 
						data: JSON.stringify(p),
					    //url: "params",
						url: "home/parameters/"+parametros[i]['id'],
				        contentType: 'application/json',
				        dataType: "json",
				        success: function(){
				        	alert("Parametros actualizados exitosamente");
				        },
				        error: function(e){
				        	console.log(e)
				        	
				        	
				        	
				        },
					};
	            
					$.ajax(parameters);
					
				}
				
			}
			
		 	//if (validator.validate()) {

//					var data = {
//						"timeQuestionPractice" : $("#timeQuestionPractice").val(),
//						"timeQuestionDuel": $("#timeQuestionDuel").val(),
//						"timeGame": $("#timeGame").val(),
//						"timeDuel": $("#timeDuel").val(),
//						"answerShowPractice": $('#answerShowPractice').val(),
//						"answerShowGame": $('#answerShowGame').val(),
//						"timeDuelExtra": $("#timeDuelExtra").val(),
//						"countQuestionDuel": $("#countQuestionDuel").val(),
//						"countQuestionDuelExtra": $("#countQuestionDuelExtra").val(),
//						"questionPoints": $("#questionPoints").val(),
//						"questionPointsHelp": $("#questionPointsHelp").val(),
//						"gamePoints": $("#gamePoints").val(),
//						"pointExtraDuel": $("#pointExtraDuel").val(),
//					};
			
			
//			var data = [
//				{	
//					'name': 'timeQuestionPractice',
//					'value':  $("#timeQuestionPractice").val()
//				},
//				{	
//					'name': 'timeQuestionDuel',
//					'value':  $("#timeQuestionDuel").val()
//				},
//				{	
//					'name': 'timeGame',
//					'value':  $("#timeGame").val()
//				},
//				{	
//					'name': 'timeDuel',
//					'value':  $("#timeDuel").val()
//				},
//				{	
//					'name': 'answerShowPractice',
//					'value':  $('#answerShowPractice').val()
//				},
//				{	
//					'name': 'answerShowGame',
//					'value':  $('#answerShowGame').val()
//				},
//				{	
//					'name': 'timeDuelExtra',
//					'value':  $("#timeDuelExtra").val()
//				},
//				{	
//					'name': 'countQuestionDuel',
//					'value':  $("#countQuestionDuel").val()
//				},
//				{	
//					'name': 'countQuestionDuelExtra',
//					'value':  $("#countQuestionDuelExtra").val()
//				},
//				{	
//					'name': 'questionPoints',
//					'value':  $("#questionPoints").val()
//				},
//				{	
//					'name': 'questionPointsHelp',
//					'value':  $("#questionPointsHelp").val()
//				},
//				{	
//					'name': 'gamePoints',
//					'value':  $("#gamePoints").val()
//				},
//				{	
//					'name': 'pointExtraDuel',
//					'value':  $("#pointExtraDuel").val()
//				},
//			];
//
//			for (i = 0; i < 13; i++){
//				
//				var sdata = data[i];
//				
//				var parameters = {
//							type: "POST", 
//							data: JSON.stringify(sdata),
//						    //url: "params",
//							url: "home/parameters/",
//					        contentType: 'application/json',
//					        dataType: "json",
//					        success: function(){
//					        	alert("Parametros guardados exitosamente");
//					        },
//					        error: function(e){
//					        	console.log(e)
//					        	
//					        	
//					        	
//					        },
//						};
//			            
//				$.ajax(parameters);
//				
//			}
		           
//		    }
//		    else {
//				alert("Uno o varios campos no cumplen con el formato")
//		    }
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
		var selectTournament = $('<select></select>').addClass('form-control select-tournament');
		var optionTournament = $('<option></option>').attr('value', '0').html('Seleccione un torneo');
		var selectLevel = $('<select></select>').addClass('form-control select-level').css("margin-left", "15px");
		var optionLevel = $('<option></option>').attr('value', '0').html('Seleccione una vista de torneo');
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
				//console.log(data);
				selectTournament.append(optionTournament);
				selectLevel.append(optionLevel);
				_.each(data, function(tournament){
					var option = $('<option></option>').attr('value', tournament.id).attr("level", tournament.level).html(tournament.name);
					selectTournament.append(option)
				})
				div.prepend(selectTournament);
				div.append(selectLevel);
			},
			error: function(){},
	    	complete: function(){
	    		loader.hide();
	    	}
        }
        $.ajax(configTorunament);

        selectTournament.change(function(op){
        	var option = $(this).find('option:selected');
        	var level = option.attr('level');
        	var optionGroup = $('<option></option>').attr('level', '1').attr('value', option.attr('value')).html('Fase de grupos');
			selectLevel.append(optionGroup);
        	//console.log(level);
        	if(level > 1)
        	{
        		var optionElimination = $('<option></option>').attr('level', '2').attr('value', option.attr('value')).html('Fase eliminatoria');
        		selectLevel.append(optionElimination);
        	}
        	me.callViewOne(panelBody, selectTournament.val());
        	
		})
		selectLevel.change(function(op){
			var option = $(this).find('option:selected');
			if(option.attr('level') == 1)
			{
				me.callViewOne(panelBody, option.attr('value'));
			}
			else if(option.attr('level') == 2)
			{
				me.callViewTwo(panelBody, option.attr('value'));
			}
		})
	},

	callViewOne: function(panelBody, tournament)
	{
		var me = this;
		panelBody.empty();
    	var loader1 = $(document.body).loaderPanel();
		loader1.show();
    	var config = {
			type: "GET",
            url: "home/game/group/"+tournament,
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
	},

	callViewTwo: function(panelBody, tournament)
	{
		var me = this;
		panelBody.empty();
    	var loader1 = $(document.body).loaderPanel();
		loader1.show();
    	var config = {
			type: "GET",
            url: "home/game/elimination/"+tournament,
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
				me.viewTwo();
			},
			error: function(){},
			complete: function(){
	    		loader1.hide();
	    	}
        }
        $.ajax(config);
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

	viewTwo: function()
	{
		var h = 106;
		var a = 20;
		var b = 60;
		var me = this;

		var x = 2*h + a;
		var l = ((x/2) - (h/2)) ;
		_.each(me.data, function(level){

			var container = $('<div class="level-'+level.level+'"></div>');
			var sw = false;
			var cont = 1;
			var contG = 0;
			var swline = false;
			var last = 0;
			_.each(level.game, function(game)
			{
				var mTop = 0;
				var c = level.level - 2;
				var d = level.level - 3;

				if(cont == 1 && sw){
					mTop = b;
				}else{
					if(sw){

						if(level.level > 2){
							//l = (x/2) - (h/2);
							//l = (h + (a/2) - (h/2)) * c + (b * d) + (a * d);
							var l2 = (((x/2) - (h/2)) * c) + (b * d) + (a * d);
							//mTop = 2*l + b;
							//console.log("level: "+level.level+" l: "+l2+" b: "+b)
							mTop = (2*l2 + b);
							
						}else{
							mTop = a;
							cont = 0;
						}
						
						
					}else{
						//Si es el ultimo nivel
						if(level.level  == (me.data.length + 1)){
							
							mTop = (l*d) + (b*(d-1)) + (a*(d-1));							

							if(last == 1){
								if(level.game.length == 2){
								
								_.each(level.game, function (game) {
																		
									if(game['indicator'] == '3rd'){
										
										//Tercer puesto
										var teamLost1 = game['team_one'];
										var teamLost2 = game['team_two'];

										var thirdPlace = $('<div class="game panel" style="position: absolute;"></div>').css({
											'width': '250px',
											'height': '140px',
											'top': '65%',
											'left': '75%',
											'right': 0,
											'bottom': 0,
											'border': '1px solid'
										});	
										
										title = $('<center><b>Tercer puesto</b></center>').css('padding', '5px');
										thirdPlace.append(title);

										teamOne = $('<div></div>').css('margin-top', '5px');	
										teamOneImg = $('<img class="img-circle" src="image/'+teamLost1.image+'?width=45&height=45"/></td>').css('width', '45px').css('margin', '0px 10px');
										teamOneName =  $('<span>'+teamLost1.name+'</span>');

										teamOne.append(teamOneImg);
										teamOne.append(teamOneName);
										thirdPlace.append(teamOne);

										teamTwo = $('<div></div>').css('margin-top', '5px');
										teamTwoImg = $('<img class="img-circle" src="image/'+teamLost2.image+'?width=45&height=45"/></td>').css('width', '45px').css('margin', '0px 10px');
										teamTwoName =  $('<span>'+teamLost2.name+'</span>');

										teamTwo.append(teamTwoImg);
										teamTwo.append(teamTwoName);
										thirdPlace.append(teamTwo);


										container.append(thirdPlace);

										thirdPlace.click(function () {
											me.showDuelFromGame(game);
										});

									}else{
										
										//Final
										finalistOne = game['team_one'];
										finalistTwo = game['team_two']
										var final = $('<div class="game panel" style="position: relative;"></div>').css('width', '250px').css('height', h).css("margin-top", mTop);

										teamOne = $('<div></div>').css('margin-top', '5px');
										teamOneImg = $('<img class="img-circle" src="image/'+finalistOne.image+'?width=45&height=45"/></td>').css('width', '45px').css('margin', '0px 10px');
										teamOneName =  $('<span>'+finalistOne.name+'</span>');

										teamOne.append(teamOneImg);
										teamOne.append(teamOneName);
										final.append(teamOne);

										teamTwo = $('<div></div>').css('margin-top', '5px');
										teamTwoImg = $('<img class="img-circle" src="image/'+finalistTwo.image+'?width=45&height=45"/></td>').css('width', '45px').css('margin', '0px 10px');
										teamTwoName =  $('<span>'+finalistTwo.name+'</span>');

										teamTwo.append(teamTwoImg);
										teamTwo.append(teamTwoName);
										final.append(teamTwo);

										container.append(final);

										final.click(function () {
											me.showDuelFromGame(game);
										});
									}
								});

							}
							}

							last++;							
							//console.log(level.game)
							
							/*winner = $('<div></div>');
							img = $('<img class="img-circle" src="image/'+level.game[0].team_one.image+'?width=45&height=45"/></td>')
										.css('width', '45px');
							winner.append(img);
							container.append(winner);
							*/
						}else{
							if(level.level > 2){
								

								mTop = ((l*c) + (b*d) + (a*d));
							}
						}
					}
				}
				// console.log("cont : "+cont+ " mtop: "+mTop)
				

				if(!(level.level  == (me.data.length + 1))){

					var divGame = $('<div class="game panel" style="position: relative;"></div>').css('width', '250px').css('height', h).css("margin-top", mTop);

					teamOne = $('<div></div>').css('margin-top', '5px');
					teamOneImg = $('<img class="img-circle" src="image/'+game.team_one.image+'?width=45&height=45"/></td>').css('width', '45px').css('margin', '0px 10px');
					teamOneName =  $('<span>'+game.team_one.name+'</span>');

					teamOne.append(teamOneImg);
					teamOne.append(teamOneName);
					divGame.append(teamOne);

					teamTwo = $('<div></div>').css('margin-top', '5px');
					teamTwoImg = $('<img class="img-circle" src="image/'+game.team_two.image+'?width=45&height=45"/></td>').css('width', '45px').css('margin', '0px 10px');
					teamTwoName =  $('<span>'+game.team_two.name+'</span>');

					teamTwo.append(teamTwoImg);
					teamTwo.append(teamTwoName);
					divGame.append(teamTwo);

					var lineHeight = 0;
					if(level.level > 2){
						l = (h + (a/2) - (h/2)) * c + (b * d) + (a * d);
						//console.log(l)
						lineHeight =  l + (b/2) + (h/2);
					}else{
						lineHeight = (a/2) + (h/2);
					}

					if(!(level.level  == (me.data.length + 1))){
						divLineTop = $('<div></div>').css({
							'border-right': 'solid 1px',
							'border-top' : 'solid 1px',
							'position': 'absolute',
							'height': lineHeight,
							'top': (h/2),
							'bottom': '0px',
							'right': '0px',
							'width': '20px'
						});
						divLineDown = $('<div></div>').css({
							'border-right': 'solid 1px',
							'border-bottom' : 'solid 1px',
							'position': 'absolute',
							'height': lineHeight,
							'top': '-'+(lineHeight - h/2)+'px',
							// 'bottom': h/2,
							'right': '0px',
							'width': '20px'
						});
					}				

					divLineJoin = $('<div></div>').css({
						'border-top' : 'solid 1px',
						'position': 'absolute',
						'top': '-'+(lineHeight - h/2)+'px',
						'right': '-10px',
						'width': '10px'
					});

					if(swline){
						divGame.append(divLineJoin);
						if(!(level.level  == (me.data.length + 1))){
							divGame.append(divLineDown);
						}
						swline = false;
					}else{
						if(!(level.level  == (me.data.length + 1))){
							divGame.append(divLineTop);
						}
						
						swline = true;
					}
					container.append(divGame);


					sw = true;
					cont++;
					contG++;
					// var fieldset = $('<fieldset></fieldset>').append($('<legend></legend>').html("Juego "+game.indicator+" : "+ game.startdate.date)).css("padding", "0 20px 40px 0");

					divGame.click(function () {
						me.showDuelFromGame(game);
					});

				}

			});
			
			divPanel = $('<div></div>').css('float', 'left').css('margin-left', '10px');
			divPanel.append(container)
			$('div[data-id=general]').append(divPanel);
			
		});
		
	},
	createPairs: function(players)
	{
		if (players.length % 2 !== 0) players.push('(empty)');
	    var startingGroups = players.length / 2;
	    var levelGroups = [];
	    var currentLevel = Math.ceil(Math.log(startingGroups) / Math.log(2));
	    for (var i = 0; i < startingGroups; i++) {
	    	levelGroups.push(currentLevel + '-' + i);
	    }
	    var totalGroups = [];
	    this.makeLevel(levelGroups, currentLevel, totalGroups, players);
	    return totalGroups;
	},

	makeLevel: function(levelGroups, currentLevel, totalGroups, players) {
		currentLevel--;
		var len = levelGroups.length;
		var parentKeys = [];
		var parentNumber = 0;
		var p = '';
		for (var i = 0; i < len; i++) {
			if (parentNumber === 0) {
				p = currentLevel + '-' + parentKeys.length;
				parentKeys.push(p);
	        }

	        if (players !== null) {
	        	var p1 = players[i*2];
		        var p2 = players[(i*2) + 1];
		        totalGroups.push({ key: levelGroups[i], parent: p, player1: p1, player2: p2, parentNumber: parentNumber });
	        } else {
	        	totalGroups.push({ key: levelGroups[i], parent: p, parentNumber: parentNumber });
	        }

	        parentNumber++;
	        if (parentNumber > 1) parentNumber = 0;
      	}
      	console.log(totalGroups);

      	// after the first created level there are no player names
      	if (currentLevel >= 0) this.makeLevel(parentKeys, currentLevel, totalGroups, null)
    },

    makeModel: function(players) {
    	var me = this;
    	this.myDiagram.model = new go.TreeModel(me.createPairs(players));
    },

    isValidScore: function(textblock, oldstr, newstr) {
    	if (newstr === "" || newstr[0] === '-' || newstr.length > 3) return false;
    	return !isNaN(parseInt(newstr, 10));
    // Here it would also be possible to disallow entering
    // the same score for two players, if that is desired.
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
					
//					console.log(duel);
					if(duel.player_one.teamId == game.team_one.id){
						if(duel['player_one']['questionMissing'] > 0 && !duel['finished'] && duel['active'])
						{
							status.children('div').css('background', '#8BFFA7');
							
						}else if(!duel['finished'] && !duel['active']){
							status.children('div').css('background', '#7E7E7E');
						}
						else
						{
							status.children('div').css('background', '#A0394A');
						}

						if(duel['player_two']['questionMissing'] > 0 && !duel['finished'] && duel['active'])
						{
							status2.children('div').css('background', '#8BFFA7');
							
						}
						else if(!duel['finished'] && !duel['active']){
							status2.children('div').css('background', '#7E7E7E');
						}
						else{
							status2.children('div').css('background', '#A0394A');
						}

					}else{
						if(duel['player_one']['questionMissing'] > 0 && !duel['finished'] && duel['active'])
						{
							status2.children('div').css('background', '#8BFFA7');
							
						}
						else if(!duel['finished'] && !duel['active']){
							status2.children('div').css('background', '#7E7E7E');
						}
						else{
							status2.children('div').css('background', '#A0394A');
						}

						if(duel['player_two']['questionMissing'] > 0 && !duel['finished'] && duel['active'])
						{
							status.children('div').css('background', '#8BFFA7');
							
						}
						else if(!duel['finished'] && !duel['active']){
							status.children('div').css('background', '#7E7E7E');
						}
						else{
							status.children('div').css('background', '#A0394A');
						}
	
					}


						buttonSendEmailDuel = $('<td><button type="button" class="btn btn-info">Notificar</button></td>');

						buttonSendEmailDuel.click(function () {
							
							var config = {
								type: "GET",
					            url: "home/duel/notification/"+duel.id,
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

						tr.append(buttonSendEmailDuel);


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
				var modal = $("<div></div>").addClass("modal fade").css({'overflow':'hidden'});
				var modalDialog = $("<div></div>").addClass("modal-dialog").css('width', '100%');
				//var modalHeader = $("<div></div>").addClass("modal-header");
				var btnClose = $("<button></button>").attr("type", "button").attr("data-dismiss", "modal").addClass("close");
				var spanClose = $("<span></span>").attr("aria-hidden", "true").html("&times;");
				var spanClose2 = $("<span></span>").addClass("sr-only").html("Close");
				btnClose.append(spanClose).append(spanClose2);
				var titleHeading = $("<h4></h4>").addClass("modal-title").html("Preguntas del duelo").css('display', 'inline');

				//modalHeader.append(btnClose).append(titleHeading).append(activeDuel);
				if(!duel.active && !duel.finished)
				{
					var activeDuel = $('<button></button>').addClass('btn btn-success').html('Iniciar').css('margin-left', '15px');
					activeDuel.click(function(){
						$.confirm({
						    text: "Desea Activar este duelo ?",
						    confirm: function(button) {
						    	var configStartDuel = {
									type: "GET",
							        url: "home/duel/start/"+duel.id,
							        contentType: "application/json",
							        dataType: "json",
							        //data: JSON.stringify(param),
								    statusCode: {
									   401:function() { 
										   window.location = '';
									   }
									},
									success: function($data)
									{
										var n = noty({
								    		text: "Duelo Activado",
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
						        }
						         $.ajax(configStartDuel);
						    },
						    cancel: function(button) {
						        // do something
						    }
						});
					})
					
					//modalHeader.append(activeDuel);
				}
				
				var modalBody = $("<div></div>").addClass("modal-body").css('text-align', 'center');

				var tableDuels = $('<table class="table-duel" cellspacing="0" cellpadding="0"></table>').css('width', '100%');
				
				var trTeamDuels = $('<thead>'+
									 '<tr>'+
								    	'<td colspan="4" style="vertical-align: middle; text-align: center;">'+
								    		'<img width="20%" class="img-circle" src="image/'+data.playerTwo.image+'?width=150&height=150"/><br/>'+
								    		'<h4>'+data.playerTwo.name+'</h4>'+
								    		'<label id="playerPointTwo">'+duel.point_two+'</label>'+
								    		//'<button class="btn" style="margin-bottom:40px;" data-id="p1" disabled="disabled">Resetear Duelo</button</td>'+
								    	'<td class="active-duel" style="vertical-align: middle; text-align: center;"></td>'+
								    	'<td colspan="4" style="vertical-align: middle; text-align: center;">'+
								    		'<img width="20%" class="img-circle" src="image/'+data.playerOne.image+'?width=150&height=150"/><br/>'+
								    		'<h4>'+data.playerOne.name+'</h4>'+
								    		'<label id="playerPointOne">'+duel.point_one+'</label>'+
								    		//'<button class="btn" style="margin-bottom:40px;" data-id="p2" disabled="disabled">Resetear Duelo</button</td>'+
								    '</tr>'+
							   '</thead>');
				
				trTeamDuels.find('.active-duel').append(activeDuel);
				if(data.playerOne.duel)
				{
					trTeamDuels.find('button[data-id=p1]').addClass('btn-success').attr("disabled", false);
				}
				if(data.playerOne.duel)
				{
					trTeamDuels.find('button[data-id=p2]').addClass('btn-success').attr("disabled", false);
				}
				tableDuels.append(trTeamDuels);
				
				var duels = $("<div></div>").addClass("table-duels").append(tableDuels);
				var modalContent = $("<div></div>").addClass("modal-content").css({
							'font-size':'200%', 
							'overflow': 'auto',
							//'height': me.modalHeight
							'min-height': me.modalMinHeight,
							'max-height': me.modalMaxHeight});
				//modal.append(modalDialog.append(modalContent.append(modalHeader).append(modalBody)));
				modal.append(modalDialog.append(modalContent.append(modalBody)));
				$(document.body).append(modal);
				modal.modal("show");							  

<<<<<<< HEAD
				me.socket = io.connect('http://10.102.1.22:3000');    
=======
				me.socket = io.connect('http://104.130.28.12:27000');
>>>>>>> 80574c842ff02a7ff141fca6e79404b20b2c87bf

				_.each(data.questions, function(question){


					var tr = $('<tr id="'+question['questionId']+'"></tr>').css({
//						height: '100px',
					}).addClass('tr-game');

					tdTimeOne = $('<td style="vertical-align: middle;" id="timeOne'+question['questionId']+'" class="timeQuestion"><label></label></td>').css('width', '50px');
					tr.append(tdTimeOne);					

					tdTimeTwo = $('<td style="vertical-align: middle;" id="timeTwo'+question['questionId']+'" class="timeQuestion"><label></label></td>').css('width', '50px');
					

					spanHelpOne = $('<td ><span id="helpOne'+question['questionId']+'" class="glyphicon glyphicon-adjust" style="display: none; color: #428BCA"></span></td>').css('width', '50px');
					
					tr.append(spanHelpOne);
					
					spanHelpTwo = $('<td><span id="helpTwo'+question['questionId']+'" class="glyphicon glyphicon-adjust" style="display: none; color: #428BCA"></span></td>').css('width', '50px');
					
					
					
					me.socket.on('time', function(time, user, qt){
						
						user = jQuery.parseJSON(user);
						
						if(user['id'] == data.playerOne.id || user['id'] == data.playerTwo.id){
																			
							if(qt['id'] == question['questionId']){

								if(user['id'] == data.playerOne.id){
									$('#timeTwo'+question['questionId']).children('label').text(time);	
								}else if(user['id'] == data.playerTwo.id){									
									$('#timeOne'+question['questionId']).children('label').text(time);
										
								}
								
							}
							
						}
						
					});					

					me.socket.on('help', function(help, user, questionId){
						
						
						user = jQuery.parseJSON(user);
						
						if(user['id'] == data.playerOne.id || user['id'] == data.playerTwo.id){
							
													
							//console.log(questionId +' =='+ question['questionId'])
							if(questionId == question['questionId']){
								
								if(user['id'] == data.playerOne.id){
									sh = $('#helpTwo'+question['questionId']);									
									sh.css('display', 'block');
									
								}else if(user['id'] == data.playerTwo.id){								
									
									sh = $('#helpOne'+question['questionId']);
									sh.css('display', 'block');
										
								}
								
							}
						}
//						spanHelp = $('<span class="glyphicon glyphicon-adjust"></span>').css({'color': '#428BCA'});
						
					});


					var resetOne = $('<td style="vertical-align: middle;"></td>').css('width', '70px');
					
					if(question.answers && question.answers.playerTwo  && _.isObject(question.answers.playerTwo) && question.answers.playerTwo.answer !== undefined)
					{
//						var buttonOne = $('<button></button>').addClass('btn').css({
//							'width': '90%',
//							'height': '40%',
//							'border': 'none',
//							'font-size': me.questionFontSize,
//							'background': 'none'
//						});
						
						var buttonOne = $('<span></span>').css({						
							'font-size': me.questionFontSize,						
						});						
						
						console.log("QUESTION")
						console.log(question)

						if(question.answers.playerTwo.find){
							//buttonOne.addClass('btn-success');
							
							buttonOne.css({'background': 'none', 'color': '#008000'});
							buttonOne.addClass('glyphicon glyphicon-ok');							

							//if(question.answers.playerTwo.help){
							if(question.playerTwo.useHelp){
								setTimeout(function () {					

									sh = $('#helpOne'+question['questionId']);									
									sh.css('display', 'block');

								}, 500);
							}

							

						}else{
							//buttonOne.addClass('btn-danger');	
							buttonOne.css({'background': 'none', 'color': '#FF0000'});
							buttonOne.addClass('glyphicon glyphicon-remove');

							//if(question.answers.playerTwo.help){
							if(question.playerTwo.useHelp){
								setTimeout(function () {					

									sh = $('#helpOne'+question['questionId']);									
									sh.css('display', 'block');

								}, 500);
							}
						} 
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

						spanCheck = $('<span class="span-check"></span>');
						buttonOne.append(spanCheck);

						resetOne.append(buttonOne);
					}
					else{
//						var buttonOne = $('<button class="btnOne"></button>').addClass('btn btn-default').attr("disabled", "disabled").css({
//							'width': '90%',
//							'height': '40%',
//							'border': 'none',
//							'font-size': me.questionFontSize,
//							'background': 'none'
//						});

						var buttonOne = $('<span class="btnOne"></span>').css({						
							'font-size': me.questionFontSize,						
						});	
						
//						spanCheck = $('<span class="span-check"></span>');
//						buttonOne.append(spanCheck);

						resetOne.append(buttonOne);
					}

					tr.append(resetOne);
					if(question.answers && question.answers.playerTwo && _.isObject(question.answers.playerTwo) && question.answers.playerTwo.answer !== undefined)
					{						
						var answerOne = $('<td style="vertical-align: middle;"><p>'+question.answers.playerTwo.answer+'</p></td>');
						answerOne.css({
							'width': '200px',
							'text-align': 'center',
							'font-size': me.answerFontSize
						});
						tr.append(answerOne);
					}
					else{						
						var answerOne = $('<td style="vertical-align: middle;"><p></p></td>');
						answerOne.css({
							'width': '200px',
							'text-align': 'center',
							'font-size': me.answerFontSize
						});
						tr.append(answerOne);
					}

					
					var q = $('<td style="vertical-align: middle;">'+
								'<p>'+question.question+'</p>'+
							  '</td>');
					
					if(!(_.isNull(question.image))){
						q.append('<img src="image/'+question.image+'?width=100&height=100"/>');
					}
					
					q.css({
						'width': '400px',
						'text-align': 'center',
						'font-size': me.questionFontSize
					});
					
					tr.append(q);
					if(question.answers && question.answers.playerOne  && _.isObject(question.answers.playerOne) && question.answers.playerOne.answer !== undefined)
					{
						var answerTwo = $('<td style="vertical-align: middle;"><p>'+question.answers.playerOne.answer+'</p></td>');
						answerTwo.css({
							'width': '200px',
							'text-align': 'center',
							'font-size': me.answerFontSize
						});
						tr.append(answerTwo);
					}
					else{
						var answerTwo = $('<td style="vertical-align: middle;"><p></p></td>');
						answerTwo.css({
							'width': '200px',
							'text-align': 'center',
							'font-size': me.answerFontSize
						});
						tr.append(answerTwo);
					}
					
					var resetTwo = $('<td style="vertical-align: middle;"></td>').css('width', '70px');
					if(question.answers && question.answers.playerOne  && _.isObject(question.answers.playerOne) && question.answers.playerOne.answer !== undefined)
					{
//						var buttonTwo = $('<button></button>').addClass('btn').css({
//							'width': '90%',
//							'height': '40%',
//							'border': 'none',
//							'font-size': me.questionFontSize,
//							'background': 'none'							
//						});
						
						var buttonTwo = $('<span></span>').css({						
							'font-size': me.questionFontSize,						
						});
						
						if(question.answers.playerOne.find){
							//buttonTwo.addClass('btn-success');	
							buttonTwo.css({'background': 'none', 'color': '#008000'});												
							buttonTwo.addClass('glyphicon glyphicon-ok');

<<<<<<< HEAD
							//if(question.answers.playerOne.help){
							if(question.playerOne.useHelp){

=======
							if(question.playerOne.useHelp){
								
>>>>>>> 80574c842ff02a7ff141fca6e79404b20b2c87bf
								setTimeout(function () {					

									sh = $('#helpTwo'+question['questionId']);									
									sh.css('display', 'block');

								}, 500);
							}

						}else {
							//buttonTwo.addClass('btn-danger');	
							buttonTwo.css({'background': 'none', 'color': '#FF0000'});
							buttonTwo.addClass('glyphicon glyphicon-remove');

							//if(question.answers.playerOne.help){
							if(question.playerOne.useHelp){
								
								setTimeout(function () {					

									sh = $('#helpTwo'+question['questionId']);									
									sh.css('display', 'block');

								}, 500);
							}
						}
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
//						var buttonTwo = $('<button class="btnTwo"></button>').addClass('btn btn-default').attr("disabled", "disabled").css({
//							'width': '90%',
//							'height': '40%',
//							'border': 'none',
//							'font-size': me.questionFontSize,
//							'background': 'none'					
//						});
						
						var buttonTwo = $('<span class="btnTwo"></span>').css({						
							'font-size': me.questionFontSize,						
						});
						
						resetTwo.append(buttonTwo);
					}

					tr.append(resetTwo);

					tr.append(spanHelpTwo);
					tr.append(tdTimeTwo);
					
					tableDuels.append(tr);
				});
				modalBody.append(duels);

				
//				console.log("duel")
//				console.log(duel)
				
//				pointsPlayerOne = 0;
//				pointsPlayerTwo = 0;
//				
//				pointsPlayerOne = duel.point_one;
//				pointsPlayerTwo = duel.point_two;	
//				
				var bool = true;
				var count = 0;
				me.socket.on('answer', function(answer, user, questionId, ptnPlayer, answerId){
					user = JSON.parse(user);

					if(user['id'] == data.playerOne.id){
						
						
						console.log("Puntos jugador One")
						console.log(ptnPlayer)
						
						//$('#timeTwo'+question['questionId']).children('label').text(time);										
						switch(answer){
							case 'Correcto': 
								
//								console.log("Sumatoria puntos PlayerOne: ")
//								console.log(pointsPlayerOne)
//								console.log('+')
//								console.log(pointsForQuestion)
//								
//								pointsPlayerOne = parseInt(pointsPlayerOne) + parseInt(pointsForQuestion);
//								
//								$('#playerPointOne').text(pointsPlayerOne);
								$('#playerPointOne').text(ptnPlayer);
								btnTwo = $('#'+questionId).find('.btnTwo');
								//btnTwo.removeClass('btn-default');					
								//btnTwo.addClass('btn-success');
								btnTwo.attr("disabled", false);
								//sc = btnOne.find('.span-check');							
								btnTwo.css({'background': 'none', 'color': '#008000'});
								btnTwo.addClass('glyphicon glyphicon-ok');
								break;							
							case 'Incorrecto': 
								
//								console.log("Sumatoria puntos PlayerOne: ")
//								console.log(parseInt(pointsPlayerOne)+ ' + '+ parseInt(pointsForQuestion))		
//								pointsPlayerOne = parseInt(pointsPlayerOne);								
//								$('#playerPointOne').text(pointsPlayerOne);
								
								btnTwo = $('#'+questionId).find('.btnTwo');
								// btnTwo.removeClass('btn-default');					
								// btnTwo.addClass('btn-danger');
								btnTwo.attr("disabled", false);

								//sc = btnOne.find('.span-check');
								btnTwo.css({'background': 'none', 'color': '#FF0000'});
								btnTwo.addClass('glyphicon glyphicon-remove');
								break;
							case 'Tiempo Agotado':
								
//								console.log("Sumatoria puntos PlayerOne: ")
//								console.log(parseInt(pointsPlayerOne)+ ' + '+ parseInt(pointsForQuestion))		
//								pointsPlayerOne = parseInt(pointsPlayerOne);								
								//$('#playerPointOne').text(pointsPlayerOne);
								
								btnTwo = $('#'+questionId).find('.btnTwo');
								//btnTwo.removeClass('btn-default');					
								//btnTwo.addClass('btn-warning');
								btnTwo.attr("disabled", false);

								//sc = btnOne.find('.span-check');
								btnTwo.css({'background': 'none', 'color': '#FF0000'});
								btnTwo.addClass('glyphicon glyphicon-remove');
								break;
						}	
						
					}else if(user['id'] == data.playerTwo.id){									
						//$('#timeOne'+question['questionId']).children('label').text(time);
						
						console.log("Puntos jugador TWO")
						console.log(ptnPlayer)
						
						switch(answer){
							case 'Correcto':
//								console.log("Sumatoria puntos PlayerTwo: ")
//								console.log(pointsPlayerTwo)
//								console.log('+')
//								console.log(pointsForQuestion)
//								
//								pointsPlayerTwo = parseInt(pointsPlayerTwo) + parseInt(pointsForQuestion);		
//								//pointsPlayerTwo = parseInt(pointsPlayerTwo) + parseInt(pointsForQuestion);								
//								$('#playerPointTwo').text(pointsPlayerTwo);
								$('#playerPointTwo').text(ptnPlayer);
								
								btnOne = $('#'+questionId).find('.btnOne');
								// btnOne.removeClass('btn-default');					
								// btnOne.addClass('btn-success');
								btnOne.remove('disabled');
	
								// sc = btnOne.find('.span-check');	
								// sc.addClass('glyphicon glyphicon-ok');
								btnOne.css({'background': 'none', 'color': '#008000'});
								btnOne.addClass('glyphicon glyphicon-ok');
								break;
							case 'Incorrecto':
								//pointsPlayerTwo = parseInt(pointsPlayerTwo);								
								//$('#playerPointTwo').text(pointsPlayerTwo);
								btnOne = $('#'+questionId).find('.btnOne');
								// btnOne.removeClass('btn-default');					
								// btnOne.addClass('btn-danger');
								btnOne.remove('disabled');
		
								// sc = btnOne.find('.span-check');
								// sc.addClass('glyphicon glyphicon-remove');
								btnOne.css({'background': 'none', 'color': '#FF0000'});
								btnOne.addClass('glyphicon glyphicon-remove');
								break;
							case 'Tiempo Agotado':
								//pointsPlayerTwo = parseInt(pointsPlayerTwo);								
								//$('#playerPointTwo').text(pointsPlayerTwo);
								btnOne = $('#'+questionId).find('.btnOne');
								// btnOne.removeClass('btn-default');					
								// btnOne.addClass('btn-warning');
								btnOne.remove('disabled');
												
								// sc = btnOne.find('.span-check');
								// sc.addClass('glyphicon glyphicon-remove');
								btnOne.css({'background': 'none', 'color': '#FF0000'});
								btnOne.addClass('glyphicon glyphicon-remove');
								break;					
						}					
					}
					
				});

				modal.on("hidden.bs.modal", function(){
		    		modal.remove();
		    	});
			},
			error: function(){},
	    	complete: function(){
	    		loader.hide();
	    	}
        }
        $.ajax(configTorunament);
	},

	getQuestion: function () {
		var me = this;
		me.socket.on('question', function(question, user){
			
			currentQuestion = JSON.parse(question);
			//console.log(currentQuestion)
			currentUser = JSON.parse(user);

			return currentUser;
			//console.log(currentUser)
			// 	//me.questionPlayer(question, user);
		});
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
				//width:500,
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

	realTime: function(){
		var me = this;
		this.removeContent("realTime");
		this.buildbreadcrumbs({
		  	Inicio: "",
		  	'Tiempo Real': "Tiempo Real"
		});
<<<<<<< HEAD

      me.socket = io.connect('http://10.102.1.22:3000');    

=======
		
		me.socket = io.connect('http://104.130.28.12:27000');   
>>>>>>> 80574c842ff02a7ff141fca6e79404b20b2c87bf

        me.socket.on('question', function(question, user){
        	console.log('question')
        	me.questionPlayer(question, user);
        });

        me.socket.on('time', function(time, user){
        	console.log('time')
        	me.timePlayer(time, user);
        });

        me.socket.on('answer', function(answer, user, questionId, pointsForQuestion, answerId){
        	console.log('answer')
        	me.answerPlayer(answer,user,answerId);
        });

//        socket.on('help', function(help, user){
//        });

        me.socket.on('load', function(userp){
        	var user = JSON.parse(userp);
        	if($("#entity-content").find("div#"+user.id).length > 0){
        		me.answerPlayer("Recargar pagina",userp,"-1");
        	}
        });

        me.socket.on('goOut', function(userp){
        	var user = JSON.parse(userp);
        	if($("#entity-content").find("div#"+user.id).length > 0){
        		me.answerPlayer("Salir pagina",userp,"-1");
        	}
        });
     
	},

	questionPlayer: function(question, userp){
		var me = this;
		var user = JSON.parse(userp);
		if($("#entity-content").find("div#"+user.id).length == 0){
			me.questionA = true;
			var question = JSON.parse(question);
			
			var div = $('<div></div>').attr('id', user.id).addClass('user-question').css("max-height","300px");
			var time = $('<span></span>').attr('id', 'time').css({
				"font-size":"20px",
				"font-weight":"400",
				"margin-left": "25px",
				"margin-right": "20px",
				"margin-bottom": "10px"
			});
			
			var userDiv = $('<div></div>');
			var userImg = $('<img src="image/'+user.image+'"/>').addClass('img-circle').css({
				'width': '80px',
				'height': '80px',
				'margin-left': '20px'
			});
			var userName = $('<label></label>').html(user.name+' '+user.latname);
			userDiv.append(userImg).append(userName).append(time);
			//div.append(time);
			var questionDiv = $('<div></div>');
			var q = $('<div></div>').html(question.question.question);
			q.css({
				"background-color": "white",
				"min-height": "60px",
				"border-radius": "10px",
				"padding-top": "15px",
				"padding-bottom": "15px"
			});			
			questionDiv.append(q);
			if(question.question.image)
			{
				var imageDiv = $('<img src="image/'+question.image+'"/>').css("width", "80px").css("height", "80px");
				questionDiv.append(imageDiv);
			}
			var answerDiv = $('<div></div>');
			_.each(question.question.answers, function(value, key){
				
				var answer = $('<div></div>').html(value.answer);
				answer.attr("id","answer_"+value.id);
				answer.css({
					"border": "solid 1px",
					"border-radius": "5px",
					"background-color": "white",
					"margin-top": "5px"
				})
				answerDiv.append(answer);
			})
			div.append(userDiv).append(questionDiv).append(answerDiv);

			var divAnswer = $("<div class='answer'></div>");
			divAnswer.css({					
					"border-radius": "20px",								
					"margin-left": "10%",
					"margin-right": "10%",					
					"margin-top": "5px",					
				})
			div.append(divAnswer);
		
			var divAlert = $('<div></div>').attr("data-alert", "true").attr("id","showanw"+user.id)
		 	.css("position", "relative")
		 	.css("top", "-125px")
		 	//.css("left", "180px")
		 	//.css("width", "100%")
		 	//.css("height", "100%")
		 	.css("display", "none")
		 	.css("z-index", "99");
		 var msg = $("<span></span>").addClass("alert-msg");
		 divAlert.append(msg);
		 div.append(divAlert);

			$("#entity-content").append(div);
		}else{
			$("#entity-content").find("div#"+user.id).remove();
			me.questionPlayer(question, userp);
		}
	},

	timePlayer:function(time, user){		
		var user = JSON.parse(user);
		var spanTime = $("div#"+user.id).find("span");
		if(spanTime){
			spanTime.html(time);
		}		
	},
	answerPlayer:function(answer, user,answerId){
		var me = this;
		var user = JSON.parse(user);
		console.log("answerId");
		console.log(answerId);
		var divAnswer = $("div#showanw"+user.id);
		var divAnswerRes = $("div#answer_"+answerId);
		me.questionA = false;
		if(divAnswer){
			
			switch(answer){
				case "Correcto":
					divAnswer.find("span").html(answer).css("color", "#5CB85C");
					divAnswerRes.addClass("btn-success");
					divAnswerRes.css("background-color", "#5cb85c");
					divAnswer.show();					
				break;
				case "Incorrecto":					
					divAnswer.find("span").html(answer).css("color", "#D9534F");
					divAnswerRes.addClass("btn-danger");
					divAnswerRes.css("background-color", "#d9534f");	
					divAnswer.show();
				break;
				case "Tiempo Agotado":					
					divAnswer.find("span").html(answer).css("color", "#F0AD4E");
					divAnswer.show();

				break;
				case "Recargar pagina":					
					divAnswer.find("span").html("Recargar página").css("color", "#F0AD4E");
					divAnswer.show();

				break;
				case "Salir pagina":					
					divAnswer.find("span").html("Salir página").css("color", "#F0AD4E");
					divAnswer.show();

				break;
			}
		}
		setTimeout(function(){
			
			if(!me.questionA){
				$("div#"+user.id).remove();
			}
			
		},3000);		
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
						  '<div class="form-group">'+
						    '<label  class="col-sm-2 control-label">Notificación a todos los jugadores del torneo</label>'+
						    '<div class="col-sm-10">'+
						    	'<button type="button" class="btn btn-primary send-not-players">Enviar</button>'+
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
		
		//Enviar un mensaje a los jugadores
		form.find(".send-not-players").click(function () {
			var tournament = form.find('.select-tournament').val();
			if(!_.isNull(tournament) && tournament != 0)
			{
				var modal = $("<div></div>").addClass("modal fade").attr({"tabindex": "-1", "role":"dialog"});
				var modalDialog = $("<div></div>").addClass("modal-dialog");
				var modalContent = $("<div></div>").addClass("modal-content");
				var modalHeader = $("<div></div>").addClass("modal-header");
				var btnClose = $("<button></button>").attr({"type": "button", "data-dismiss": "modal", "aria-label": "Close"}).addClass("Close.");
				var spanClose = $("<span></span>").attr("aria-hidden", "true").html("&times;");
				btnClose.append(spanClose);
				var titleHeading = $("<h4></h4>").addClass("modal-title").html("Notificar a todos los jugadores");
				modalHeader.append(titleHeading);
				var modalBody = $("<div></div>").addClass("modal-body");
				
				var formBody = $("<form></form>");
				
				var divSubject = $("<div></div>").addClass("form-group");
				var labelSubject = $("<label></label>").attr({"for": "subjectMessage"}).html("Asunto");
				var subject = $("<input></input").attr({"type": "text", "placeholder": "Asunto", "id": "subjectMessage"}).addClass("form-control");
				divSubject.append(labelSubject).append(subject);
				
				var divBody = $("<div></div>").addClass("form-group");
				var labelBody = $("<label></label>").attr({"for": "bodyMessage"}).html("Mensaje");
				var body = $("<textarea></textarea").attr({"placeholder": "Mensaje", "id": "bodyMessage", "rows": "5"}).addClass("form-control");
				divBody.append(labelBody).append(body);
				
				formBody.append(divSubject).append(divBody);
				modalBody.append(formBody);

				var modalFooter = $("<div></div>").addClass("modal-footer");
				var btnCancel = $("<button></button>").addClass("btn btn-default").attr("data-dismiss", "modal").html("Cancelar");
				var btnSend = $("<button></button>").addClass("btn btn-primary").html("Enviar");
				modalFooter.append(btnCancel).append(btnSend);

				modal.append(modalDialog.append(modalContent.append(modalHeader).append(modalBody).append(modalFooter)));
				$(document.body).append(modal);
				modal.modal("show");

				btnSend.click(function(){
					var su = subject.val();
					var bo = body.val();
					if(!_.isNull(su) && !_.isNull(bo))
					{
						$.confirm({
						    text: "Desea enviar este mensaje a los jugadores ?",
						    confirm: function(button) {

						    	var data = {
						    		"tournamentId": tournament,
						    		"subject": su,
						    		"message": bo
						    	}
			        			parameters = {
									type: "GET",
								    url: "home/player/all/notification",
							        contentType: 'application/json',
					            	dataType: "json",
					            	data: data,
			            	     	statusCode: {
								      401:function() { 
								      	window.location = '';
								      }
								    },
							        success: function(data){
							        	modal.modal("hide");
							        },
							        error: function(){}
								};

								$.ajax(parameters);
						    },
						    cancel: function(button) {
						        // do something
						    }
						});
					}
					else{
						alert("Por favor rellene los campos Asunto y Mensaje");
					}
					
				})
			}
			else{
				alert("Por favor seleccione un torneo");
			}
			
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

function generateDuel(){
	
	var router = new routerManager();
	Backbone.history.navigate("generateDuel", true);
}
// function selectTournament(){
	
// 	var router = new routerManager();
// 	Backbone.history.navigate("selectTournament", true);
// }



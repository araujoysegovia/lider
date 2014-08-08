
var Entity = function(){
	if(typeof this.constructor == "function"){
		this.constructor.apply(this, arguments);
	}
}

Entity.prototype = {
	constructor: function(config){
		var me = this;
		_.extend(me, config);
		me.container.addClass("panel panel-default");
		
		me.body = $("<div></div>").addClass("panel-body");
		me.container.append(me.body);
		me.buildTitle();
		
		me.datasource = me.buildDatasource();
		me.grid = me.buildGrid();
	},
	buildTitle: function(){
		var me = this;
		var title = $("<h3>").text(me.title);
		me.body.append(title);
	},
	buildDatasource: function(){
		var me = this;
		//console.log(me.model)
		
		return new kendo.data.DataSource({			  
			  	transport: {
				    read:  {
				    	url: me.url,
				    	dataType: "json"
				    },
				    create: {
			            url: me.url,
			            type: "POST",
			            contentType: "application/json",
			            dataType: "json"
			        },
			        update: {
			            url: function (e) {
			            	console.log(e)
			            	console.log("url: "+me.url + e.id)
			                return me.url + e.id;
			            },
			            type: "PUT",
			            contentType: "application/json",
			            dataType: "json"
			        },
				    destroy: {
				    	url: function (e) {
			                return me.url + e.id;
			            },
		                dataType: "json",
		                contentType: "application/json",
		                type: 'DELETE',  
		            },
		            parameterMap: function (data, type) {
				        if (type !== "read") {
				        	
				        	if(data.startdate){
				        		data.startdate = kendo.toString(new Date(data.startdate), "MM/dd/yyyy");
				        	}
				        	if(data.enddate){
				        		data.enddate = kendo.toString(new Date(data.enddate), "MM/dd/yyyy");
				        	}
				        	console.log(data)
				            return kendo.stringify(data);
				        }
		  			}
			  },
			  schema: {
			  	total: "total",
		    	data: "data",
		        model: me.model
			  },
			  pageSize: 20,	
			  groupable: false,			  
			  sortable: true, 
			  selectable: "row",
			  filterable: false,          
			  pageable: {
			    refresh: true,
			    pageSizes: false,               
			    buttonCount: 5,
			    info: true,
			    messages: {
			      display: "Mostrando {0}-{1} de {2} datos"
			    }
	 		  },
	 		  requestEnd: function (e){
//	 			  console.log(e)
	 			  if(e.type && e.response && e.type !="read"){	 	 			
		         	me.grid.data('kendoGrid').dataSource.read();
		        	//me.grid.data('kendoGrid').refresh(); 
	 			  }
	 		  }
			  
		});
	},
	
	buildGrid: function(){
		
		var me = this;
		
		var d = $("<div></div>");
		me.body.append(d);	

		me.columns.push(            { 
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

            	],
            	width: "200px",
            })	;
		return d.kendoGrid({
	      	dataSource: me.datasource,        
	        pageable: true,
	        height: 350,	        
	        columns: me.columns,
	        editable: "popup",
	        toolbar: [
			    { name: "create", text: "Agregar registro" },			
			],

	    });

	}
}
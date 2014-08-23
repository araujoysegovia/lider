
var Entity = function(){
	if(typeof this.constructor == "function"){
		this.constructor.apply(this, arguments);
	}
}

Entity.prototype = {

	constructor: function(config){
		var me = this;
				
		me.read = {
	    	url: function(e){
	    		return me.url;
	    	},
	    	dataType: "json"
	    },
	    me.create = {
            url: function(e){
            	return me.url
            },
            type: "POST",
            contentType: "application/json",
            dataType: "json"
        },
        me.update = {
            url: function (e) {            	
            	//console.log("url: "+me.url + e.id)
                return me.url + e.id;
            },
            type: "PUT",
            contentType: "application/json",
            dataType: "json"
        },
        me.destroy = {
	    	url: function (e) {
                return me.url + e.id;
            },
            dataType: "json",
            contentType: "application/json",
            type: 'DELETE',  
        }, 
        me.parameterMap = function (data, type) {
			//console.log("entro")
	        if (type !== "read") {	        	
	        	if(data.startdate){
	        		data.startdate = kendo.toString(new Date(data.startdate), "MM/dd/yyyy");
	        	}
	        	if(data.enddate){
	        		data.enddate = kendo.toString(new Date(data.enddate), "MM/dd/yyyy");
	        	}
	        	
	            return kendo.stringify(data);
	        }
		}        
	    
		me.editable = {
	        	mode: "popup",
	        	confirmation: "Seguro quieres eliminar este registro?",
	        	window: {
	                title: "Editar",
	                animation: false,
	                width: 700
	            }
	    };
		me.command = [
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

        ];
			
	    me.autoSync = false;
	    me.dataBound =  function(e) {	       
	    }
	    me.dataBinding  = function(e){
	    	
	    }
	    me.width = "200px";
	    
		_.extend(me, config);
		
		me.container.addClass("panel panel-default");
		
		me.body = $("<div></div>").addClass("panel-body");
		me.container.append(me.body);
		me.buildTitle();
		me.model = kendo.data.Model.define(me.model);
		//console.log(me.model)
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
		//console.log(me.autoSync)
		var conf = {	
		    autoSync: me.autoSync,
		  	transport: {
			    read:  me.read,
			    create: me.create,
		        update: me.update,
			    destroy: me.destroy,
	            parameterMap: me.parameterMap
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
	 			  if(e.type && e.response){
	 				if(e.type !="read"){
	 					me.grid.data('kendoGrid').dataSource.read();
	 					me.grid.data('kendoGrid').refresh();
	 				}else{
	 					me.onReadData(e);
	 				}
	 			  }
	 		  }
			  
		};
		if(me.change){
			conf["change"] = function(e){
				me.change(e, me);
			}
		}
			
		return new kendo.data.DataSource(conf);
				
	},
	
	onReadData: function(e){
		
	},
	
	buildGrid: function(){
		
		var me = this;	
		var d = $("<div></div>");
		me.body.append(d);	
		me.columns.push({ 
            	command : me.command,
            	width: me.width,
            })	;
		var config = {
		      	dataSource: me.datasource, 
		      	filterable: {
                    extra: false,
                    operators: {
                        string: {
                            startswith: "Empieza con",
                            eq: "Es igual a ",
                            contains: "Contiene" 
                        }
                    }
                },
		        pageable: true,
		        height: 500,	        
		        columns: me.columns,
		        editable: me.editable,
		        sortable: true,                               
		        toolbar: [
				    { name: "create", text: "Agregar registro" },			
				],
				dataBound: me.dataBound,
				dataBinding: me.dataBinding
		    };
		
		if(me.detailInit){			
			config["detailInit"] = me.detailInit;
		}
		
		if(me.dataBound){
			config["dataBound"] = me.dataBound;
		}
		
		return d.kendoGrid(config);

	},
	
	getModel: function(){
		//return this.datasource.
	}
}
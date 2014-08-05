function parseDate (rec){
	console.log("fecha : "+rec)
	if(_.isString(rec)){
		console.log("es string")
		return new Date(rec);		                	
	}else if(_.isDate(rec)){
		console.log("es date")
		return rec;
	}else if(_.isObject(rec)){
		console.log("es objeto")
		return new Date(rec.date);		                	
	}	
}

$(document).ready(function () { 	
	var kurl = "home/tournament/";
	var dataSourceTournaments = new kendo.data.DataSource({			  
			  transport: {
				    read:  {
				    	url: kurl,
				    	dataType: "json"
				    },
				    create: {
			            url: kurl,
			            type: "POST",
			            contentType: "application/json",
			            dataType: "json"
			        },
			        update: {
			            url: function (e) {
			                return kurl + e.id;
			            },
			            type: "PUT",
			            contentType: "application/json",
			            dataType: "json"
			        },
				    destroy: {
				    	url: function (e) {
			                return kurl + e.id;
			            },
		                dataType: "json",
		                contentType: "application/json",
		                type: 'DELETE',  
		            },
		            parameterMap: function (data, type) {
				        if (type !== "read") {
				        	
				        	data.startdate = kendo.toString(new Date(data.startdate), "MM/dd/yyyy");
				        	console.log(data.startdate)
			                data.enddate = kendo.toString(new Date(data.enddate), "MM/dd/yyyy");
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
		            	id: { editable: false, nullable: true },
		                name: { type: "string" },
		                startdate: { type: "date", parse: parseDate},
		                enddate: { type: "date", parse: parseDate},
		                active: { type: "boolean" }
		            }
		        }
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
	 			  if(e.type && e.response && e.type !="read"){	 		        
		         	$('#gridTournaments').data('kendoGrid').dataSource.read();
 		        	$('#gridTournaments').data('kendoGrid').refresh(); 
	 			  }
	 		  }
			  
    });

    $("#gridTournaments").kendoGrid({
      	dataSource: dataSourceTournaments,        
        pageable: true,
        height: 500,
        toolbar: ["create"],
        columns: [
            { field:"name", title: "Nombre" },
            { field: "startdate", title:"Fecha de inicio", template: "#= kendo.toString(kendo.parseDate(startdate, 'yyyy-MM-dd'), 'dd/MM/yyyy') #"},
            { field: "enddate", title:"Fecha de fin", template: "#= kendo.toString(kendo.parseDate(startdate, 'yyyy-MM-dd'), 'dd/MM/yyyy') #"},
            { field: "active", title: "Activo" },            //
            { command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }],
        editable: "popup",
    });

});
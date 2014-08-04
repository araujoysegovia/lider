$(document).ready(function () {
 
	var url = "home/tournament/";
	
	dataSourceTournaments = new kendo.data.DataSource({
			  autoSync: false,
			  transport: {
				    read:  {
				    	url: url,
				    	dataType: "json",
				    	type: 'GET',
				    },
				    create: {
			            url: url,
			            type: "POST",
			            contentType: "application/json",
			            dataType: "json"
			        },
			        update: {
			            url: function (e) {
			                return url + e.id;
			            },
			            type: "PUT",
			            contentType: "application/json",
			            dataType: "json"
			        },
				    destroy: {
				    	url: function (e) {
			                return url + e.id;
			            },
	                    dataType: "json",
	                    contentType: "application/json",
	                    type: 'DELETE',  
	                },
	                parameterMap: function (data, type) {
	  			        if (type !== "read") {
	  			            return kendo.stringify(data);
	  			        }
		  			}
			  },
			  error: function(e, x, y) {
				  //console.log(e.xhr.status);
				  try{
				     var json = JSON.parse(e.xhr.responseText);
				     console.error("Error: " + json.message);
				     notification.show({
                       title: "Error",
                       message: json.message
                   }, "error");
				  }catch(e){
//					 notification.show({
//                       title: "Error",
//                       message: "Server Error"
//                   }, "error");
				  }
				  if(e.xhr.status == 401){
					  window.location= "/index";
				  }
			  },
			  requestEnd: function(e) {
				  if(e.type && e.response){
					  var msg = null;
					  switch (e.type) {
						case "create":
							msg = "Create Successful";
							break;
						case "update":
							msg = "Update Successful";
							break;
						case "destroy":
							msg = "Delete Successful";
							break;
						/*case "read":
							msg = "Read Successful";
							break;
						default:
							break;*/
					  }
					  if(msg){
					  	 notification.show({
	                         message: msg
	                     }, "success"); 
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
	                    startdate: { type: "date"},
	                    enddate: { type: "date"},
	                    active: { type: "boolean" }
	                }
	            }
			  },
			  pageSize: 20,
		      serverPaging: true,		      
		      serverFiltering: true,
		      serverSorting: true
				
		
    });

    $("#gridTournaments").kendoGrid({
    	autoBind: true,
        dataSource: dataSourceTournaments,
        pageable: true,
        height: 500,
        toolbar: ["create"],
        columns: [
            { field:"name", title: "Nombre" },
            { field: "startdate", title:"Fecha de inicio",  format: "{0:dd-MM-yyyy}", parseFormats: ["yyyy-MM-dd"] },
            { field: "enddate", title:"Fecha de fin",  format: "{0:dd-MM-yyyy}", parseFormats: ["yyyy-MM-dd"] },
            { field: "active", title: "Activo"},
            { command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }],
        editable: "popup"
    });

});
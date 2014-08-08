$(document).ready(function () {
   var dataSourceCategories = new kendo.data.DataSource({
        transport: {
            read:  {
                url:  "home/category/",
                dataType: "json",
                type: "GET"
            },
            update: {                
                url: function (e) {
	                return "home/category/" + e.id;
	            },
                dataType: "json",
                type: "PUT"
            },
            destroy: {
                url: "home/category/",
                dataType: "json",
                type: "DELETE"
            },
            create: {
                url: "home/category/",
                type: "POST",
                dataType: "json",
                contentType: "application/json",
            },
            parameterMap: function (data, type) {
			        if (type !== "read") {
			            return kendo.stringify(data);
			        }
  			}
        },
        batch: true,
        pageSize: 20,
        schema: {
		  	total: "total",
        	data: "data",        	
            model: {
                id: "id",
                fields: {
                    id: { editable: false, nullable: true },
                    name: { validation: { required: true } },
                   
                }
            }
        }
    });

    $("#gridCategories").kendoGrid({
        dataSource: dataSourceCategories,
        pageable: true,
        height: 500,
        toolbar: ["create"],
        columns: [
            { field:"name", title: "Nombre" },             
            { command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }],
        editable: "popup"
    });
})
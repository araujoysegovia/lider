$(document).ready(function () {

    // var crudServiceBaseUrl = "http://localhost/lider/web/app_dev.php",
        dataSource = new kendo.data.DataSource({
            transport: {
                read:  {
                    url:  "admin/tournament",
                    dataType: "json",
                    type: "GET"
                },
                update: {
                    url: "admin/tournament",
                    dataType: "json",
                    type: "PUT"
                },
                destroy: {
                    url: "admin/tournament",
                    dataType: "json",
                    type: "DELETE"
                },
                create: {
                    url: "admin/tournament",
                    dataType: "POST"
                },
                parameterMap: function(options, operation) {
                    if (operation !== "read" && options.models) {
                        return {models: kendo.stringify(options.models)};
                    }
                }
            },
            batch: true,
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        tournamentId: { editable: false, nullable: true },
                        name: { validation: { required: true } },
                        startDate: { type: "date", },
                        endDate: { type: "date" },
                        active: { type: "boolean" }
                    }
                }
            }
        });



    $("#gridTournaments").kendoGrid({
        dataSource: dataSource,
        pageable: true,
        height: 550,
        toolbar: ["create"],
        columns: [
            { field:"name", title: "Nombre" },
            { field: "startDate", title:"Fecha de inicio" },
            { field: "endDate", title:"Fecha de fin"},
            { field: "active", title: "Activo"},
            { command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }],
        editable: "popup"
    });


});
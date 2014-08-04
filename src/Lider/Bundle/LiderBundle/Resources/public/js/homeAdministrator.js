$(document).ready(function () {

    $("#linkTournaments").click(function () {

        $(".tournaments").css("display","block");
        $(".groups").css("display","none");
        $(".players").css("display","none");
        $(".categories").css("display","none");
        $(".questions").css("display","none");
    });

    $("#linkPlayers").click(function () {

        $(".players").css("display","block");
        $(".tournaments").css("display","none");
        $(".groups").css("display","none");
        $(".categories").css("display","none");
        $(".questions").css("display","none");
    });

    $("#linkGroups").click(function () {

        $(".groups").css("display","block");
        $(".players").css("display","none");
        $(".tournaments").css("display","none");
        $(".categories").css("display","none");
        $(".questions").css("display","none");
    });        
    
    $("#linkCategories").click(function () {

        $(".categories").css("display","block");
        $(".players").css("display","none");
        $(".tournaments").css("display","none");
        $(".groups").css("display","none");
        $(".questions").css("display","none");
    });      

    $("#linkQuestions").click(function () {

        $(".questions").css("display","block");
        $(".categories").css("display","none");
        $(".players").css("display","none");
        $(".tournaments").css("display","none");
        $(".groups").css("display","none");

    });      

    /**************** KENDO GRIDS ******************/
    
   

//    dataSourcePlayers = new kendo.data.DataSource({
//        transport: {
//            read:  {
//                url:  "tournament",
//                dataType: "json",
//                type: "GET"
//            },
//            update: {
//                url: "tournament",
//                dataType: "json",
//                type: "PUT"
//            },
//            destroy: {
//                url: "tournament",
//                dataType: "json",
//                type: "DELETE"
//            },
//            create: {
//                url: "tournament",
//                dataType: "POST"
//            },
//            parameterMap: function(options, operation) {
//                if (operation !== "read" && options.models) {
//                    return {models: kendo.stringify(options.models)};
//                }
//            }
//        },
//        batch: true,
//        pageSize: 20,
//        schema: {
//            model: {
//                id: "id",
//                fields: {
//                    tournamentId: { editable: false, nullable: true },
//                    name: { validation: { required: true } },
//                    startDate: { type: "date", },
//                    endDate: { type: "date" },
//                    active: { type: "boolean" }
//                }
//            }
//        }
//    });
//
//    $("#gridPlayers").kendoGrid({
//        dataSource: dataSourcePlayers,
//        pageable: true,
//        height: 500,
//        toolbar: ["create"],
//        columns: [
//            { field:"name", title: "Nombre" },
//            { field: "lastname", title:"Apellido" },
//            { field: "office_id", title:"Oficina"},
//            { field: "team_id", title: "Equipo"},
//            { command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }],
//        editable: "popup"
//    });
//
//    dataSourceGroups = new kendo.data.DataSource({
//        transport: {
//            read:  {
//                url:  "tournament",
//                dataType: "json",
//                type: "GET"
//            },
//            update: {
//                url: "tournament",
//                dataType: "json",
//                type: "PUT"
//            },
//            destroy: {
//                url: "tournament",
//                dataType: "json",
//                type: "DELETE"
//            },
//            create: {
//                url: "tournament",
//                dataType: "POST"
//            },
//            parameterMap: function(options, operation) {
//                if (operation !== "read" && options.models) {
//                    return {models: kendo.stringify(options.models)};
//                }
//            }
//        },
//        batch: true,
//        pageSize: 20,
//        schema: {
//            model: {
//                id: "id",
//                fields: {
//                    tournamentId: { editable: false, nullable: true },
//                    name: { validation: { required: true } },
//                    startDate: { type: "date", },
//                    endDate: { type: "date" },
//                    active: { type: "boolean" }
//                }
//            }
//        }
//    });
//
//    $("#gridGroups").kendoGrid({
//        dataSource: dataSourceGroups,
//        pageable: true,
//        height: 500,
//        toolbar: ["create"],
//        columns: [
//            { field:"name", title: "Nombre" },
//            { field:"tournament_id", title: "Toreo" },
//            { field: "entryDate", title:"Fecha de inicio" },
//            { field: "lastUpdate", title:"Ultima modificación"},
//            { field: "active", title: "Activo"},
//            { command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }],
//        editable: "popup"
//    });
//
//
//    dataSourceCategories = new kendo.data.DataSource({
//        transport: {
//            read:  {
//                url:  "tournament",
//                dataType: "json",
//                type: "GET"
//            },
//            update: {
//                url: "tournament",
//                dataType: "json",
//                type: "PUT"
//            },
//            destroy: {
//                url: "tournament",
//                dataType: "json",
//                type: "DELETE"
//            },
//            create: {
//                url: "tournament",
//                dataType: "POST"
//            },
//            parameterMap: function(options, operation) {
//                if (operation !== "read" && options.models) {
//                    return {models: kendo.stringify(options.models)};
//                }
//            }
//        },
//        batch: true,
//        pageSize: 20,
//        schema: {
//            model: {
//                id: "id",
//                fields: {
//                    tournamentId: { editable: false, nullable: true },
//                    name: { validation: { required: true } },
//                    startDate: { type: "date", },
//                    endDate: { type: "date" },
//                    active: { type: "boolean" }
//                }
//            }
//        }
//    });
//
//    $("#gridCategories").kendoGrid({
//        dataSource: dataSourceCategories,
//        pageable: true,
//        height: 500,
//        toolbar: ["create"],
//        columns: [
//            { field:"name", title: "Nombre" },            
//            { field: "entryDate", title:"Fecha de inicio" },
//            { field: "lastUpdate", title:"Ultima modificación"},            
//            { command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }],
//        editable: "popup"
//    });


      
});
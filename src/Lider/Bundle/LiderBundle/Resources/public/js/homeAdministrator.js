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
    
    dataSourceTournaments = new kendo.data.DataSource({
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
        dataSource: dataSourceTournaments,
        pageable: true,
        height: 500,
        toolbar: ["create"],
        columns: [
            { field:"name", title: "Nombre" },
            { field: "startDate", title:"Fecha de inicio" },
            { field: "endDate", title:"Fecha de fin"},
            { field: "active", title: "Activo"},
            { command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }],
        editable: "popup"
    });


    dataSourcePlayers = new kendo.data.DataSource({
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

    $("#gridPlayers").kendoGrid({
        dataSource: dataSourcePlayers,
        pageable: true,
        height: 500,
        toolbar: ["create"],
        columns: [
            { field:"name", title: "Nombre" },
            { field: "lastname", title:"Apellido" },
            { field: "office_id", title:"Oficina"},
            { field: "team_id", title: "Equipo"},
            { command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }],
        editable: "popup"
    });

    dataSourceGroups = new kendo.data.DataSource({
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

    $("#gridGroups").kendoGrid({
        dataSource: dataSourceGroups,
        pageable: true,
        height: 500,
        toolbar: ["create"],
        columns: [
            { field:"name", title: "Nombre" },
            { field:"tournament_id", title: "Toreo" },
            { field: "entryDate", title:"Fecha de inicio" },
            { field: "lastUpdate", title:"Ultima modificación"},
            { field: "active", title: "Activo"},
            { command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }],
        editable: "popup"
    });


    dataSourceCategories = new kendo.data.DataSource({
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

    $("#gridCategories").kendoGrid({
        dataSource: dataSourceCategories,
        pageable: true,
        height: 500,
        toolbar: ["create"],
        columns: [
            { field:"name", title: "Nombre" },            
            { field: "entryDate", title:"Fecha de inicio" },
            { field: "lastUpdate", title:"Ultima modificación"},            
            { command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }],
        editable: "popup"
    });


    dataSourceQuestions = new kendo.data.DataSource({
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
                    category: { defaultValue: { CategoryID: 1, CategoryName: ""} },
                }
            }
        }
    });

    $("#gridQuestions").kendoGrid({
        dataSource: dataSourceQuestions,
        pageable: true,
        height: 500,
        toolbar: ["create"],
        columns: [
            { field:"question", title: "Pregunta" },            
            //{ field: "category_id", title:"Categoria" }, 
            { field: "category", title: "Category", width: "180px", 
            editor: categoryDropDownEditor, template: "#=category.CategoryName#" },             
            { command: ["edit", "destroy"], title: "&nbsp;", width: "200px" }],
        editable: "popup"
    });

    function categoryDropDownEditor(container, options) {
    $('<input required data-text-field="CategoryName" data-value-field="CategoryID" data-bind="value:' + options.field + '"/>')
        .appendTo(container)
        .kendoDropDownList({
            autoBind: false,
            dataSource: {
                type: "odata",
                transport: {
                    read: "http://demos.telerik.com/kendo-ui/service/Northwind.svc/Categories"
                }
            }
        });
    }    
});
var groupBuilder = function () {

	if(typeof this.constructor == "function"){
		this.constructor.apply(this, arguments);
	}
}

groupBuilder.prototype = {

	constructor: function (container, min, max) {
		console.log("djiajdi")
		this.container = container;
		this.getData(min, max);
	},

	getData: function  (min, max) {
			
		var me = this;

		me.cities = {},

		parameters = {
			type: "GET",     
            url: "home/team/generate?max="+max+"&min="+min,		            
            contentType: 'application/json',
            dataType: "json",
            success: function(data){
            	cities = data['cities'];

            	me.generateCity(cities);
            },
            error: function(){},
		};
			
		var ajax = $.ajax(parameters);			
		
	},
}
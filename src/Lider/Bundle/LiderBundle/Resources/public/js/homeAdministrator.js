$(document).ready(function () {

    $("a", "#menu").click(function (e) {
    	e.preventDefault();
//		
    	var me = $(this);
    	if(me.attr("data-url")){
    		//Backbone.history.navigate(me.attr("data-url"), true);
    		window.location = "#"+me.attr("data-url");
    	}
    	
    });
});

function parseDate(rec){	
	if(_.isString(rec)){		
		return new Date(rec);		                	
	}else if(_.isDate(rec)){		
		return rec;
	}else if(_.isObject(rec)){		
		return new Date(rec.date);		                	
	}	
}
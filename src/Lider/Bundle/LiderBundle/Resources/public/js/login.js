$(document).ready(function () {

	formData = new FormData();
	formData.append(name, value);

	form.submit(function(e){
	
	alert("dadad")
	e.preventDefault();

	$.ajax({
		type: "POST",     
		url: "",
        data: formData,
        contentType: false,
        processData: false,

		config.success = function(obj, textStatus, jqXHR){
			if(obj["message"]){
		    	notification.show({
	                title: "Success",
                    message: obj.message
                }, "success");
			}
		},
		
		config.error = function(xhr, textStatus, jqXHR){
			try{
		    	var obj = jQuery.parseJSON(xhr.responseText);
		    	notification.show({
                    title: "Error",
                    message: obj.message
                }, "error");
	    	}catch(ex){
	    		notification.show({
                    title: "Error",
                    message: "Server error"
                }, "error");
	    	}

	    	if(xhr.status == 401){
				  window.location= "/index";
			}
		});

	});
}
$(document).ready(function () {

	var form = $("form[role=form]");

	form.submit(function(e){
		
		e.preventDefault();

		formData = new FormData();

 		var name = form.find("#email").val();
 		var password = form.find("#password").val();


		formData.append("_username", name);
		formData.append("_password", password);
		

		$.ajax({
			type: "POST",     
			url: "login-check",
	        data: formData,
	        contentType: false,
	        processData: false,

			success : function(obj, textStatus, jqXHR){
				if(obj["message"]){
			    	notification.show({
		                title: "Success",
	                    message: obj.message
	                }, "success");
				}
			},
			
			error : function(xhr, textStatus, jqXHR){
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

			}

		});
	})
})
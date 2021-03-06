$(document).ready(function () {
	 var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	    po.src = 'https://apis.google.com/js/client:plusone.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	
});

function signinCallback(authResult) {
	debugger
	  if(authResult['access_token']) {
		  console.log(authResult)
		  var formData = new FormData();
		  formData.append("access_token", authResult['access_token']);
		  formData.append("code", authResult['code']);
		  formData.append("authenticated", true)
		  
		  var form = $("form", ".login-form");
		  form.attr("action", "check/google");
		  form.append($("<input>")
				  .attr("type", "hidden")
				  .attr("name", "access_token")
				  .attr("value", authResult['access_token']));
		  form.append($("<input>")
				  .attr("type", "hidden")
				  .attr("name", "code")
				  .attr("value", authResult['code']));
		  
		  form.submit();
		  
		  /*$.ajax({
		  	type: "POST",     
			url: "login-check/google",
			data: formData,
			contentType: false,
            processData: false,
			//contentType: 'application/json',
            //dataType: "json",
            statusCode: {
                302: function(jqXHR) {
                    console.log(jqXHR)
                }
            },
			success: function(data){
				console.log(data)
				if(data["success"] && data.success)
					window.location = "home";
			},
			error: function(xhr, status, error) {
		    	loader.hide();
		    	try{
			    	var obj = jQuery.parseJSON(xhr.responseText);
	            	$.notify(obj.message, { 
	            		className:"error", 
	            		globalPosition:"top center" 
	            	});
		    	}catch(ex){
		    		$.notify("Error", { 
	            		className:"error", 
	            		globalPosition:"top center" 
	            	});
		    	}
	    	}
		});*/
		  
	  } else if (authResult['error']) {
	    // Se ha producido un error.
	    // Posibles códigos de error:
	    //   "access_denied": el usuario ha denegado el acceso a la aplicación.
	    //   "immediate_failed": no se ha podido dar acceso al usuario de forma automática.
	    // console.log('There was an error: ' + authResult['error']);
	  }
}

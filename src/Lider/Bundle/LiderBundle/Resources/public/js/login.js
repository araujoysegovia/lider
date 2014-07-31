$(document).ready(function () {
	 var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	    po.src = 'https://apis.google.com/js/client:plusone.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	
});

function signinCallback(authResult) {
	
	  if(authResult['access_token']) {
		  console.log(authResult)
		  $.ajax({
			  	type: "POST",     
				url: "login-check",
				data: {
					code: authResult['code'] 
				},
				contentType: 'application/json',
				dataType: "json",
				success: function(){
					
				},
				error: function(){
					
				}
		});		  
	  } else if (authResult['error']) {
	    // Se ha producido un error.
	    // Posibles códigos de error:
	    //   "access_denied": el usuario ha denegado el acceso a la aplicación.
	    //   "immediate_failed": no se ha podido dar acceso al usuario de forma automática.
	    // console.log('There was an error: ' + authResult['error']);
	  }
}

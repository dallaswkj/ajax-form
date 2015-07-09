$().ready(function(){
	$('#myForm').validate({
		rules: {
		    name: {
		    	required: true,
		    	minlength: 2
		    },

		    dob: {
		    	required: true,
		    	ozDate: true
		    },

		    add: {
		    	required: true
		    },

		    phone: {
		    	required: true,
		    	ozPhone: true

		    },

		    email: {
		    	required: true,
		    	email: true
		    },

		    message: {
		    	required: true
		    }
		},
  		messages: {
		    name: {
		    	required: "Please enter your full name",
		    	minlength: jQuery.validator.format("At least {0} characters required!")
		    },

		    dob: {
		    	required: 'Please enter your Date of Birth'
		    },

		    add: {
		    	required: 'Please enter your Address'
		    },

		    phone: {
		    	required: "Please enter your phone number"
		    },

		    email: {
		    	required: "Please enter your Email address",
		    	email: "Please enter a validated Email"
		    },

		    message: {
		    	required: "Please enter your message"
		    }
		},
		//
		//success function ajax post
		//
		submitHandler: function(form) {
			//verify recaptcha checked
			var v = grecaptcha.getResponse();
			if (v.length != 0) {
				var formdata = {
					'name' : $('input[name=name]').val(),
					'dob'  : $('input[name=dob]').val(),
					'add'  : $('input[name=add]').val(),
					'phone': $('input[name=phone]').val(),
					'email': $('input[name=email]').val(),
					'message': $('#message').val(),
					'g-recaptcha-response':$('#g-recaptcha-response').val()
				};
	/*
	           $(form).ajaxSubmit({
	                url:"server.php",
	                type:"POST",
	                success: function(){
	                    alert(data);
	                    $('#myForm').hide();
	                    $('#sent').show();
	              	}
	            });
	*/

				$.ajax({
					type: "POST",
				  	url: "server.php",
				  	data: formdata,
				  	datatype: 'json',
					success: function(data){
						$('#sent').empty();
						if(!data){
							$('#myForm').each(function(){
    							this.reset();
							});
							grecaptcha.reset();
			                $('#sent').html('Successfully Submitted');
						}
						else{
							$('#myForm').each(function(){
    							this.reset();
							});
							grecaptcha.reset();
			                $('#sent').html('Error Please try again!');
						}
		                
				  	}
				});
			};
        }

	});

});

/*
customized validate rules
*/
//var reg = '/^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
///^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/ DD-MM-YYYY
jQuery.validator.addMethod('ozDate', function(val, element){
	return this.optional(element)|| /^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/.test(val);
}, 'Please enter a validated Date! dd-mm-yyyy');

//au phone No: /^\({0,1}((0|\+61)(2|4|3|7|8)){0,1}\){0,1}(\ |-){0,1}[0-9]{2}(\ |-){0,1}[0-9]{2}(\ |-){0,1}[0-9]{1}(\ |-){0,1}[0-9]{3}$/
//  /^0[0-9]\s?\d{4}\s?\d{4}$/
jQuery.validator.addMethod('ozPhone', function(val, element){
	return this.optional(element)|| /^0[0-9]\s?\d{4}\s?\d{4}$/.test(val);
}, 'Please enter a validated Number! 0300000000 or 03 0000 0000');
/*
check recaptcha before form submit
*/
function get_action(form) {
    var v = grecaptcha.getResponse();
    if(v.length == 0)
    {
        document.getElementById('captcha').innerHTML="You can't leave Captcha Code empty";
        return false;
    }
    if(v.length != 0)
    {
    	document.getElementById('captcha').innerHTML="";
        return true; 
    }
}
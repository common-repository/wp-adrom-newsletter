jQuery(function($) {'use strict',

	setupDatepicker = function(){		
		$( ".jqueryDatepicker" ).datepicker({
			selectOtherMonths: true,
			changeMonth: true,
			changeYear: true,
			dateFormat: "dd.mm.yy",
			altFormat: "mm-dd-yyyy"
		});
	}

	setupSingoutWithHashForm = function(){
		
		var form = $('.ajax-signout-form-with-hash');
		var url = form.find('.wp_adrom_newsletter_wordpress_ajax_url').text();
		form.submit(function(event){
		
			event.preventDefault();
			//var form_status = $('<div class="form_status"></div>');
			$.ajax({
				url: url,
				data: form.serialize(),
				type: "POST",
				beforeSend: function(){
					form.find('.preloadingForm').css('display','block');
				}
			}).done(function(data){
				
				var data = JSON.parse(data);
				var className = data.type == "error" ? "alert-danger" :  "alert-success";
				var message = data.message;
				form.find('.preloadingForm').hide();
				form.find('.status').css('display','block').removeClass("alert-danger alert-success");
				form.find('.status').css('display','block').addClass(className).html(message);
				form.trigger("reset");
				form.find('.formWrapper').hide();
			}).fail(function(data){
				var data = JSON.parse(data);
				var message = data.statusText;
				form.find('.preloadingForm').hide();
				form.find('.status').css('display','block').addClass("alert-danger").html(message);
			});
		});
	}
	
	setupSingoutForm = function(){		
		var form = $('.ajax-signout-form');
		var url = form.find('.wp_adrom_newsletter_wordpress_ajax_url').text();//$('.wp_adrom_newsletter_wordpress_ajax_url').text();
		console.log('url',url);
		form.submit(function(event){
		
			event.preventDefault();
			//var form_status = form.find('<div class="form_status"></div>');
			$.ajax({
				url: url,
				data: form.serialize(),
				type: "POST",
				beforeSend: function(){										
					form.find('.preloadingForm').css('display','block');
				}
			}).done(function(data){							
				var data = JSON.parse(data);
				var className = data.type == "error" ? "alert-danger" :  "alert-success";
				var message = data.message;				
				form.find('.preloadingForm').hide();
				form.find('.status').css('display','block').removeClass("alert-danger alert-success");
				form.find('.status').css('display','block').addClass(className).html(message);				
				form.trigger("reset");				
				form.find('.formWrapper').hide();
			}).fail(function(data){			
				var data = JSON.parse(data);
				var message = data.statusText;
				form.find('.preloadingForm').hide();
				form.find('.status').css('display','block').addClass("alert-danger").html(message);
			});
		});
				
	}
	
	setupSingInForm = function(){	
		var form = $('.ajax-signin-form');
		var url = form.find('.wp_adrom_newsletter_wordpress_ajax_url').text();
		
		form.submit(function(event){
			event.preventDefault();
			//var form_status = $('<div class="form_status"></div>');
			
			$.ajax({
				url: url,
				data: form.serialize(),
				type: "POST",
				beforeSend: function(){
					form.find('.preloadingForm').css('display','block');
				}
			}).done(function(data){			
				var data = JSON.parse(data);
				var className = data.type == "error" ? "alert-danger" :  "neutral";
				var message = data.message;
				form.find('.preloadingForm').hide();
				form.find('.status').css('display','block').removeClass("alert-danger alert-success alert");
				form.find('.status').css('display','block').addClass(className).html(message);
				form.trigger("reset");
				form.find('.formWrapper').hide();
			}).fail(function(data){
				var data = JSON.parse(data);
				var message = data.statusText;
				form.find('.preloadingForm').hide();
				form.find('.status').css('display','block').addClass("alert-danger").html(message);
			});
		});
				
	}
	
	setupValidateSignIn = function(){
		
		if($('#validate_sign_in').length == 0){ 
			return;
		}
		
		var confirmHash = $('.confirmHash').text();		
		$.ajax({
				url: "/wp-admin/admin-ajax.php",
				data: {
					action: "process_wp_adrom_newsletter_validate_sign_in",
					ConfirmHash: confirmHash
				},
				type: "POST",
				beforeSend: function(){
					$('.preloadingForm').css('display','block');
				}
			}).done(function(data){							
				var data = JSON.parse(data);
				var className = data.type == "error" ? "alert-danger" :  "alert-success";
				var message = data.message;
				$('.preloadingForm').hide();
				$('.status').css('display','block').removeClass("alert-danger alert-success");
				$('.status').css('display','block').addClass(className).html(message);				
			}).fail(function(data){				
				var data = JSON.parse(data);
				var message = data.statusText;
				$('.preloadingForm').hide();
				$('.status').css('display','block').addClass("alert-danger").html(message);
			});
			
	}
	
	trimEmailInputField = function(){	
		$('.emailInput').bind('blur', function(){
			  $(this).val(  $.trim($(this).val()));			  
			  //var form = $('.ajax-signin-form');
			  //form.valid();
		});	
	}
	
	function readyFunctions(){
		trimEmailInputField();
		setupSingoutForm();
		setupSingoutWithHashForm();
		setupSingInForm();
		//setupValidateSignIn();
		setupDatepicker();
	}
	
	$( document ).ready(readyFunctions);
	
});
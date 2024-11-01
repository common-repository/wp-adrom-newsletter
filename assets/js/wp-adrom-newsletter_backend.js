jQuery(function($) {'use strict',

	setupColorpickers = function(){		
	
		$('.wp_adrom_newsletter_signin_template_background_color').wpColorPicker({
			palettes: false,			
		});
		
		$('.wp_adrom_newsletter_signin_template_button_color').wpColorPicker({
			palettes: false,
		});
	
	}
	
	setupFormTabs = function(){
		var form = $('.wp_adrom_newsletter_be_form');		
		var tabbable = form.find('.tabbable');		
		var clickableTabs = $('.wp_adrom_newsletter_tab');
		
		$('.wp_adrom_newsletter_tab').on('click', function(e){			
			e.preventDefault();			
			var tabName = $(this).data('tabname');			
			//remove active class from headerTab
			clickableTabs.removeClass('nav-tab-active');
			//add active class from clicked headerTab
			$(this).addClass('nav-tab-active');			
			//remove 'hidden' class from tabcontent
			tabbable.removeClass('hidden');
			//add hidden class on other tabcontents
			tabbable.not('#' + tabName).addClass('hidden');						
			location.hash = "tab=" + tabName;			 
			return false;
		});
		
		//check for active tab
		/*
		var tabHash = location.hash;
		console.log('tabHash', tabHash);
		var currentTab = tabHash.replace("#tab=",'');
		
		if(currentTab.length == 0){
			//no active tab found, take first tab
			//$('.wp_adrom_newsletter_tab').first().trigger('click');
		} else {
			//trigger active tab
			//$('.wp_adrom_newsletter_tab[data-tabname="'+currentTab+'"]').trigger('click');
		}
		*/
		$('.wp_adrom_newsletter_tab').first().trigger('click');
	}
	
	setupToastr = function(){
		toastr.options = {
			"closeButton": false,
			"debug": false,
			"positionClass": "toast-bottom-right",
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "5000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		}
	}
	
	validateForms = function(){
		
		if($('.wp_adrom_newsletter_be_form').length == 0){ return;}
		
		var isRequiredText = $('.wp_adrom_newsletter_input_is_required_text').text();
		
		$('#submit').on('click', function(e){			
			$('.wp_adrom_newsletter_be_form').find('input, textarea, select').each(function(i,el) {        				
				//only inputs with required classes
				if( $(el).hasClass('required') ){			
					//remove requiredError
					$(el).removeClass('requiredError');
					//check its value
					
					if($(el).val().length == 0 || ($(el).is('select') && $(el).val() == -1) ){
						$(el).addClass('requiredError');						
						e.preventDefault();												
						var requiredValue = $(el).parents('tr').find('th').text().replace('*','').replace(' *','');
						toastr.error('"' + requiredValue + '" ' + isRequiredText);
					}					
				}								
			});		
		});	
		
	}
	
	function readyFunctions(){
		setupColorpickers();
		setupFormTabs();
		setupToastr();
		validateForms();
	}
	
	$( document ).ready(readyFunctions);
	
});
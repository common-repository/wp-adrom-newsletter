<?php

class adromNewsletterRenderForms {

	public $ang;
	
	public function __construct() {
	
		$this->ang = new adromNewsletterGlobal();
		
		//add shortcodes		
		//render signout form
		add_shortcode( 'render_wp_adrom_newsletter_form_signout', array($this, 'render_wp_adrom_newsletter_form_signout_func') );
		
		//render signin form
		add_shortcode( 'render_wp_adrom_newsletter_form_signin', array($this, 'render_wp_adrom_newsletter_form_signin_func') );
		
		//render validate signin
		add_shortcode( 'render_wp_adrom_newsletter_form_validate_signin', array($this, 'render_wp_adrom_newsletter_form_validate_signin_func') );
	}
	
	public function prepare_wp_adrom_newsletter_form_signout(){
		
		$customDecryptionParams = $this->ang->optoutManagmentInitSettings;				
		$emailSystemClientId = $this->ang->get_option("signout_emailSystemClientId");		
		$apikey = $this->ang->get_option("general_apikey");
		$apiurl = $this->ang->get_option("general_apiurl");
		
		$om = new OptoutManagement(0,$emailSystemClientId, "", $apiurl, $apikey, $customDecryptionParams);
		
		//$uhash = (isset($_GET['u'])) ? $_GET['u'] : $_POST['u'];
		$uhash = "";
		if(isset($_POST['u'])){ 
			$uhash = $_POST['u']; 
		} else if(isset($_GET['u'])){
			$uhash = $_GET['u']; 
		}
		
		if(isset($_POST['c_email']) && $uhash == '') { $uhash = $_POST['c_email']; }
		
		if($uhash == ""){
			return false;
		}
		
		$formValues = array(
			"error" => false,
			'uhash' => $uhash,	
			'message' => '',
			'text' => '',
			'foundEmail' => '',
			'uhash' => ''
		);
		
		if(!$om->init($uhash))
		{	
			$formValues['error'] = true;			
			$error_message_wrong_link = $this->ang->get_option('signout_error_wrong_link_message');
			$formValues['message'] = $error_message_wrong_link;	
			//die('IF');
		} 
		else 
		{		
			$formValues['formAction'] = "process_wp_adrom_newsletter_form_sign_out_with_hash";
			$formValues['formName'] = "ajax-signout-form-with-hash";
			
			$formValues['foundEmail'] = $om->getEmail();			
			$formValues['uhash'] = $uhash;
			
			if(!$om->isPortalOptout)
			{
				$category = $om->retrieveCategoryTitle();				
				//$category = "FakeCat";
				
				$formValues['text'] = sprintf(__('You will be signed out from the newsletter-list <b>%s</b> after you clicked on the "Signout"-Button', 'wp-adrom-newsletter'), $category);				
			}
		}
		
		return $formValues;
	}
	
	public function check_for_missing_settings($params){
		$returner = false;
		//if(is_user_logged_in()){ $params['needed_settings'][] = "FakeSetting"; }		
		foreach($params['needed_settings'] as $needed_setting){
			$setting = $this->ang->get_option($needed_setting);					

			if($setting == ""){			
				ob_start();			
				echo '<div class="status alert alert-danger" style="display: block;"><p>required settings missing under "'. $params['type'] .'" ('. $needed_setting .')</p></div>';
				$returner = ob_get_clean();
			}
		}
		return $returner;
	}
	
	//render signout form
	public function render_wp_adrom_newsletter_form_signout_func( $atts ){					

		$checkMissingSettings = array(
			'needed_settings' => array(
				"signout_ok_message",
				"signout_error_message",
				"signout_error_wrong_link_message",
				"signout_error_failed_automated_message",
				"general_apikey",
				"general_apiurl",
				"signout_emailSystemClientId",
				"signout_password",
				"signout_salt",
				"signout_iterations",
				"signout_keylength",
				"signout_iv"
			),
			'type' => 'WP adRom Signout Options'
		);
		
		$missingSettings = $this->check_for_missing_settings($checkMissingSettings);		
		if($missingSettings != false){return $missingSettings;}		
		
		$formValues = $this->prepare_wp_adrom_newsletter_form_signout();
		$formAction = "process_wp_adrom_newsletter_form_sign_out";
		$formName = "ajax-signout-form";
		$innerFormStyle= $formValues['error'] ? "style='display:none;'": '';
		
		if(isset($formValues['uhash']) && $formValues['uhash'] != ''){ 			
			//print_R($formValues);
			$formAction = $formValues['formAction'];
			$formName = $formValues['formName'];			
		}

		ob_start();
				
		?>
<form id="wp-adrom-newsletter-form-sign-out" class="wp-adrom-newsletter-form <?php echo $formName;?>" name="wp-adrom-newsletter-form" method="post" action="">				
	<div <?php echo $innerFormStyle;?>>
		<div class="formWrapper">
			
			<?php 
				if(isset($formValues['text']) && $formValues['text'] != ''){ 
					echo '<p style="clear:both;">' . $formValues['text'] . '</p>';
				} else {
					echo '<p style="clear:both;">' . __('You will be signed out from the newsletter after you clicked on the "Signout"-Button', 'wp-adrom-newsletter') . '</p>';
				}
			?>
			
			<input class="wp_adrom_newsletter_signout_email" type="email" class="emailInput" name="wp_adrom_newsletter_signout_email" required="required" pattern="[a-z0-9A-Z._%+-]+@[a-z0-9A-Z.-]+\.[a-zA-Z]{2,4}$" placeholder="<?php _e('E-Mail-Adress', 'wp-adrom-newsletter');?>*" value="<?php echo $formValues['foundEmail']; ?>"
			<?php echo isset($formValues['foundEmail']) ? "readonly" : ''; ?>
			oninput="setCustomValidity('')" oninvalid="this.setCustomValidity('<?php _e('This field is required','wp-adrom-newsletter'); ?>')" 
			>
						
			<input type="submit" name="submit" class="submitButton" required="required" value="<?php _e('Signout', 'wp-adrom-newsletter');?>">
			
			<?php 		
				//print_R($formValues);			
				if(isset($formValues) && $formValues['foundEmail'] != '' && $formValues['text'] != ''){					
					$signoutLink = get_page_link( get_option("wp_adrom_newsletter_signout_signoutpage"));
					$signoutLink .= "?u=" . $formValues['foundEmail'];
					$blogName = get_bloginfo("name");
					echo "<div class='clear'></div>";					
					printf(__("If you don't want to receive newsletters from <b>%s</b> click <a href='%s'>here</a> to unsubscribe.", "wp-adrom-newsletter"), $blogName, $signoutLink);
				}
			?>
			
			<span class="wp_adrom_newsletter_wordpress_ajax_url" style="display:none;"><?php echo admin_url('admin-ajax.php'); ?></span>
		</div>
		
		<div class="preloadingForm" style="display: none;"><div class="loader"></div></div>
		
		<div class="clear"></div>
	</div>	
	
	<?php 
		if(isset($formValues['uhash'])){
			echo "<input type='hidden' name='uhash' value='". $formValues['uhash'] ."' />";
		}
	?>
	
	<div class="status alert <?php echo $formValues['error'] ? 'alert-danger' : '';?>" style="display: <?php echo $formValues['error'] ? 'block' : 'none'; ?>;">
	<?php echo wpautop($formValues['message']);?>
	</div>
	
	<input type="hidden" name="action" value="<?php echo $formAction;?>">
	<?php wp_nonce_field( 'wp_adrom_newsletter_signout_nonce' ); ?>
		
</form>
		<?php		
		
		return ob_get_clean();  	
	}
	
	//render signin form
	public function render_wp_adrom_newsletter_form_signin_func( $atts ){			
		
		$checkMissingSettings = array(
			'needed_settings' => array(
				"signin_ok_message",
				"signin_error_message",
				"confirmation_ok_message",
				"confirmation_error_message",
				"signin_template_confirmation_url",				
				"signin_template_logo_url",
				"signin_template_logo_alt",
				"signin_template_background_color",
				"signin_template_button_color",
				"signin_template_website_url",
				"confirmation_terms_and_conditions"
			),
			'type' => 'WP adRom Signin Options'
		);
		$missingSettings = $this->check_for_missing_settings($checkMissingSettings);		
		if($missingSettings != false){return $missingSettings;}		
		//include plugins_url() . '/wp-adrom-newsletter/frontend/signin_form.php';				
		$selectedSignInRenderFields = $this->ang->get_option('signin_render', true);
		$selectedSignInRenderFields["EmailAddress"] = 1; //this is predefined (and cannot be unselected in the backend)		
		
		ob_start();
		?>
<form id="wp-adrom-newsletter-form-sign-in" class="ajax-signin-form wp-adrom-newsletter-form" name="wp-adrom-newsletter-form" method="post" action="">				
		
		<?php		
			$availableRenderSignInFields = $this->ang->renderSignInFields;					
			foreach($availableRenderSignInFields as $aKey => $availableRenderSignInField){		
				foreach($selectedSignInRenderFields as $key => $selectedSignInRenderField){									
					if($aKey == $key ){
					
						$inputName = "";
						$label_suffix = "";
						$isEmailField = false;
						if($key == "EmailAddress"){
							$inputName = $key;
							$label_suffix = "*";
							$isEmailField = true;
						} else {
							$inputName = "Person[". $key ."]";
						}
						
						?>
						<div class="formWrapper">
							<div class="formLabel"><?php _e($availableRenderSignInField['name'], 'wp-adrom-newsletter'); echo " ". $label_suffix;?></div>							
							<?php if($key == "Gender"){ ?>
								<div class="formInput radioGroup">
									<div style="float:left;">
										<input type="radio" id="gender_m" name="Person[Gender]" value="1" checked="checked"> <label for="gender_m"><?php _e('Male', 'wp-adrom-newsletter');?></label>
									</div>
									<div style="float:left;">
										<input type="radio" id="gender_w" name="Person[Gender]" value="2"><label for="gender_w"><?php _e('Female', 'wp-adrom-newsletter');?></label>
									</div>
								</div>
							<?php } else {?>
								<div class="formInput">
									
									<input class="<?php if($isEmailField){echo "emailInput";}?> wp_adrom_newsletter_signin_<?php echo $key; ?> <?php echo $availableRenderSignInField['className'];?>" type="text" name="<?php echo $inputName;?>" 
									<?php 
										//echo $availableRenderSignInField["required"] == true ? "required" : ""; 
										if($availableRenderSignInField["required"] == true){
											echo 'required ';											
											if($isEmailField){
												echo 'oninput="setCustomValidity(\'\')" oninvalid="this.setCustomValidity(\''. __('This field is required','wp-adrom-newsletter') .'\')" ';
											}											
										}
									?> 
									<?php echo $availableRenderSignInField['regexpattern'] != "" ? 'pattern="'.$availableRenderSignInField['regexpattern'].'"' : ''; ?>
									placeholder="<?php echo $availableRenderSignInField['placeholder'];?>" />
								</div>
							<?php }?>								
						</div>
						<?php
					}
				}				
			}
		
		?>
		
		<?php if($this->ang->get_option('signin_render_gtc_required')){ ?>
		<div class="formWrapper">		
			<div class="formLabel"> </div>						
			<div class="formInput">
				<input style="float:left;" id="GTC" class="wp_adrom_newsletter_signin_agb" type="checkbox" name="GTC" value="1" required="required" oninvalid="this.setCustomValidity('<?php _e('This box must be checked','wp-adrom-newsletter')?>')" onchange="setCustomValidity('')" />				
				
				<label class="desc" for="GTC" style="padding:8px;float:left;">										
					<?php printf(__('Subscribe to our newsletter<br><span style="font-weight:normal;">This consent may be revoked at any time on the website or at the end of the newsletter.</span>', 'wp-adrom-newsletter'), get_page_link( get_option("wp_adrom_newsletter_signout_signoutpage")) );
					?>
				</label>
				
			</div>
		</div>
		<?php } ?>
		<input type="hidden" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" name="IpAddress" />
		<span class="wp_adrom_newsletter_wordpress_ajax_url" style="display:none;"><?php echo admin_url('admin-ajax.php'); ?></span>
		<div class="clear"></div>
			
		<div class="formWrapper">
			<div class="formLabel"></div>
			<div class="formInput">
				<input type="submit" name="submit" class="submitButton" required="required" value="<?php _e('Signin', 'wp-adrom-newsletter');?>">
			</div>
		</div>
				
		<div class="preloadingForm" style="display: none;"><div class="loader"></div></div>
		
		<div class="clear"></div>
		
		<div class="status alert" style="display: none;"></div>
		
		<input type="hidden" name="action" value="process_wp_adrom_newsletter_form_sign_in">
		<?php wp_nonce_field( 'wp_adrom_newsletter_signin_nonce' ); ?>
		
</form>
		<?php
		return ob_get_clean();  		
	}
	
	//render validate signin
	public function render_wp_adrom_newsletter_form_validate_signin_func( $atts ){	
		ob_start();
		$confirmHash = isset($_GET['hash']) ? $_GET['hash'] : '';	
		return ob_get_clean();  		
	}
}

?>
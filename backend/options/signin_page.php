<div class="wrap">
<h2>WP adRom Newsletter: SignIn</h2>

<?php 
$ang = new adromNewsletterGlobal(); 
$tabs = array( 
	'formMessage' => __('Form-Messages',  'wp-adrom-newsletter'), 
	'formSettings' => __('Form settings',  'wp-adrom-newsletter'),	
	'templateDetails' => 'Template Details',
	'confirmationPageSettings' => __('Confirmation-Page', 'wp-adrom-newsletter'),	
);
echo $ang->generate_backend_tabs($tabs);
?>

<form method="post" action="options.php" class="wp_adrom_newsletter_be_form">
    <?php settings_fields( 'wp-adroom-newsletter-options-signin' ); ?>
    <?php do_settings_sections( 'wp-adroom-newsletter-options-signin' ); ?>
	
	<div id="formMessage" class="tabbable">
		
		<h3 class="title"><?php _e('Form-Messages', 'wp-adrom-newsletter');?></h3>
		<p class="description" id="tagline-description">
			<?php _e('Available placeholders', 'wp-adrom-newsletter');?>: <br>
			<kbd>{EMAIL_ADDRESS}</kbd>
		</p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">OK *</th>
				<td>					
					<textarea name="<?php echo $ang->optionprefix; ?>signin_ok_message" class="large-text required" rows="3"><?php echo $ang->get_option('signin_ok_message'); ?></textarea>						
				</td>
			</tr>         
			
			<tr valign="top">
				<th scope="row">Error *</th>
				<td>
					<textarea name="<?php echo $ang->optionprefix; ?>signin_error_message" class="large-text required" rows="3"><?php echo $ang->get_option('signin_error_message'); ?></textarea>			
				</td>
			</tr>
			
		</table>
	</div>
	
	<div id="formSettings" class="tabbable">
		<h3 class="title"><?php _e('Form settings', 'wp-adrom-newsletter');?></h3>
		<p class="description" id="tagline-description">
		<?php _e('define which fields should be rendered', 'wp-adrom-newsletter');?>
		</p>
		<table class="form-table">
			<?php 			
				$selectedSignInRenderFields = $ang->get_option('signin_render', true);//get_option('wp_adrom_newsletter_signin_render');				
				$renderfields = $ang->renderSignInFields;							
				foreach($renderfields as $key => $renderfield){			
					$disabled = "";					
					$selected = '';
					
					if(isset($selectedSignInRenderFields[$key])){
						$selected = "checked";		
					}
					
					if($key == "EmailAddress"){
						$disabled = "disabled";
						$selected = "checked";
						$renderfield['name'] = $renderfield['name'] . " *";
					}
					
					?>
					<tr valign="top">
						<th scope="row"><?php echo $renderfield['name']; ?></th>
						<td>
							<input name="<?php echo $ang->optionprefix; ?>signin_render[<?php echo $key; ?>]" type="checkbox" value="1" <?php echo $selected; ?> <?php echo $disabled;?>>					
						</td>
					</tr>
					<?php
				}
				
				?>
				
				<tr valign="top"><td>&nbsp;</td></tr><!--empty row here-->
				
				<tr valign="top">
					<th scope="row"><?php _e('global terms and conditions', 'wp-adrom-newsletter') ?></th>
					<td>
						<input name="<?php echo $ang->optionprefix; ?>signin_render_gtc_required" type="checkbox" value="1" <?php echo $ang->get_option('signin_render_gtc_required') ? 'checked' : ''; ?> <?php echo $disabled;?>>		
						<p class="description" id="tagline-description"><?php _e('define if "global terms & conditions"-checkbox must be checked to submit the form', 'wp-adrom-newsletter');?></p>						
					</td>
				</tr>
						
		</table>
		
	</div>
	
	<div id="templateDetails" class="tabbable">
		<h3 class="title">Template Details</h3>
			
		<table class="form-table">
					
			<tr valign="top">
				<th scope="row"><?php _e('Background Color', 'wp-adrom-newsletter');?></th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>signin_template_background_color" class="regular-text ltr wp_adrom_newsletter_signin_template_background_color" value="<?php echo $ang->get_option('signin_template_background_color'); ?>"/>				
				</td>
			</tr>  
		
			<tr valign="top">
				<th scope="row"><?php _e('Button Color', 'wp-adrom-newsletter');?></th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>signin_template_button_color" class="regular-text ltr wp_adrom_newsletter_signin_template_button_color" value="<?php echo $ang->get_option('signin_template_button_color'); ?>"/>								
				</td>
			</tr> 
			
			<tr valign="top">
				<th scope="row"><?php _e('Logo Url', 'wp-adrom-newsletter');?> *</th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>signin_template_logo_url" class="regular-text ltr  required" value="<?php echo $ang->get_option('signin_template_logo_url'); ?>"/>
					<p class="description" id="tagline-description">https://www.link-to-your-logo.com/logo.png</p>
				</td>
			</tr>   

			<tr valign="top">
				<th scope="row"><?php _e('alternative logo text', 'wp-adrom-newsletter');?> *</th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>signin_template_logo_alt" class="regular-text ltr required" value="<?php echo $ang->get_option('signin_template_logo_alt'); ?>"/>
					<p class="description" id="tagline-description"><?php _e('alternative logo text', 'wp-adrom-newsletter');?></p>
				</td>
			</tr>   
			 
			
			<tr valign="top">
				<th scope="row"><?php _e('Website Url', 'wp-adrom-newsletter');?> *</th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>signin_template_website_url" class="regular-text ltr" value="<?php echo get_site_url();?>" readonly/>
					<p class="description" id="tagline-description"><?php _e('can not be edited', 'wp-adrom-newsletter');?></p>
				</td>
			</tr>   
			
			<tr valign="top">
				<th scope="row"><?php _e('Terms & condition page', 'wp-adrom-newsletter');?></th>
				<td>				
					<?php 
					$args = array(
						'depth'                 => 0,
						'child_of'              => 0,
						'selected'              => $ang->get_option('confirmation_terms_and_conditions'),
						'echo'                  => 1,
						'name'                  => $ang->optionprefix . 'confirmation_terms_and_conditions',
						'id'                    => null, // string
						'class'                 => "required", // string
						'show_option_none'      => null, // string
						'show_option_no_change' => "-",
						'option_none_value'     => null, // string
					);				
					wp_dropdown_pages( $args );
					?>
				</td>
			</tr>
			
			<tr valign="top">
				<td  colspan="2">
					<p class="description" id="tagline-description"> <?php _e('Companydetails for the template', 'wp-adrom-newsletter');?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Name', 'wp-adrom-newsletter');?></th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>signin_template_company_name" class="regular-text ltr" value="<?php echo $ang->get_option('signin_template_company_name'); ?>"/>
				</td>
			</tr>  
			
			<tr valign="top">
				<th scope="row"><?php _e('Street', 'wp-adrom-newsletter');?></th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>signin_template_company_street" class="regular-text ltr" value="<?php echo $ang->get_option('signin_template_company_street'); ?>"/>
				</td>
			</tr>  
			
			<tr valign="top">
				<th scope="row"><?php _e('PostalCode', 'wp-adrom-newsletter');?></th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>signin_template_company_postalcode" class="regular-text ltr" value="<?php echo $ang->get_option('signin_template_company_postalcode'); ?>"/>
				</td>
			</tr>  
			
			<tr valign="top">
				<th scope="row"><?php _e('City', 'wp-adrom-newsletter');?></th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>signin_template_company_city" class="regular-text ltr" value="<?php echo $ang->get_option('signin_template_company_city'); ?>"/>
				</td>
			</tr>  
			
			<tr valign="top">
				<th scope="row"><?php _e('Country', 'wp-adrom-newsletter');?></th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>signin_template_company_country" class="regular-text ltr" value="<?php echo $ang->get_option('signin_template_company_country'); ?>"/>
				</td>
			</tr>  
			
			<tr valign="top">
				<th scope="row"><?php _e('Phonenumber', 'wp-adrom-newsletter');?></th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>signin_template_company_phonenumber" class="regular-text ltr" value="<?php echo $ang->get_option('signin_template_company_phonenumber'); ?>"/>
				</td>
			</tr>  
						
			<tr valign="top">
				<th scope="row"><?php _e('EmailAddress', 'wp-adrom-newsletter');?></th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>signin_template_company_email" class="regular-text ltr" value="<?php echo $ang->get_option('signin_template_company_email'); ?>"/>
				</td>
			</tr> 
			
		</table>
    </div>
	
	<div id="confirmationPageSettings" class="tabbable">
		<h3 class="title"><?php _e('Confirmation-Page', 'wp-adrom-newsletter');?></h3>
			
		<p class="description" id="tagline-description">
			<?php _e('Page after open Confirmation-Link in Email', 'wp-adrom-newsletter');?>
		</p>
		
		<p class="description" id="tagline-description">
			<?php _e('Available placeholders', 'wp-adrom-newsletter');?>: <br>
			<kbd>{WEBSITE_NAME}</kbd><br>
			<kbd>{SIGNOUT_PAGE}</kbd>
		</p>
		
		<table class="form-table">

			<tr valign="top">
				<th scope="row"><?php _e('choose page', 'wp-adrom-newsletter');?> *</th>
				<td>				
					<?php 
					$args = array(
						'depth'                 => 0,
						'child_of'              => 0,
						'selected'              => $ang->get_option('signin_template_confirmation_url'),
						'echo'                  => 1,
						'name'                  => $ang->optionprefix . 'signin_template_confirmation_url',
						'id'                    => null, // string
						'class'                 => "required", // string
						'show_option_none'      => null, // string
						'show_option_no_change' => "-",
						'option_none_value'     => null, // string
					);				
					wp_dropdown_pages( $args );
					?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Custom url', 'wp-adrom-newsletter');?></th>
				<td>					
					<input type="text" name="<?php echo $ang->optionprefix; ?>confirmation_custom_url" class="regular-text ltr" value="<?php echo $ang->get_option('confirmation_custom_url'); ?>"/>
					<p class="description" id="tagline-description"><?php _e('define the url at which the confirmationpage should be accessible', 'wp-adrom-newsletter');?>: ("newsletter/confirm/")</p>
				</td>
			</tr> 
			
			<tr valign="top">
				<th scope="row"><?php _e('Confirmation', 'wp-adrom-newsletter');?> OK *</th>
				<td>
					<textarea name="<?php echo $ang->optionprefix; ?>confirmation_ok_message" class="large-text required" rows="3"><?php echo $ang->get_option('confirmation_ok_message'); ?></textarea>				
				</td>
			</tr>         
			
			<tr valign="top">
				<th scope="row"><?php _e('Confirmation', 'wp-adrom-newsletter');?> Error *</th>
				<td>
					<textarea name="<?php echo $ang->optionprefix; ?>confirmation_error_message" class="large-text required" rows="3"><?php echo $ang->get_option('confirmation_error_message'); ?></textarea>			
				</td>
			</tr>		
		
			<tr valign="top">
				<th scope="row"><?php _e('Confirmation', 'wp-adrom-newsletter');?> Error (<?php _e('already confirmed', 'wp-adrom-newsletter');?>)*</th>
				<td>
					<textarea name="<?php echo $ang->optionprefix; ?>confirmation_error_already_confirmed_message" class="large-text required" rows="3"><?php echo $ang->get_option('confirmation_error_already_confirmed_message'); ?></textarea>			
				</td>
			</tr>
		
			<tr valign="top">
				<th scope="row"><?php _e('Confirmation', 'wp-adrom-newsletter');?> Error (<?php _e('hash expired', 'wp-adrom-newsletter');?>)*</th>
				<td>
					<textarea name="<?php echo $ang->optionprefix; ?>confirmation_error_message_expired_hash" class="large-text required" rows="3"><?php echo $ang->get_option('confirmation_error_message_expired_hash'); ?></textarea>			
				</td>
			</tr>
		
		</table>
		
		
	</div>
    <?php submit_button(); ?>
	
</form>
</div>
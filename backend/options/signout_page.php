<div class="wrap">
<h2>WP adRom Newsletter: SignOut</h2>

<?php 
$tabs = array( 
	'formMessage' => __('Form-Messages',  'wp-adrom-newsletter'), 
	'settings' => __('Settings',  'wp-adrom-newsletter'),	
);
$ang = new adromNewsletterGlobal();
echo $ang->generate_backend_tabs($tabs);
?>

<form method="post" action="options.php" class="wp_adrom_newsletter_be_form">

    <?php settings_fields( 'wp-adroom-newsletter-options-signout' ); ?>
    <?php do_settings_sections( 'wp-adroom-newsletter-options-signout' ); ?>
	
	<!-- formMessage -->
	<div id="formMessage" class="tabbable">
	
		<h3 class="title"><?php _e('Form-Messages', 'wp-adrom-newsletter');?></h3>
		<p class="description" id="tagline-description">
			<?php 
				_e('Available placeholders', 'wp-adrom-newsletter');
				echo ":<br>";
				
				$placeholders = $ang->availablePlaceholders;				
				foreach($placeholders as $ph){					
					echo "<kbd>" . $ph . "</kbd><br>";
				}							
			?> 						
		</p>
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row">OK *</th>
				<td>
					<textarea name="<?php echo $ang->optionprefix; ?>signout_ok_message" class="large-text required" rows="3"><?php echo $ang->get_option('signout_ok_message'); ?></textarea>				
				</td>
			</tr>         
			
			<tr valign="top">
				<th scope="row">Error *</th>
				<td>
					<textarea name="<?php echo $ang->optionprefix; ?>signout_error_message" class="large-text required" rows="3"><?php echo $ang->get_option('signout_error_message'); ?></textarea>			
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row">Error (Hash-Link) *</th>
				<td>
					<textarea name="<?php echo $ang->optionprefix; ?>signout_error_wrong_link_message" class="large-text required" rows="3"><?php echo $ang->get_option('signout_error_wrong_link_message'); ?></textarea>							
					<p class="description" id="tagline-description"><?php _e('error message when wrong link with hash is detected', 'wp-adrom-newsletter');?></p>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row">Error ("failed automated signout" message) *</th>
				<td>
					<textarea name="<?php echo $ang->optionprefix; ?>signout_error_failed_automated_message" class="large-text required" rows="3"><?php echo $ang->get_option('signout_error_failed_automated_message'); ?></textarea>							
					<p class="description" id="tagline-description"><?php _e('error message when address could not be signed out automatic', 'wp-adrom-newsletter');?></p>
				</td>
			</tr>
			
		</table>
    </div>
	<!-- formMessage END -->
	
	<!-- SETTINGS-->
	<div id="settings" class="tabbable">
		<h3 class="title"><?php _e('Settings', 'wp-adrom-newsletter');?></h3>		
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Signout Page', 'wp-adrom-newsletter');?></th>
				<td>				
					<?php 
					$args = array(
						'depth'                 => 0,
						'child_of'              => 0,
						'selected'              => $ang->get_option('signout_signoutpage'),
						'echo'                  => 1,
						'name'                  => $ang->optionprefix . 'signout_signoutpage',
						'id'                    => null, // string
						'class'                 => null, // string
						'show_option_none'      => null, // string
						'show_option_no_change' => "-",
						'option_none_value'     => null, // string
					);				
					wp_dropdown_pages( $args );
					?>
					<p class="description" id="tagline-description"><?php _e('should be the same page where the signout-shortcode "[render_wp_adrom_newsletter_form_signout]" is used', 'wp-adrom-newsletter');?></p>
				</td>
			</tr>
			
			<tr valign="top">
					<th scope="row"><?php _e('EmailSystemClientId', 'wp-adrom-newsletter');?> *</th>
					<td>						
						<input type="text" name="<?php echo $ang->optionprefix; ?>signout_emailSystemClientId" class="regular-text ltr required" value="<?php echo $ang->get_option('signout_emailSystemClientId'); ?>"/>
					</td>
				</tr>
				
		</table>
		
		<div class="wp_adrom_newsletter_groupbox">
			<h3 class="title">Encryption</h3>
			<table class="form-table">
				
				<tr valign="top">
					<th scope="row"><?php _e('Passphrase', 'wp-adrom-newsletter');?> *</th>
					<td>						
						<input type="text" name="<?php echo $ang->optionprefix; ?>signout_password" class="regular-text ltr required" value="<?php echo $ang->get_option('signout_password'); ?>"/>
					</td>
				</tr>  
				
				<tr valign="top">
					<th scope="row"><?php _e('Salt', 'wp-adrom-newsletter');?> *</th>
					<td>						
						<input type="text" name="<?php echo $ang->optionprefix; ?>signout_salt" class="regular-text ltr required" value="<?php echo $ang->get_option('signout_salt'); ?>"/>
					</td>
				</tr>  
				
				<tr valign="top">
					<th scope="row"><?php _e('Iterations', 'wp-adrom-newsletter');?> *</th>
					<td>						
						<input type="text" name="<?php echo $ang->optionprefix; ?>signout_iterations" class="regular-text ltr required" value="<?php echo $ang->get_option('signout_iterations'); ?>"/>
					</td>
				</tr> 
				
				<tr valign="top">
					<th scope="row"><?php _e('Keysize', 'wp-adrom-newsletter');?> *</th>
					<td>						
						<input type="text" name="<?php echo $ang->optionprefix; ?>signout_keylength" class="regular-text ltr required" value="<?php echo $ang->get_option('signout_keylength'); ?>"/>
					</td>
				</tr> 
				
				<tr valign="top">
					<th scope="row"><?php _e('Initialization Vectors', 'wp-adrom-newsletter');?> *</th>
					<td>						
						<input type="text" name="<?php echo $ang->optionprefix; ?>signout_iv" class="regular-text ltr required" value="<?php echo $ang->get_option('signout_iv'); ?>"/>
					</td>
				</tr> 
			</table>
		</div>
	</div>
	<!--SETTINGS END-->
	
    <?php submit_button(); ?>

</form>
</div>
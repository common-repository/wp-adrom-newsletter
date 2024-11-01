<?php 
$ang = new adromNewsletterGlobal();
?>
<div class="wrap">
<h2>WP adRom Newsletter: General</h2>

<form method="post" action="options.php" class="wp_adrom_newsletter_be_form">
    <?php settings_fields( 'wp-adroom-newsletter-options-general' ); ?>
    <?php do_settings_sections( 'wp-adroom-newsletter-options-general' ); ?>
	
	<h3 class="title"><?php _e('Global settings', 'wp-adrom-newsletter');?></h3>
		
	<table class="form-table">
        
		<tr valign="top">
			<th scope="row">API Key *</th>
			<td>
				<input type="text" name="<?php echo $ang->optionprefix; ?>general_apikey" class="regular-text ltr required" value="<?php echo $ang->get_option("general_apikey"); ?>" />
			</td>
        </tr>

		<tr valign="top">
			<th scope="row">API Url *</th>
			<td>
				<input type="text" name="<?php echo $ang->optionprefix; ?>general_apiurl" class="regular-text ltr required" value="<?php echo $ang->get_option("general_apiurl"); ?>" />
			</td>
        </tr>
		
		<!-- TESTER-->
			<!--
			<tr valign="top">
				<th scope="row">Tester A</th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>general_tester_a" class="regular-text ltr" value="<?php echo $ang->get_option('general_tester_a'); ?>"/>
				</td>
			</tr> 
			
			<tr valign="top">
				<th scope="row">Tester B</th>
				<td>						
					<input type="text" name="<?php echo $ang->optionprefix; ?>general_tester_b" class="regular-text ltr" value="<?php echo $ang->get_option('general_tester_b'); ?>"/>
				</td>
			</tr>
			-->
			<!---TESTER END -->
		
    </table>
	
    <?php submit_button(); ?>
	
	<div class="wp_adrom_newsletter_groupbox">			
		<h3 class="title"><?php _e('Available shortcodes', 'wp-adrom-newsletter');?></h3>
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row">[render_wp_adrom_newsletter_form_signout]</th>
				<td>
					<!--Rendert das "Abmelden"-Formular im Frontend-->
					<?php _e('renders the signout form', 'wp-adrom-newsletter');?>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row">[render_wp_adrom_newsletter_form_signin]</th>
				<td>
					<!--Rendert das "Anmelden"-Formular im Frontend-->
					<?php _e('renders the signin form', 'wp-adrom-newsletter');?>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row">[render_wp_adrom_newsletter_form_validate_signin]</th>
				<td>
					<!--Rendert die "Anmeldung bestätigen"-Seite im Frontend-->
					<?php _e('renders the confirmation page', 'wp-adrom-newsletter');?>
				</td>
			</tr>
			
		</table>
	</div>
	
</form>
</div>
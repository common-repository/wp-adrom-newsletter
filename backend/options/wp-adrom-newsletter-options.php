<?php

class adromNewsletterOptions {

	private $sub_menu_items = array(
		//just rename the first item (which is the parent item) to make it look better
		array(
			'parent_slug' => 'wp-adrom-newsletter-options',
			'page_title' => 'General',
			'menu_title' => 'General',
			'capability' => 'manage_options',
			'menu_slug' => 'wp-adrom-newsletter-options',
			'callback' => 'wp_adrom_newsletter_options_settings',
		),
		
		//signin options
		array(			
			'parent_slug' => 'wp-adrom-newsletter-options',
			'page_title' => 'SignIn',
			'menu_title' => 'SignIn',
			'capability' => 'manage_options',
			'menu_slug' => 'wp-adrom-newsletter-options-sign-in',
			'callback' => 'wp_adrom_newsletter_options_settings_sign_in',
		),
		
		//signout options
		array(			
			'parent_slug' => 'wp-adrom-newsletter-options',
			'page_title' => 'SignOut',
			'menu_title' => 'SignOut',
			'capability' => 'manage_options',
			'menu_slug' => 'wp-adrom-newsletter-options-sign-out',
			'callback' => 'wp_adrom_newsletter_options_settings_sign_out',
		),
		
		//log
		array(			
			'parent_slug' => 'wp-adrom-newsletter-options',
			'page_title' => 'Log',
			'menu_title' => 'Log',
			'capability' => 'manage_options',
			'menu_slug' => 'wp-adrom-newsletter-log',
			'callback' => 'wp_adrom_newsletter_log',
		)
	);
	
	public function __construct() {
		if ( is_admin() ){				
			add_action("admin_menu", array($this, "add_plugin_options_page"));
			add_action( 'admin_init', array($this, 'register_plugin_options_page') );
		}
		
	}
	
	public function register_plugin_options_page() { // whitelist options
	
		//error_reporting( E_ALL );
		$settings = array(
			
			"wp-adroom-newsletter-options-general" => array(
				"wp_adrom_newsletter_general_apikey",
				"wp_adrom_newsletter_general_apiurl",
				
				//test (not in use)
				"wp_adrom_newsletter_general_tester_a",
				"wp_adrom_newsletter_general_tester_b",
			),			
			"wp-adroom-newsletter-options-signin" => array(
				"wp_adrom_newsletter_signin_ok_message",
				"wp_adrom_newsletter_signin_error_message",				
				
				//RENDER FORM
				"wp_adrom_newsletter_signin_render",
				"wp_adrom_newsletter_signin_render_gtc_required",
				//CONFIRMATION
				"wp_adrom_newsletter_confirmation_custom_url",
				"wp_adrom_newsletter_confirmation_ok_message",
				"wp_adrom_newsletter_confirmation_error_message",
				"wp_adrom_newsletter_confirmation_terms_and_conditions",
				"wp_adrom_newsletter_confirmation_error_already_confirmed_message",
				"wp_adrom_newsletter_confirmation_error_message_expired_hash",
				//TEMPLATE				
				"wp_adrom_newsletter_signin_template_confirmation_url",		
				"wp_adrom_newsletter_signin_template_logo_url",
				"wp_adrom_newsletter_signin_template_logo_alt",
				"wp_adrom_newsletter_signin_template_background_color",
				"wp_adrom_newsletter_signin_template_button_color",
				"wp_adrom_newsletter_signin_template_website_url",
				"wp_adrom_newsletter_signin_template_company_name",
				"wp_adrom_newsletter_signin_template_company_street",
				"wp_adrom_newsletter_signin_template_company_postalcode",
				"wp_adrom_newsletter_signin_template_company_city",
				"wp_adrom_newsletter_signin_template_company_country",
				"wp_adrom_newsletter_signin_template_company_phonenumber",
				"wp_adrom_newsletter_signin_template_company_email"
			),
			"wp-adroom-newsletter-options-signout" => array(
				"wp_adrom_newsletter_signout_ok_message",
				"wp_adrom_newsletter_signout_error_message",
				"wp_adrom_newsletter_signout_error_wrong_link_message",
				"wp_adrom_newsletter_signout_error_failed_automated_message",
				"wp_adrom_newsletter_signout_request_data",
				"wp_adrom_newsletter_signout_signoutpage",				
				"wp_adrom_newsletter_signout_emailSystemClientId",				
				"wp_adrom_newsletter_signout_password",
				"wp_adrom_newsletter_signout_salt",
				"wp_adrom_newsletter_signout_iterations",
				"wp_adrom_newsletter_signout_keylength",
				"wp_adrom_newsletter_signout_iv",
			)
		);
	
		foreach($settings as $settingsGroup => $settingsNames){		
			foreach($settingsNames as $settingsName){				
				register_setting( (string)$settingsGroup , (string)$settingsName );
			}			
		};
		
	}
	
	public function add_admin_menu_separator( $position ) {

		global $menu;
		$menu[ $position ] = array(
			0	=>	'',
			1	=>	'read',
			2	=>	'separator' . $position,
			3	=>	'',
			4	=>	'wp-menu-separator'
		);

	}
	
	public function add_plugin_options_page() {

		$this->add_admin_menu_separator(998);
		//add main page
		add_menu_page(
			"WP adRom Newsletter", 
			"WP adRom Newsletter", 
			"manage_options", 
			"wp-adrom-newsletter-options", 
			array($this, "wp_adrom_newsletter_options_settings"), 
			WP_ADROM_NEWSLETTER_PLUGIN_URL . "icon.jpg",//'dashicons-email-alt', 
			999
		);		
		//add sub pages		
		foreach( $this->sub_menu_items as $item ) {
			add_submenu_page( $item['parent_slug'], $item['page_title'], $item['menu_title'], $item['capability'], $item['menu_slug'], array($this, $item['callback']) );
		}
		
		$this->add_admin_menu_separator(1000);
		
	}
	
	public function wp_adrom_newsletter_options_settings() { 		
		include 'general_page.php';
	}
	
	public function wp_adrom_newsletter_options_settings_sign_in() { 
		include 'signin_page.php';		
	}
	
	public function wp_adrom_newsletter_options_settings_sign_out() { 
		include 'signout_page.php';		
	}
	
	public function wp_adrom_newsletter_log(){		
		include 'log_page.php';		
	}

}

?>
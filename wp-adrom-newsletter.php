<?php
/*
Plugin Name: WP adRom Newsletter
Description: Renders Signin/Signout forms via shortcodes and handles them
Plugin URI: 
Version: 1.1.35
Author: Benjamin Gök
Author URI: 
*/

error_reporting( E_ALL );

include 'backend/options/wp-adrom-newsletter-options.php';
include 'frontend/wp-adrom-newsletter-render-forms.php';
include 'backend/process-forms/wp-adrom-newsletter-process-forms.php';
include 'wp-adrom-newsletter-global.php';
include 'helper/OptoutManagement.php';

define("WP_ADROM_NEWSLETTER_PLUGIN_URL", plugin_dir_url( __FILE__ ));

class adromNewsletter {
        
	private static $instance;
	protected $renderFormsClass;
	protected $processFormsClass;
	protected $optionsClass;	
	private $confirmationArray;
	public $ang;
	
	public static function get_instance() {
		if( null == self::$instance ) {
			self::$instance = new adromNewsletter();
		} 
		return self::$instance;
	} 
	
	private function __construct() {
	
		$this->check_for_required_php_modules();
	
		$this->ang = new adromNewsletterGlobal();
	
		//check for updates
		add_action('admin_init', array($this,'check_for_updates'),10);		
		
		add_filter( 'query_vars', array($this,'addnew_query_vars'), 10, 1 );				
		add_action('init', array($this,'redirect_when'));		
		
		add_filter('the_title', array($this,'check_for_confirmation_page_title'),10,2);			
		add_filter('the_content', array($this,'check_for_confirmation_page_content'));
		
		//translation
		add_action( 'init', array($this,'wp_adrom_newsletter_load_textdomain') );
		
		//add scripts & styles (BE)
		add_action( 'admin_enqueue_scripts', array($this, 'wp_adrom_newsletter_enqueue_scripts_be') );		
		//add scripts & styles (FE)
		add_action( 'wp_enqueue_scripts', array($this, 'wp_adrom_newsletter_enqueue_scripts_fe') );
		//register ajax funcitons (the wordpress-way)
		$this->processFormsClass = new adromNewsletterProcessForms();
		//add shortcode functions
		$this->renderFormsClass = new adromNewsletterRenderForms();				
		//add options pages
		$this->optionsClass = new adromNewsletterOptions();
		
		add_action('admin_footer', array($this, 'wp_adrom_newsletter_admin_footer'));
	} 
	
	public function check_for_updates(){
	
		$currentPluginData = get_plugin_data(__FILE__ );			
		$currentPluginVersion = $currentPluginData['Version'];	
		$oldPluginVersion = $this->ang->get_option('plugin_version');	
		//$oldPluginVersion = "1.0"; //OVERWRITE FOR DEBUG
		$optionprefix = $this->ang->optionprefix;
		
		if(version_compare($oldPluginVersion, $currentPluginVersion) == -1){	//if old < new   =====> -1
			
			update_option($optionprefix.'plugin_version', $currentPluginVersion);
			
			//-----------------------------------------------------------------------------------------
			//REPLACE OLD OPTION NAME WITH NEW OPTION NAME (and insert the value of the old field into the new field) (Field-Old, Field-New)
			$replaceFields = array(
				//"general_tester_a" => $optionprefix.'general_tester_b'
			);
			if(!empty($replaceFields)){
			
				foreach($replaceFields as $oldFieldName => $newFieldName){					
					$optionValue = $this->ang->get_option($oldFieldName);									
					update_option($newFieldName, $optionValue);
					delete_option($optionprefix.$oldFieldName);					
				}				
			}

			//-----------------------------------------------------------------------------------------		
			//FILL UP NEW OPTIONS WITH NEW PREDEFINED VALUES (Field, Value)
			$newOptions = array(
				//'general_tester_a'
			);		
			if(!empty($newOptions)){
				foreach($newOptions as $optionName ){									
					if(isset($this->ang->defaultOptions[$optionprefix.$optionName])){
						$newValue = $this->ang->defaultOptions[$optionprefix.$optionName];
						update_option($optionprefix.$optionName, $newValue);
					} else {
						wp_die('<h1>WP adRom Newsletter</h1> <p>Could not update plugin-options <strong>'. $optionName .'</strong>), please report it at b.goek@adrom.net</p>', 'update error');
					}
				}
			}
			
			//-----------------------------------------------------------------------------------------
			//DELETE OLD OPTIONS
			$deleteOptions = array(
				//"general_tester_c"
			);
			if(!empty($deleteOptions)){			
				foreach($deleteOptions as $deleteOption ){						
					delete_option($optionprefix.$deleteOption);	
				}
			}
			
		}
		
		//-----------------------------------------------------------------------------------------------
		//FILL UP DEFAULT VALUES IF NOT EXISTS		
		$default_settings = $this->ang->defaultOptions;		
		foreach($default_settings as $key => $val){
			if( !get_option($key) ) {			
				//not present, so add        
				update_option($key, $val);
			}
		}
		
	}
	
	public function check_for_required_php_modules(){
		if(is_admin()){
			//check for required modules
			$requiredModules = array('mcrypt');
			$message = "";
			foreach($requiredModules as $requiredModule){
				if( extension_loaded($requiredModule) === false ){					
					//only show error on own plugin page
					if(isset($_GET['page']) &&  strpos($_GET['page'],'wp-adrom-newsletter-options') !== false){										
						$message .= "<li><strong>" . $requiredModule . "</strong></li>";					
					}					
				}
			}					
			if(strlen($message)>0){
				$completeMessage = "<h1>WP adRom Newsletter</h1>" . "<p>Missing php-module</p>";								
				$completeMessage .= '<ul>' . $message . '</ul>';				
				wp_die( $completeMessage , "missing php-module");
			}			
		}
	}
	
	public function check_for_activation_hash(){
		global $wp_query;				
		$confirmHash = isset($wp_query->query_vars['hash']) ? $wp_query->query_vars['hash'] : '';//isset($_GET['hash']) ? $_GET['hash'] : '';				
		$_POST['ConfirmHash'] = $confirmHash;
		$anpf = new adromNewsletterProcessForms();
		$confirmRequest = $anpf->process_wp_adrom_newsletter_validate_sign_in();				
		$validationClass = "";
		$validationMessage = $confirmRequest['message'];
		
		if($confirmRequest['type'] == 'error'){
			$this->confirmationArray['title'] = __('Confirmation failed', 'wp-adrom-newsletter');					
			$validationClass = " alert alert-danger";
		} else {
			$this->confirmationArray['title'] = __('Confirmation succeeded', 'wp-adrom-newsletter');					
			$validationClass = "neutral";
		}				
		$this->confirmationArray['content'] = '<div id="validate_sign_in" class="wp-adrom-newsletter-form"> <div class="status '. $validationClass .'">'. $validationMessage .'</div> </div>';		
	}
	
	public function check_for_confirmation_page_title($title, $id){				
		
		global $post;
		$post_object = $post;			
		$confirmPageId = $this->ang->get_option('signin_template_confirmation_url');							
		if($confirmPageId != false && $confirmPageId == $id){							
			if($post_object->ID == $confirmPageId){			
				//to be sure, we just send the request ONCE (independent how often "the_title" is called)
				if($this->confirmationArray == null){
					//we are on the confirmation page, call the request the first time
					$this->check_for_activation_hash();
					$title = $this->confirmationArray['title'];	
				} else {
					//again the same page but the_title appears somewhere else, so no need to do the request again
					if(isset($this->confirmationArray['title'])){
						$title = $this->confirmationArray['title'];
					}
				}			
			}			
		}
		return $title;
	}
	
	public function check_for_confirmation_page_content($content){				
		$confirmPageId = $this->ang->get_option('signin_template_confirmation_url');			
		global $post;		
		if($post->ID == $confirmPageId){		
			//we are on confirmation page
			if(isset($this->confirmationArray['content'])){
				$content = $this->confirmationArray['content'];
			} else {
				//there was not "the_content" called, so lets check again for activation hash
				$this->check_for_activation_hash();
				$content = $this->confirmationArray['content'];
			}
		}
		return $content;
	}
	
	
	public function addnew_query_vars($vars)
	{   	
		// "hash" is the name of variable we want to add to wordpress's knowledge
		$vars[] = 'hash'; 
		$vars[] = 'u'; 
		return $vars;
	}
	
	public function redirect_when(){
	
		//redirect "htt://www.domain.com/unsubscribe/1234" -> to defined signout page with the hash
		$stringToSearch = '/unsubscribe/';
		$uri = $_SERVER["REQUEST_URI"];
		
		if (strpos($uri,$stringToSearch) !== false) {		
			$hash = null;
			$explodes = explode($stringToSearch, $uri);
			if(count($explodes) > 1 && isset($explodes[1]) && $explodes[1] != ""){
				$hash = $explodes[1];
			}
			if($hash != null || $hash != "" || strlen($hash) != 0){				
				$signoutPageId = esc_attr( get_option('wp_adrom_newsletter_signout_signoutpage') );						
				if($signoutPageId != 0 || $signoutPageId != null){				
					$signoutPage = get_page_link($signoutPageId);
					$useURL = $signoutPage . "?u=" .$hash;			
					wp_redirect( $useURL, 302 );
					exit();					
				}
			}
		}
		
		//---------------------------------------------------------------------
		//UNSUBSCRIBE URL
		//$signOutPageId = $this->ang->get_option("signout_signoutpage");		
		//add_rewrite_rule('^unsubscribe/(.)/?', 'index.php?page_id='. $signOutPageId .'&u=$matches[1]', 'top');
		
		//---------------------------------------------------------------------
		//CONFIRMATION URL
		/*
			http://www.domain.de/anmeldung-bestätigen/123456
			interpret to
			http://www.domain.de/anmeldung-bestätigen/?hash=123456
		*/
		
		$confirmationPageId = $this->ang->get_option("signin_template_confirmation_url");		
		if($confirmationPageId == null || $confirmationPageId == ""){ return; }		
		$confirmationPage = get_post($confirmationPageId);
		$confirmationPageUrl = $this->ang->get_option("confirmation_custom_url");
		if($confirmationPageUrl == ""){
			$confirmationPageUrl = $confirmationPage->post_name . '/';
		}
		add_rewrite_rule('^'. $confirmationPageUrl .'([0-9a-z]+)/?', 'index.php?page_id='. $confirmationPageId .'&hash=$matches[1]', 'top');
		
		//flush_rewrite_rules();
		
	}
	
	public function wp_adrom_newsletter_load_textdomain(){			
		//TO FAKE US LANGUAGE
		//add_filter('locale', array(&$this, 'fake_locale'));	
		$domain = 'wp-adrom-newsletter';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );		
		load_plugin_textdomain( $domain, FALSE, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );			
	}
	
	public function fake_locale($locale) { return "en_US"; }
		
	public function wp_adrom_newsletter_enqueue_scripts_be() {
	
		//BACKEND				
		//wp colorpicker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'wp-adrom-newsletter_backend_css', plugins_url() . '/wp-adrom-newsletter/assets/css/wp-adrom-newsletter_backend.css' );
		//toastr
		wp_enqueue_style( 'wp-adrom-newsletter_backend_toastr_css', plugins_url() . '/wp-adrom-newsletter/assets/toastr/toastr.min.css' );
		
		//load plugin backend
		wp_enqueue_script('wp-adrom-newsletter_backend_js', plugins_url() . '/wp-adrom-newsletter/assets/js/wp-adrom-newsletter_backend.js', array( 'jquery','wp-color-picker') );			
		//toastr
		wp_enqueue_script('wp-adrom-newsletter_backend_toastr_js', plugins_url() . '/wp-adrom-newsletter/assets/toastr/toastr.min.js', array( 'jquery' ) );
		
	}
	
	
	public function wp_adrom_newsletter_enqueue_scripts_fe() {
		//load from wordpress
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-datepicker');					
		//wp_enqueue_style( 'jquery-ui-css', plugins_url() . '/wp-adrom-newsletter/assets/jquery-ui/jquery-ui.min.css' );
		
		//load plugin frontend
		wp_enqueue_script('wp-adrom-newsletter_js', plugins_url() . '/wp-adrom-newsletter/assets/js/wp-adrom-newsletter.js', array( 'jquery' ) );		
		wp_enqueue_style( 'wp-adrom-newsletter_css', plugins_url() . '/wp-adrom-newsletter/assets/css/wp-adrom-newsletter.css' );
	}

	public function wp_adrom_newsletter_admin_footer(){
		echo "<span class='hidden wp_adrom_newsletter_input_is_required_text'>". __('is required','wp-adrom-newsletter') ."</span>";		
	}
} 

add_action( 'plugins_loaded', array( 'adromNewsletter', 'get_instance' ) );

function wp_adrom_newsletter_activated() {

}
//call when plugin is activated
register_activation_hook( __FILE__, 'wp_adrom_newsletter_activated' );

?>
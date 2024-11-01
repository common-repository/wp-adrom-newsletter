<?php

//Models
$modelPath = realpath(__DIR__ . '/../..');
include $modelPath . '/models/Person.php';
include $modelPath . '/models/TemplateDetail.php';
include $modelPath . '/models/Impressum.php';
include $modelPath . '/models/Lead.php';

class adromNewsletterProcessForms {

	public $ang;
	
	public function __construct() {	
	
		$this->ang = new adromNewsletterGlobal();
	
		//register ajax funcitons the wordpress-way
		
		//signout
		add_action( 'wp_ajax_process_wp_adrom_newsletter_form_sign_out', array($this, 'process_wp_adrom_newsletter_form_sign_out') );
		add_action( 'wp_ajax_nopriv_process_wp_adrom_newsletter_form_sign_out', array($this, 'process_wp_adrom_newsletter_form_sign_out') );
		
		//signout with hash
		add_action( 'wp_ajax_process_wp_adrom_newsletter_form_sign_out_with_hash', array($this, 'process_wp_adrom_newsletter_form_sign_out_with_hash') );
		add_action( 'wp_ajax_nopriv_process_wp_adrom_newsletter_form_sign_out_with_hash', array($this, 'process_wp_adrom_newsletter_form_sign_out_with_hash') );
	
		//signin
		add_action( 'wp_ajax_process_wp_adrom_newsletter_form_sign_in', array($this, 'process_wp_adrom_newsletter_form_sign_in') );
		add_action( 'wp_ajax_nopriv_process_wp_adrom_newsletter_form_sign_in', array($this, 'process_wp_adrom_newsletter_form_sign_in') );
		
		//validate signin
		add_action( 'wp_ajax_process_wp_adrom_newsletter_validate_sign_in', array($this, 'process_wp_adrom_newsletter_validate_sign_in') );
		add_action( 'wp_ajax_nopriv_process_wp_adrom_newsletter_validate_sign_in', array($this, 'process_wp_adrom_newsletter_validate_sign_in') );
		
	}
		
	public function unset_unwanted_post_data($post){
		$unwanted_keys = array(
			"action",
			"_wpnonce",
			"_wp_http_referer",
			"username",
		);		
		foreach($unwanted_keys as $unwanted_key){
			if(isset($post[$unwanted_key])){
				unset($post[$unwanted_key]);
			}
		}		
		return $post;
	}
	
	public function sanatize_post($array){		
		$sanitize = array();		
		foreach($array as $key => $val){			
			if(!is_array($val)){				
				$sanitize[$key] = sanitize_text_field($val);
			} else {				
				foreach($val as $k => $v){
					$sanitize[$key][$k] = sanitize_text_field($v);
				}
			}
		}
		return $sanitize;
	}
		
	public function send_curl($params){
		
		$curl = curl_init();
		//curl_setopt($curl,CURLOPT_URL, "https://localhost:44302/subscription/subscribe/");
		curl_setopt($curl,CURLOPT_URL, $params['url']);
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($curl,CURLOPT_TIMEOUT,30);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_POST,true);
		curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($params['data'])); 
		
		if (strpos($_SERVER['HTTP_HOST'],'localhost') !== false) {
		//if localhost, disable "ssl verify"
			curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0); 			
		}
		
		//$status = array( 'type'=>'success', 'message'=> wpautop($params['ok_message']));
		
		$response = curl_exec($curl);
		$json = json_decode($response);
		$curlstatus = curl_getinfo($curl);								
		curl_close($curl);
		
		$result = array(
			'json' => $json,
			'curlstatus' => $curlstatus
		);		
		
		return $result;		
	}
	
	public function process_wp_adrom_newsletter_form_sign_out_with_hash(){
			
		// check if nonce is set
		$nonce = $_REQUEST['_wpnonce'];
		if ( !wp_verify_nonce( $nonce, 'wp_adrom_newsletter_signout_nonce' ) ) {
			echo json_encode(array( 'type'=>'error', 'message'=> wpautop("Incorrect nonce")));
			die();
		}
		
		$email = $_POST['wp_adrom_newsletter_signout_email'];
		$uhash = isset($_POST['uhash']) ? $_POST['uhash'] : '';
		
		$ok_message = wpautop($this->ang->get_option('signout_ok_message',true));		
		$error_message = wpautop($this->ang->get_option('signout_error_message',true));
		$error_failed_automated = wpautop($this->ang->get_option('signout_error_failed_automated_message', true));
		//replace placeholders
		$ok_message = str_replace(array("{EMAIL_ADDRESS}"), array("<b>" . $email . "</b>"), $ok_message);		
		$error_message = str_replace(array("{EMAIL_ADDRESS}"), array("<b>" . $email . "</b>"), $error_message);		
		$error_failed_automated = str_replace(array("{EMAIL_ADDRESS}"), array("<b>" . $email . "</b>"), $error_failed_automated);		
		$status = array( 'type'=>'success', 'message'=> wpautop($ok_message));		
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $uhash == "" )
		{			
			$status['type'] = 'error';
			$status['message'] = $error_message;
		}
		else
		{						
			$customDecryptionParams = $this->ang->optoutManagmentInitSettings;				
			$emailSystemClientId = $this->ang->get_option("signout_emailSystemClientId");
			$apikey = $this->ang->get_option("general_apikey");
			$apiurl = $this->ang->get_option("general_apiurl");		
			
			$om = new OptoutManagement(3,$emailSystemClientId, "", $apiurl, $apikey, $customDecryptionParams);
			
			if($om->init($uhash)){
				$result = $om->submitOptout();											
				if($result === true)
				{
					$status['type'] = 'success';				
					$status['message'] = $ok_message;
				}				
				else if($result === null)
				{
					$status['type'] = 'error';				
					$status['message'] = $error_failed_automated;
				}
				else if($result === false)
				{
					$status['type'] = 'error';				
					$status['message'] = $error_message;
				}
			} else {
				$status['type'] = 'error';				
				$status['message'] = $error_message;
			}
			
		}
			
		echo json_encode($status);
		die();
	}
	
	public function process_wp_adrom_newsletter_form_sign_out(){
		
		$email_from = $_POST['wp_adrom_newsletter_signout_email'];	
		$ok_message = wpautop($this->ang->get_option('signout_ok_message',true));		
		$error_message = wpautop($this->ang->get_option('signout_error_message',true));
		$error_failed_automated = wpautop($this->ang->get_option('signout_error_failed_automated_message', true));
		$client = $this->ang->get_option('general_client');
		$apiKey = $this->ang->get_option('general_apikey');
		
		if($ok_message == "") {
			$ok_message = 'Vielen Dank, die Abmeldung Ihrer Emailadresse wird verarbeitet.';
		}
				
		if($error_message == "") {
			$error_message = 'Ein Fehler ist aufgetreten, die Emailadresse konnte nicht abgemeldet werden, bitte versuchen Sie es später noch einmal.';
		}
		
		//replace placeholders
		$ok_message = str_replace(array("{EMAIL_ADDRESS}"), array("<b>" . $email_from . "</b>"), $ok_message);		
		$error_message = str_replace(array("{EMAIL_ADDRESS}"), array("<b>" . $email_from . "</b>"), $error_message);		
		$error_failed_automated = str_replace(array("{EMAIL_ADDRESS}"), array("<b>" . $email_from . "</b>"), $error_failed_automated);		
		$status = array( 'type'=>'success', 'message'=> wpautop($ok_message));				
		
		// check if nonce is set
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'wp_adrom_newsletter_signout_nonce' ) ) {
			echo json_encode(array( 'type'=>'error', 'message'=> wpautop("Incorrect nonce")));
			die();
		}
		
		$customDecryptionParams = $this->ang->optoutManagmentInitSettings;				
		$emailSystemClientId = $this->ang->get_option("signout_emailSystemClientId");
		$apikey = $this->ang->get_option("general_apikey");
		$apiurl = $this->ang->get_option("general_apiurl");				
		$om = new OptoutManagement(3,$emailSystemClientId, "", $apiurl, $apikey, $customDecryptionParams);
		$om->init($email_from);
		$result = $om->submitOptout();											
		
		if($result === true)
		{
			$status['type'] = 'success';				
			$status['message'] = $ok_message;
		}				
		else if($result === null)
		{
			$status['type'] = 'error';				
			$status['message'] = $error_failed_automated;
		}
		else if($result === false)
		{
			$status['type'] = 'error';				
			$status['message'] = $error_message;
		}
		
		echo json_encode($status);
		
		die();
	}
	
	public function process_wp_adrom_newsletter_form_sign_in(){	
	
		//sanatize post				
		$post = $this->sanatize_post($_POST);				
		$post['UserAgent'] = $_SERVER['HTTP_USER_AGENT'];
		
		// check if nonce is set
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'wp_adrom_newsletter_signin_nonce' ) ) {
			echo json_encode(array( 'type'=>'error', 'message'=> "Incorrect nonce"));
			die();
		}
		
		$apiKey = $this->ang->get_option('general_apikey');
		$apiUrl = $this->ang->get_option('general_apiurl');
		$client = $this->ang->get_option('general_client');				
		$ok_message = $this->ang->get_option('signin_ok_message',true);		
		$error_message = $this->ang->get_option('signin_error_message',true);		
		
		//replace placeholders
		$ok_message = str_replace(array("{EMAIL_ADDRESS}"), array("<b>" . $post['EmailAddress'] . "</b>"), $ok_message);		
		$error_message = str_replace(array("{EMAIL_ADDRESS}"), array("<b>" . $post['EmailAddress'] . "</b>"), $error_message);
		
		$confirmationpageId = $this->ang->get_option('signin_template_confirmation_page');
		$confirmationPageUrl = $this->ang->get_option("confirmation_custom_url");
		if($confirmationPageUrl == ""){
			$confirmationPageUrl = get_page_link($confirmationpageId);
		} else {
			$confirmationPageUrl = get_bloginfo('url') . '/' . $confirmationPageUrl;
		}
		
		$status = array( 'type'=>'success', 'message'=> wpautop($ok_message));
				
		$website_url = $this->ang->get_option('signin_template_website_url');
		if (strpos($_SERVER['HTTP_HOST'],'localhost') !== false) {
		//if localhost, fake websitedomain "sparenxxl.com"
			$website_url = "http://sparenxxl.com";
		}
		
		$post = $this->unset_unwanted_post_data($post);		
		$Impressum = new Impressum(
			$this->ang->get_option('signin_template_company_name'),
			$this->ang->get_option('signin_template_company_street'),
			$this->ang->get_option('signin_template_company_postalcode'),
			$this->ang->get_option('signin_template_company_city'),
			$this->ang->get_option('signin_template_company_country'),
			$this->ang->get_option('signin_template_company_phonenumber'),
			$this->ang->get_option('signin_template_company_email')			
		);
		
		$TemplateDetail = new TemplateDetail(
			$confirmationPageUrl,
			$website_url,
			$this->ang->get_option('signin_template_logo_url'),
			$this->ang->get_option('signin_template_logo_alt'),
			$this->ang->get_option('signin_template_background_color'),
			$this->ang->get_option('signin_template_button_color')
		);
		
		
		if(isset($post['Person']['Birthday']) && $post['Person']['Birthday'] != ""){			
			$bday = strtotime($post['Person']['Birthday']);			
			$post['Person']['Birthday'] = date("m.d.y",$bday);
		}
		
		$Person = new Person(
			$post['Person']['Gender'],					
			isset($post['Person']['Firstname']) ? $post['Person']['Firstname'] : "",
			isset($post['Person']['Surname']) ? $post['Person']['Surname'] : "",
			isset($post['Person']['Birthday']) ? $post['Person']['Birthday'] : "",
			isset($post['Person']['Street']) ? $post['Person']['Street'] : "",
			isset($post['Person']['Streetnumber']) ? $post['Person']['Streetnumber'] : "",
			isset($post['Person']['PostalCode']) ? $post['Person']['PostalCode'] : "",
			isset($post['Person']['City']) ? $post['Person']['City'] : "",
			isset($post['Person']['Country']) ? $post['Person']['Country'] : ""
		);		
		
		$Lead = new Lead(
			$apiKey,
			$post['EmailAddress'],
			$post['GTC'],
			$post['IpAddress'],
			$post['UserAgent'],
			$Person,
			$TemplateDetail,
			$Impressum
		);
		
		$curl_setting = array(		
			"url" =>  $apiUrl . "/subscribe/",
			"data" =>  $Lead,
		);
				
		$status = array( 'type'=>'success', 'message'=> wpautop($ok_message));		
		$result = $this->send_curl($curl_setting);		
		
		if($result['curlstatus']['http_code'] == 200)
		{
			if($result['json']->requestStatus != "Successful"){					
				$status['type'] = 'error';
				$status['message'] = "<p>" . $error_message . "</p>";
			}
		}
		else 
		{
			$status['type'] = 'error';
			$status['message'] = "<p>" . $error_message . "</p>";
		}
		
		echo json_encode($status);		
		die();		
	}
	
	public function process_wp_adrom_newsletter_validate_sign_in(){
	
		$post = $_POST;		
		$post['ApiKey'] = $this->ang->get_option('general_apikey');
		$post['IpAddress'] = $_SERVER['REMOTE_ADDR'];
		$post['UserAgent'] = $_SERVER['HTTP_USER_AGENT'];
		$post = $this->unset_unwanted_post_data($post);		
			
		$apiUrl = $this->ang->get_option('general_apiurl');			
		$ok_message = wpautop($this->ang->get_option('confirmation_ok_message',true));		
		$error_message = wpautop($this->ang->get_option('confirmation_error_message',true));		
		$error_message_already_confirmed = wpautop($this->ang->get_option('confirmation_error_already_confirmed_message',true));
		$error_message_expired_hash = wpautop($this->ang->get_option('confirmation_error_message_expired_hash',true));
		
		//replace placeholders
		$blogname = get_bloginfo("name");
		
		$signoutPageId = $this->ang->get_option('signout_signoutpage',true);
		$signoutPageLink = get_page_link($signoutPageId);
		//die($signoutPageLink);
		$ok_message = str_replace(array("{WEBSITE_NAME}", "{SIGNOUT_PAGE}"), array($blogname, $signoutPageLink), $ok_message);				
				
		$status = array( 'type'=>'success', 'message'=> $ok_message);		
		$is_valide = true;		
		
		$curl_setting = array(
			"url" => $apiUrl . "/subscribeconfirm/",
			"data" => $post,
		);
		
		if($post['ConfirmHash'] == ""){			
			return array( 'type'=>'error', 'message'=> wpautop(__("Hash is missing.", 'wp-adrom-newsletter')));
			die();
		} else {			
			$result = $this->send_curl($curl_setting);			
			
			if($result['curlstatus']['http_code'] == 200)
			{
				//ConfirmHashInvalid
				if($result['json']->requestStatus != "Successful" || $result['json']->data->confirmResult == "ConfirmHashInvalid"){					
					$status['type'] = 'error';
					$status['message'] = $error_message;
				}				
				//AlreadyConfirmed
				if($result['json']->data->confirmResult == "AlreadyConfirmed"){
					$status['type'] = 'error';
					$status['message'] = $error_message_already_confirmed;					
				}					
				//HashExpired
				if($result['json']->data->confirmResult == "HashExpired"){
					$status['type'] = 'error';
					$status['message'] = $error_message_expired_hash;					
				}	
			}
			else 
			{
				$status['type'] = 'error';
				$status['message'] = $error_message;
			}
			
			return $status;
			
			die();
		}
		
	}

}

?>
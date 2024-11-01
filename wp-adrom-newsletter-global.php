<?php

class adromNewsletterGlobal {

	public $optionprefix = "wp_adrom_newsletter_";
	public $renderSignInFields = "";
	public $defaultOptions = array();
	public $optoutManagmentInitSettings = array();
	public $availablePlaceholders = array(
		"{EMAIL_ADDRESS}",
	);
	
	public function __construct() {
		$this->set_default_values();
		$this->setDefaultOptions();
		$this->set_optout_management_init_settings();		
	}
	
	public function set_optout_management_init_settings(){
		
		$emailSystemClientId = esc_attr( get_option('wp_adrom_newsletter_signout_emailSystemClientId') );
		$password = esc_attr( get_option('wp_adrom_newsletter_signout_password') );
		$salt = esc_attr( get_option('wp_adrom_newsletter_signout_salt') );
		$iterations = esc_attr( get_option('wp_adrom_newsletter_signout_iterations') );
		$keylength = esc_attr( get_option('wp_adrom_newsletter_signout_keylength') );
		$iv = esc_attr( get_option('wp_adrom_newsletter_signout_iv') );
		
		$this->optoutManagmentInitSettings = array(
			"password" => $password,
			"salt" => $salt,
			"iterations" => $iterations,
			"keylength" => $keylength,
			"iv" => $iv,
		);
		
	}
	
	public function setDefaultOptions(){
	
		$this->defaultOptions = array(
		//SAVE PLUGIN VERSION INTO DB (needed for update scripts)		
		$this->optionprefix . "plugin_version" => "",//$currentPluginVersion,
		//ANMELDUNG
		$this->optionprefix . "signin_ok_message" => '<h3>Fast geschafft...</h3>
<div style="display: block;">
<p>Sie haben soeben eine E-Mail an <strong>{EMAIL_ADDRESS}</strong> von uns erhalten.<br />So geht es weiter:
<ol style="list-style-type: decimal; font-size:inherit; margin-left: 0; list-style-position: inside;">
<li style="margin-bottom:2px;">Die Bestätigungs-E-Mail öffnen</li>
<li style="margin-bottom:2px;">Den Bestätigungs-Link klicken</li>
<li style="margin-bottom:2px;">Sie haben den Newsletter aktiviert</li>
</ol>
<p>Wenn sie kein Bestätigungs-E-Mail in ihrem Postfach finden, prüfen sie bitte den Ordner "Unerwünschte Werbung" oder "Spam"</p>
</div>',
		$this->optionprefix . "signin_error_message" => "Ein Fehler ist aufgetreten, bitte versuchen Sie es später noch einmal.",
		//CONFIRMATION
		$this->optionprefix . "confirmation_ok_message" => 'Wir freuen uns, Sie demnächst über Aktionen und Angebote von {WEBSITE_NAME} per E-Mail informieren zu dürfen.

Diese Einwilligung kann jederzeit auf <a href="{SIGNOUT_PAGE}">{SIGNOUT_PAGE}</a> oder am Ende jeder E-Mail widerrufen werden.',
		$this->optionprefix . "confirmation_error_message" => "Fehler bei der Newsletter-Anmeldung.

Es ist leider ein Fehler bei der Anmeldung zum Newsletter aufgetreten.
Bitte versuchen Sie es erneut und überprüfen Ihren Bestätigungs-Link.",
		$this->optionprefix . "confirmation_error_already_confirmed_message" => "Diese Emailadresse wurde bereits bestätigt.",
		$this->optionprefix . "confirmation_error_message_expired_hash" => "Dieser Hash ist bereits abgelaufen.",
		//ABMELDUNG
		$this->optionprefix . "signout_ok_message" => "Vielen Dank, die Abmeldung Ihrer Emailadresse {EMAIL_ADDRESS} wird verarbeitet.",
		$this->optionprefix . "signout_error_message" => "Ein Fehler ist aufgetreten, die Emailadresse {EMAIL_ADDRESS} konnte nicht abgemeldet werden, bitte versuchen Sie es später noch einmal.",		
		$this->optionprefix . "signout_error_wrong_link_message" => "Es ist ein Problem mit dem Link aufgetreten, dies kann durch ein versehentliches nur teilweises kopieren verursacht werden.
Bitte kopieren Sie den vollständigen Link in Ihren Webbrowser oder klicken Sie ihn direkt im Newsletter an.",
		$this->optionprefix . "signout_error_failed_automated_message" => '<h4>Leider konnte die Abmeldung nicht automatisch durchgeführt werden.</h4>

Um Ihre Abmeldung dennoch schnellstmöglich durchzuführen, bitten wir Sie auf die erhaltene Email unter Verwendung des Betreffs "abmelden" zu Antworten. Ihre Anfrage wird dann von unserem Support entgegen genommen und von einem unserer Mitarbeiter persönlich bearbeitet.

Mögliche Ursachen:
•	Sie haben eine Weiterleitung auf eine andere E-Mail-Adresse eingerichtet
•	Sie haben die falsche E-Mail-Adresse angegeben
•	Sie haben einen Tippfehler in Ihrer E-Mail-Adresse

Ihr Anliegen ist uns wichtig
Unser Ziel ist es, Ihr Anliegen schnellstmöglich und zu Ihrer vollen Zufriedenheit zu bearbeiten.

Für mögliche Rückfragen stehen wir Ihnen gerne zur Verfügung.

Email: <strong>office@entertainment.at</strong>',
	);
	}
	
	public function set_default_values(){
		
		$this->renderSignInFields = array(
			"Gender" => array(
				"name" => __('Gender', 'wp-adrom-newsletter'),								
				"type" => "text",
				"className" => "",
				"required" => false,
				"regexpattern" => "",
				"placeholder" => "",
				"isOnlySetting" => false
			),			
			"Firstname" => array(
				"name" => __('Firstname', 'wp-adrom-newsletter'),
				"type" => "text",
				"className" => "",
				"required" => false,
				"regexpattern" => "",
				"placeholder" => "",
				"isOnlySetting" => false
			),
			"Surname" => array(
				"name" => __('Surname', 'wp-adrom-newsletter'),
				"type" => "text",
				"className" => "",
				"required" => false,
				"regexpattern" => "",
				"placeholder" => "",
				"isOnlySetting" => false
			),			
			"EmailAddress" => array(
				"name" => __('EmailAddress', 'wp-adrom-newsletter'),
				"type" => "text",
				"className" => "",
				"required" => true,
				"regexpattern" => "[a-z0-9A-Z._%+-]+@[a-z0-9A-Z.-]+\.[a-zA-Z]{2,4}$",
				"placeholder" => "",
				"isOnlySetting" => false
			),
			"Street" => array(
				"name" => __('Street', 'wp-adrom-newsletter'),
				"type" => "text",
				"className" => "",
				"required" => false,
				"regexpattern" => "",
				"placeholder" => "",
				"isOnlySetting" => false
			),
			"Streetnumber" => array(
				"name" => __('Streetnumber', 'wp-adrom-newsletter'),
				"type" => "text",
				"className" => "",
				"required" => false,
				"regexpattern" => "",
				"placeholder" => "",
				"isOnlySetting" => false
			),
			"City" => array(
				"name" => __('City', 'wp-adrom-newsletter'),
				"type" => "text",
				"className" => "",
				"required" => false,
				"regexpattern" => "",
				"placeholder" => "",
				"isOnlySetting" => false
			),
			"PostalCode" => array(
				"name" => __('PostalCode', 'wp-adrom-newsletter'),
				"type" => "text",
				"className" => "",
				"required" => false,
				"regexpattern" => "", 
				"placeholder" => "",
				"isOnlySetting" => false
			),
			"State" => array(
				"name" => __('State', 'wp-adrom-newsletter'),
				"type" => "text",
				"className" => "",
				"required" => false,
				"regexpattern" => "",
				"placeholder" => "",
				"isOnlySetting" => false
			),
			"Country" => array(
				"name" => __('Country', 'wp-adrom-newsletter'),
				"type" => "text",
				"className" => "",
				"required" => false,
				"regexpattern" => "",
				"placeholder" => "",
				"isOnlySetting" => false
			),
			"Birthday" => array(
				"name" => __('Birthday', 'wp-adrom-newsletter'),
				"type" => "text",
				"className" => "jqueryDatepicker",
				"required" => false,
				"regexpattern" => "",
				"placeholder" => "",
				"isOnlySetting" => false
			),					
		);
			
	}
	
	public function generate_backend_tabs($tabs){
		$currentTab = isset($_GET['tab']) ? $_GET['tab'] : key($tabs);
		$str = '';
		$str .= '<div id="icon-themes" class="icon32"><br></div>';
		$str .= '<h2 class="nav-tab-wrapper">';
		foreach( $tabs as $tab => $name ){	
			$class = ( $tab == $currentTab ) ? ' nav-tab-active' : '';
			//$str .= "<a class='wp_adrom_newsletter_tab nav-tab$class' href='?page=". $_GET['page'] ."&tab=$tab' data-tabname='$tab'>$name</a>";
			$str .= "<span class='wp_adrom_newsletter_tab nav-tab$class' data-tabname='$tab'>$name</span>";
		}
		$str .= '</h2>';
		return $str;		
	}
	
	public function get_option($optionfield, $getRaw = false){
					
		if($getRaw){
			return get_option($this->optionprefix . $optionfield);
		} else {
			return esc_attr( get_option($this->optionprefix . $optionfield) );
		}
			
	}
	
}


?>
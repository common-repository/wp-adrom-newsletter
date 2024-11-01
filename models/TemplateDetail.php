<?php
class TemplateDetail
{
	public $ConfirmationUrl;
	public $WebsiteUrl;
	public $LogoUrl;
	public $LogoAlternativText;
	public $BackgroundColor;
	public $ButtonColor;
	
	function __construct($ConfirmationUrl = "", $WebsiteUrl = "", $LogoUrl = "", $LogoAlternativText = "", $BackgroundColor = "", $ButtonColor){
		$this->ConfirmationUrl = (string)$ConfirmationUrl;
		$this->WebsiteUrl = (string)$WebsiteUrl;
		$this->LogoUrl = (string)$LogoUrl;
		$this->LogoAlternativText = (string)$LogoAlternativText;
		$this->BackgroundColor = (string)$BackgroundColor;
		$this->ButtonColor = (string)$ButtonColor;	
	}	
}
?>
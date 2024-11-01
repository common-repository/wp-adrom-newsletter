<?php
class Lead
{
	public $ApiKey;
	public $EmailAddress;
	public $GTC;
	public $IpAddress;
	public $Person;
	public $TemplateDetail;
	public $Impressum;

	function __construct($ApiKey = "", $EmailAddress = "", $GTC = "", $IpAddress = "", $UserAgent = "", $Person, $TemplateDetail, $Impressum){		
		$this->ApiKey = (string)$ApiKey;
		$this->EmailAddress = (string)$EmailAddress;
		$this->GTC = (bool)$GTC;
		$this->IpAddress = (string)$IpAddress;
		$this->UserAgent = (string)$UserAgent;
		$this->Person = $Person;
		$this->TemplateDetail = $TemplateDetail;
		$this->Impressum = $Impressum;
	}
}
?>
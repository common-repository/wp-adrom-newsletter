<?php
class PhoneNumber
{
	public $CountryCode;
	public $Number;
	public $Prefix;
	
	function __construct($CountryCode = "", $Number = "", $Prefix = ""){	
		$this->CountryCode = (string)$CountryCode;
		$this->Number = (string)$Number;
		$this->Prefix = (string)$Prefix;
	}	
}
?>
<?php
class LeadUpdate
{
	public $ApiKey;
	public $Hash;
	public $Person;
	public $PhoneNumbers;
	
	function __construct($ApiKey = "", $Hash = "", $Person = "", $PhoneNumbers = array()){	
		$this->ApiKey = (string)$ApiKey;	
		$this->Hash = (string)$Hash;
		$this->Person = $Person;
		$this->PhoneNumbers = (array)$PhoneNumbers;
	}
}
?>
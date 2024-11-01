<?php
class Impressum
{
	public $Name;
	public $Street;
	public $PostalCode;
	public $City;
	public $Country;
	public $Phonenumber;
	public $EmailAddress;
	
	function __construct($Name = "", $Street = "", $PostalCode = "", $City = "", $Country = "", $Phonenumber = "", $EmailAddress = ""){	
		$this->Name = (string)$Name;
		$this->Street = (string)$Street;
		$this->PostalCode = (string)$PostalCode;
		$this->City = (string)$City;
		$this->Country = (string)$Country;
		$this->Phonenumber = (string)$Phonenumber;
		$this->EmailAddress = (string)$EmailAddress;	
	}	
}
?>
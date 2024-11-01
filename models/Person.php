<?php
class Person
{
	public $Gender;
	public $Firstname;
	public $Surname;
	public $Birthday;
	public $Street;
	public $StreetNumber;
	public $PostalCode;
	public $City;
	public $Country;
	
	function __construct($Gender = 1, $Firstname = "", $Surname = "", $Birthday = "", $Street = "", $StreetNumber = "", $PostalCode = "", $City = "", $Country = ""){	
		$this->Gender = (int)$Gender;
		$this->Firstname = (string)$Firstname;
		$this->Surname = (string)$Surname;
		$this->Birthday = (string)$Birthday;
		$this->Street = (string)$Street;
		$this->StreetNumber = (string)$StreetNumber;
		$this->PostalCode = (string)$PostalCode;
		$this->City = (string)$City;
		$this->Country = (string)$Country;		
	}	
}
?>
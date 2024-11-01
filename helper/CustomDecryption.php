<?php
/*
20151208 - LUST - added warning suppression
20151216 - LUST - added check if mcrypt_decrypt function exists
*/

class CustomDecryption
{
        private $password;
        private $salt;
        private $iterations;
        private $keylength;
        private $iv;

	function __construct($customDecryptionParams)
	{
		//---------------------------------------------
		$this->password = $customDecryptionParams['password'];
		$this->salt = $customDecryptionParams['salt'];
		$this->iterations = $customDecryptionParams['iterations'];
		$this->keylength = $customDecryptionParams['keylength'];
		$this->iv = $customDecryptionParams['iv'];
		//---------------------------------------------
	}

	function decrypt($data)
	{
		$encryptionMode = substr($data,0,3);
		//maybe change decryption parameters based on encryptionMode
		//not used now... prepared for future use.
		//----------------------------------------------------------
		$data = substr($data,3);
		$key = $this->pbkdf2($this->password,$this->salt,$this->iterations,$this->keylength);
		if(function_exists('mcrypt_decrypt'))
		{
		        return @mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($this->urlsafe_base64_decode($data)), MCRYPT_MODE_CBC, $this->iv);
		}
        return 'Function mcrypt_decrypt does not exist!';
	}

	function encrypt($text)
	{
		$key = $this->pbkdf2($this->password,$this->salt,$this->iterations,$this->keylength);
		return $this->urlsafe_base64_encode(base64_encode(@mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_CBC, $this->iv)));
	}

	function pbkdf2( $p, $s, $c, $kl, $a = 'sha1' )
	{
	    $hl = strlen(hash($a, null, true)); # Hash length
	    $kb = ceil($kl / $hl);              # Key blocks to compute
   	 $dk = '';                           # Derived key

	    # Create key
	    for ( $block = 1; $block <= $kb; $block ++ ) {

	        # Initial hash for this block
	        $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);

	        # Perform block iterations
	        for ( $i = 1; $i < $c; $i ++ )

	            # XOR each iterate
	            $ib ^= ($b = hash_hmac($a, $b, $p, true));

	        $dk .= $ib; # Append iterated block
	    }

	    # Return derived key of correct length
	    return substr($dk, 0, $kl);
	}

	function urlsafe_base64_decode($data)
	{
		$number = substr($data,strlen($data)-1,1);
		$data = substr($data,0,strlen($data)-1);
		switch($number)
		{
			case '1': { $data .= '='; break; }
			case '2': { $data .= '=='; break; }
		}
		//var_dump($data);*/
		$data = base64_decode($data);
		//var_dump($data);
		return $data;
	}

	function urlsafe_base64_encode($string)
	{
		#First base64 encode
		$data = base64_encode($string);
		return $data;
		#Base64 strings can end in several = chars. These need to be translated into a number
		$no_of_eq = substr_count($data, "=");
		$data = str_replace("=", "", $data);
		$data = $data.$no_of_eq;

		#Then replace all non-url safe characters
		$data = str_replace(array('+','/'),array('-','_'),$data);
		return $data;
	}
}
?>

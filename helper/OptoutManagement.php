<?php
/*
V 1.5
20151118 - LUST - added possibility to use API additional to DIRECT optout
20151119 - LUST - bugfix IsPortalOptout was used instead isPortalOptout
20151120 - LUST - submitOptout() now returns false if error, true if success and null if emailAddress is unknown
20151124 - MOSE - submitOptout($ip = '') extended due to no ip information available if called from crontab
20151127 - GOBE - added logging stuff for wordpress
20151211 - LUST - changed decode() to take care of special url safe base64 (number of filling chars at the end)
20151215 - LUST - bugfix - using of unassigned var $tmp
20160218 - LUST - added emailSystemClientId to Optout-Requests
20160226 - GOBE - "logit()"-function now always creates a new folder if not available, also "$log_path" changed so the "temp" folder is createt in the same folder where this script is located
20160304 - LUST - changed function submitOptout($ip = '', $type = '') to accept optional type to override hash determined type of optout
20160504 - LUST - replaced old TemporaryError/email unknown status with new ResponseStatus NotFound
*/

date_default_timezone_set("Europe/Vienna");
require_once('CustomDecryption.php');

class OptoutManagement
{
	private $customDecrypter;
	private $emailAddress;
	private $mailingId;
	private $category;
	private $externalMailingId;
	private $emailSystemId;
	private $logLevel;
	private $emailSystemClientId;
	private $serviceIp;
	private $useApiOptout;
	private $apiKey;
	private $apiUrl;
	public $isPortalOptout;
	public $isEmailChangeable;

	function __construct($logLevel = 0, $emailSystemClientId, $serviceUri, $apiUrl = "", $apiKey = "", $customDecryptionParams)
	{
		$this->logLevel = $logLevel;
		$this->emailSystemClientId = $emailSystemClientId;
		$this->customDecrypter = new CustomDecryption($customDecryptionParams);
		$this->service = $serviceUri;
		$this->isPortalOptout = false;
		$this->useApiOptout = false;
		$this->isEmailChangeable = false;
		if($apiUrl != "" && $apiKey != "")
		{
			$this->configureApi($apiUrl, $apiKey);
		}
	}

	function init($data)
	{
		$this->logit(3, "----------------------------------------------------------------------------------------------------------------------------------------------");
		$this->logit(3, "Init(".strlen($data)."): ".print_r($data,true));
		$this->logit(3, "IP: ".$_SERVER["REMOTE_ADDR"]." Agent: ".$_SERVER["HTTP_USER_AGENT"]);
		if(substr($data,0,3) == 'a00')
		{
			$result = $this->parseCryptedElaineData(substr($data,3));
		}
		else if($data == "" || strpos($data,'@') > 0)
		{
			$this->isPortalOptout = true;
			$this->isEmailChangeable = true;
			if($data != "")
			{
				$this->setEmail(trim($data));
			}
			$this->logit(3,"Init OK(direct Portal)");
			return true;
		}
		else
		{
			$result = $this->parseCryptedData($data);
		}
		return $result;
	}

	function configureApi($apiUrl, $apiKey)
	{
		$this->apiKey = $apiKey;
		$this->apiUrl = $apiUrl;
		$this->useApiOptout = true;
	}

	function submit($url,$data)
	{

		$curl = curl_init();
		curl_setopt($curl,CURLOPT_URL,$url);
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($curl,CURLOPT_TIMEOUT,30);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

		if (strpos($_SERVER['HTTP_HOST'],'localhost') !== false) {
			//if localhost, disable "ssl verify"
			curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
		}

		if($data != null)
		{
			curl_setopt($curl,CURLOPT_POST,true);
                	curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
		}
                //curl_setopt($curl,CURLOPT_HEADER,true);

                $response = curl_exec($curl);
                //$json = json_decode($response);
                $curlstatus = curl_getinfo($curl);
                curl_close($curl);

                //$this->logit(3, 'Curlstatus: '.print_r($curlstatus,true));
		//$this->logit(3, 'Response: '.print_r($response,true));
		return array('curlstatus'=>$curlstatus, 'response' => $response);
	}

	function parseCryptedData($data)
	{
		$decrypted = trim($this->customDecrypter->decrypt($data));
		$this->logit(3, 'Decrypted: '.$decrypted);
		$json = json_decode($decrypted);
		$this->logit(3, 'JSON Object: '.print_r($json,true));
		if($json == NULL) { $this->logit(1,"json was NULL"); return false; }
		if(isset($json->email))	{ $this->emailAddress = $json->email; } else { $this->logit(1,"no emailAddress"); return false; }
		if(isset($json->category))
		{
			if($json->category == "") { $this->isPortalOptout = true; $this->logit(1,"category empty - portal optout"); } else { $this->category = $json->category; }
		} else { $this->isPortalOptout = true; $this->logit(1,"no category - portal optout"); }
		if(isset($json->mailingId))  { $this->mailingId = $json->mailingId; } else { $this->logit(1,"no mailingId"); return false; }
		$this->logit(3,"Init OK");
		return true;
	}

	//ELAINE---------------------------------------------------------------------------
	function parseCryptedElaineData($data)
	{
		$decrypted = $this->decode($data);
		$this->logit(3, 'Decrypted(Elaine): '.$decrypted);
                $json = json_decode($decrypted);
                $this->logit(3, 'JSON Object(Elaine): '.print_r($json,true));
                if($json == NULL) { $this->logit(1,"json was NULL(Elaine)"); return false; }
                if(isset($json->emailAddress))  { $this->emailAddress = $json->emailAddress; } else { $this->logit(1,"no emailAddress"); return false; }
                if(isset($json->category))
		{
			if($json->category == "") { $this->isPortalOptout = true; $this->logit(1,"category empty - portal optout"); } else { $this->category = $json->category; }
		} else { $this->isPortalOptout = true; $this->logit(1,"no category - portal optout"); }
                if(isset($json->externalMailingId))  { $this->externalMailingId = $json->externalMailingId; } else { $this->logit(1,"no externalMailingId"); return false; }
                if(isset($json->emailSystemId))  { $this->emailSystemId = $json->emailSystemId; } else { $this->logit(1,"no emailSystemId"); return false; }
		$this->logit(3,"Init OK(Elaine)");
                return true;
	}

	function decode($data)
	{
		//Obsolete nach Umstellung aller Elaines auf geaenderte hasherzeugung in p_unsub und cl_unsub------------------
		$ms = substr($data,5,5);
		if($ms == 'Rmx25')
		{
			$tmp = strtr($data, '-_,', '+/=');
			$base64 = substr($tmp,0,5).substr($tmp,10);
		}
		else//End Obsolete-------------------------------------------------------------------------------------------------
		{
			$tmp = strtr($data, '-_', '+/');
			$base64 = substr($tmp,0,5).substr($tmp,10);
			//detect and handle padding chars---------
			$number = substr($base64,strlen($base64)-1,1);
			if(in_array($number, array(0,1,2)))
			{
				$base64 = substr($base64,0,strlen($base64)-1);
				switch($number)
				{
					case '1': { $base64 .= '='; break; }
					case '2': { $base64 .= '=='; break; }
				}
			}
			//-----------------------------------------
		}
       		return base64_decode($base64);
	}
	//---------------------------------------------------------------------------------

	function retrieveCategoryTitle($category = null)
	{
		if($category == null)
		{
			$category = $this->category;
		}

		if($this->useApiOptout)
                {
                	$data = array('apiKey' => $this->apiKey, 'categoryTag' => $category);
                        $data = json_encode($data);
                        $this->logit(3,"retrieveCategoryTitle(Api) - ".print_r($data,true));
                        $response = $this->submit($this->apiUrl.'/category',$data);
			if($response['curlstatus']['http_code'] == 200)
               	 	{
                        	$json = json_decode($response['response']);
                        	if($json == null) { $this->logit(1,"retrieveCategoryTitle - json was NULL"); return $category;  }
				if(isset($json->requestStatus) && $json->requestStatus == "Successful" && isset($json->data->categoryTitle))
				{
                        		return $json->data->categoryTitle;
				}
                	}
                }
		else
		{
			$this->logit(3,"retrieveCategoryTitle - categoryTag: ".$category);
			$response = $this->submit("http://".$this->service."/api/blacklist/getcategory/".$category, null);
			if($response['curlstatus']['http_code'] == 200)
                	{
                        	$json = json_decode($response['response']);
                        	if($json == null) { $this->logit(1,"retrieveCategoryTitle - json was NULL"); return $category;  }
                        	return $json->title;
                	}
		}

		$this->logit(1,"Error on retrieveCategoryTitle - ".print_r($response['curlstatus'],true));
		$this->logit(1,"Response: ".$response['response']);
		return $category;
	}

	function getEmail()
	{
		return $this->emailAddress;
	}

	function setEmail($email)
	{
		$this->emailAddress = $email;
	}

	function getMailingId()
	{
		return $this->mailingId;
	}

	function submitOptout($ip = '', $type = '')
	{
		//submit credential/optoutinfo to CusomerUnsubscribe
		if(empty($ip))
		{
			$ip = strpos($_SERVER['HTTP_HOST'],'localhost') !== false ? "127.0.0.1" : $_SERVER['REMOTE_ADDR'];
		}
		if(!empty($type))
		{
			if($type == 'unsubscribe')
			{
				$this->isPortalOptout = true;
			}
			else
			{
				$this->isPortalOptout = false;
			}
		}

		if($this->isPortalOptout)
		{
			if($this->mailingId > 0)
			{
				$data = array('EmailAddress'=>$this->emailAddress,'Source'=>'Unsubscribe', 'Client'=>$this->emailSystemClientId,'IpAddress'=>$ip,'mailingId'=>$this->mailingId,'emailSystemClientId'=>$this->emailSystemClientId);
			}
			else
			{
				$data = array('EmailAddress'=>$this->emailAddress,'Source'=>'Unsubscribe', 'Client'=>$this->emailSystemClientId,'IpAddress'=>$ip,'externalMailingId'=>$this->externalMailingId,'emailSystemId'=>$this->emailSystemId,'emailSystemClientId'=>$this->emailSystemClientId);
			}

			if($this->useApiOptout)
			{
				$data['apiKey'] = $this->apiKey;
				$data = json_encode($data);
				$this->logit(1,"submitOptout(Api) - portal - ".print_r($data,true));
				$response = $this->submit($this->apiUrl.'/unsubscribe',$data);
				if($response['curlstatus']['http_code'] == 200)
                {
					$json = json_decode($response['response']);
                    if(isset($json->requestStatus) && $json->requestStatus == "Successful")
                    {
                            return true;
                    }
					else if(isset($json->requestStatus) && $json->requestStatus == "NotFound")
					{
						return null;
					}
                }
			}
			else
			{
				$data = json_encode($data);
				$this->logit(1,"submitOptout - portal - ".print_r($data,true));
                $response = $this->submit("http://".$this->service."/api/blacklist",$data);

				if($response['curlstatus']['http_code'] == 200)
                {
                        return true;
                }
				else if($response['curlstatus']['http_code'] == 400)
                {
                        $jsonResponse = json_decode($response['response']);
                        if(isset($jsonResponse->message) && $jsonResponse->message == 'Unknown emailAddress')
                        {
                                return null;
                        }
                }
			}
		}
		else
		{
			if($this->mailingId > 0)
            {
                    $data = array('EmailAddress'=>$this->emailAddress,'Source'=>'Unsubscribe', 'Client'=>$this->category,'IpAddress'=>$ip,'mailingId'=>$this->mailingId,'emailSystemClientId'=>$this->emailSystemClientId);
            }
            else
            {
                    $data = array('EmailAddress'=>$this->emailAddress,'Source'=>'Unsubscribe', 'Client'=>$this->category,'IpAddress'=>$ip,'externalMailingId'=>$this->externalMailingId,'emailSystemId'=>$this->emailSystemId,'emailSystemClientId'=>$this->emailSystemClientId);
            }

			if($this->useApiOptout)
            {
				$data['apiKey'] = $this->apiKey;
				$data = json_encode($data);
				$this->logit(1,"submitOptout(Api) - category - ".print_r($data,true));
				$response = $this->submit($this->apiUrl.'/unsubscribecategory',$data);
				if($response['curlstatus']['http_code'] == 200)
                {
					$json = json_decode($response['response']);
					if(isset($json->requestStatus) && $json->requestStatus == "Successful")
                    {
                    	return true;
					}
					else if(isset($json->requestStatus) && $json->requestStatus == "NotFound")
                    {
                    	return null;
                    }
                }
			}
			else
			{
				$data = json_encode($data);
				$this->logit(1,"submitOptout - category - ".print_r($data,true));
                                $response = $this->submit("http://".$this->service."/api/blacklist/categoryunsubscribe",$data);
				if($response['curlstatus']['http_code'] == 200)
                		{
                        		return true;
                		}
				else if($response['curlstatus']['http_code'] == 400)
				{
					$jsonResponse = json_decode($response['response']);
					if(isset($jsonResponse->message) && $jsonResponse->message == 'Unknown emailAddress')
					{
						return null;
					}
				}
			}
		}

		$this->logit(1,"Error on submitOptout - ".print_r($response,true));
		return false;
	}

	function logit($level,$msg)
	{
		if($level != 0 && $level <= $this->logLevel)
		{
			$log = date("Y-m-d H:i:s").'->['.getmypid().'] '.$msg."\r\n";
			if(false)
			{
				echo $log;
			}
			else
			{
				$log_path = dirname(__FILE__)  ."/temp";

				if( function_exists('wp_upload_dir')){
					//FOR WORDPRESS ONLY
					$wp_upload_dir = wp_upload_dir();
					//print_R($wp_upload_dir['basedir']);
					if (!file_exists($wp_upload_dir['basedir'] . '/wp_adrom_newsletter')) {
						mkdir($wp_upload_dir['basedir'] . '/wp_adrom_newsletter', 0777, true);
					}
					$log_path = $wp_upload_dir['basedir'] . '/wp_adrom_newsletter';
				} else {
					if (!file_exists($log_path)) {
						mkdir($log_path, 0777, true);
					}
				}

				$fp = fopen($log_path . '/OptoutManagement_'.$this->emailSystemClientId.'.log','a');
				fwrite($fp,$log);
				fflush($fp);
				fclose($fp);
			}
		}
	}
}
?>

<?php
class SanitizeClass
{
	private $errorLogging;
	private $sanitization_array;
	private $dateClass;
	
	private $encryptionEncoderArray;
	private $encryptionDecoderArray;
	
	private $encryptionKey;
	private $iv;

	protected function __construct()
	{
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'loggingINC.php');
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Dates' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'datesINC.php');
	
	$this->errorLogging = LoggingClass::getInstance();
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$this->dateClass = new ProceduralDates();
	
	//This is an array of items we are going to replace
	$this->sanitization_array = array(
	'"' => "!dqt!",
	'`' => "!apt!",
	"'" => "!sqt!",
	"&" => "!amp!",
	"*" => "!str!"
	);			
	}
	
	public static function getInstance()
    {
    //this creates one instances of the class if it doesn't exist, and if it does it returns that one instance
    static $instance = null;

        if (null === $instance) 
        {
        $instance = new static();
        }

	return $instance;
    }
	
	public function setCryptoKey($suppliedKey = false)
	{//This can only be done after the session is validated/created
	
		//check if database encryption is enabled once when the class is created, and set the appropriate arrays
		if (DATABASE_ENCRYPTION === TRUE)
		{
		//The encoder and decoder arrays should contain the same values, with the key/value pairs inverted
		$this->encryptionEncoderArray = array("a" => "z", "b" => "a", "c" => "b", "d" => "c", "e" => "d", "f" => "e", "g" => "f", "h" => "g", "i" => "h", "j" => "i", "k" => "j", "l" => "k", "m" => "l", "n" => "m", "o" => "n", "p" => "o", "q" => "p", "r" => "q", "s" => "r", "t" => "s", "u" => "t", "v" => "u", "w" => "v", "x" => "w", "y" => "x", "z" => "y");
		$this->encryptionDecoderArray = array("z" => "a", "a" => "b", "b" => "c", "c" => "d", "d" => "e", "e" => "f", "f" => "g", "g" => "h", "h" => "i", "i" => "j", "j" => "k", "k" => "l", "l" => "m", "m" => "n", "n" => "o", "o" => "p", "p" => "q", "q" => "r", "r" => "s", "s" => "t", "t" => "u", "u" => "v", "v" => "w", "w" => "x", "x" => "y", "y" => "z");		
	
		//set the hashed password for the encyption key
			if ($suppliedKey === false)
			{
			$userCryptoHash = $_SESSION["usergroup"];
			}
			else
			{
			$userCryptoHash = $suppliedKey;	
			}
			
		$password = DATABASE_SITE_KEY . $userCryptoHash;		
		$hashedPasswordHex = bin2hex($password);		
		$keySection = substr($hashedPasswordHex, CRYPTO_HEX_OFFSET, 32);
			
		//set the encyption key
		$this->encryptionKey = pack('H*', "$keySection");	
		}
	}

	public function sanitizeValues($unsanitized, $reverse = false, $encrypt = false)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
		if (is_array($unsanitized))
		{
		$unsanitized_copy = $unsanitized;
		$sanitized = array();
			foreach ($unsanitized_copy as $unsKey => $unsValue)
			{
			$cleaned_key = $this->sanitizeString($unsKey, $reverse, false);
			
				if(is_array($unsValue))
				{
				$cleaned_value = $this->sanitizeArray($unsValue, $reverse, $encrypt);
				}
				else
				{
				$cleaned_value = $this->sanitizeString($unsValue, $reverse, $encrypt);
				}
				
			$sanitized["$cleaned_key"] = $cleaned_value;
			}
		}
		else
		{
		$sanitized = $this->sanitizeString($unsanitized, $reverse, $encrypt);
		}
	
	return $sanitized;
	}
	
	private function sanitizeString($unsanitized, $reverse, $encrypt)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Unsanitized: $unsanitized");
	
		if (is_numeric($unsanitized))
		{
		return $unsanitized;	
		}
		
		//we shouldn't encrypt / decrypt dates - skip encryption if it is enabled
		if ($encrypt === true)
		{
			if ($this->dateClass->verifyDatePublic($unsanitized))
			{
			$encrypt = false;
			}
		}
		
		$sanitized = $unsanitized;
		
		if($reverse === false)
		{		
			foreach ($this->sanitization_array as $look_for => $replace_with)
			{
			$sanitized = str_replace($look_for, $replace_with, $sanitized);				
			}
		}
		else
		{
			if (DATABASE_ENCRYPTION === TRUE && $encrypt === TRUE)
			{
			$sanitized = $this->decryptString($sanitized);	
			}
			
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "reverse: $reverse");
			foreach ($this->sanitization_array as $look_for => $replace_with)
			{
			$sanitized = str_replace($replace_with, $look_for, $sanitized);
			}
			
		$firstDigit = substr($sanitized, 4, 1);
		$secondDigit =  substr($sanitized, 7, 1);
		
			if ($firstDigit == "-" && $secondDigit == "-")
			{
			$validDateFormat = $this->dateClass->monthYearDateFormatPublic($sanitized);
				if ($validDateFormat !== false)
				{
				$sanitized = $validDateFormat;
				}
			}
		}
		
		if (DATABASE_ENCRYPTION === TRUE && $encrypt === TRUE)
		{
			if($reverse === false)
			{
			$sanitized = $this->encryptString($sanitized);
			}
		}
		
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Sanitized: $sanitized");
	return $sanitized;
	}
	
	private function sanitizeArray($unsanitized, $reverse, $encrypt)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$unsanitized_copy = $unsanitized;
	
		foreach ($unsanitized_copy as $unsKey => $unsValue)
		{
		$cleaned_key = $this->sanitizeString($unsKey, $reverse, false);
		
			if(is_array($unsValue))
			{
			$cleaned_value = $this->sanitizeArray($unsValue, $reverse, $encrypt);
			}
			else
			{
			$cleaned_value = $this->sanitizeString($unsValue, $reverse, $encrypt);
			}
		$sanitized["$cleaned_key"] = $cleaned_value;
		}
		
	return $sanitized;
	}
	
	private function encryptString($unencryptedString)
	{
	$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->encryptionKey, $unencryptedString, MCRYPT_MODE_CBC, CRYPTO_IV);
	
	$cipherEncodedString = $this->encodeString($ciphertext);
	
	$ciphertext_base64 = base64_encode($cipherEncodedString);
	
	return $ciphertext_base64;
	}
	
	private function decryptString($encryptedString)
	{
	$ciphertext_dec = base64_decode($encryptedString);
	
	$cipherDecodedString = $this->decodeString($ciphertext_dec);
	
	$plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->encryptionKey, $cipherDecodedString, MCRYPT_MODE_CBC, CRYPTO_IV);
	
	$plaintext_dec = trim($plaintext_dec);
	
	return $plaintext_dec;
	}
	
	private function encodeString($unencodedString)
	{
	$characterValuesArray = str_split($unencodedString);
	$encodedString = "";
		foreach ($characterValuesArray as $key => $value)
		{
			if (array_key_exists($value, $this->encryptionEncoderArray))
			{
			$encodedString = $encodedString . $this->encryptionEncoderArray["$value"];	
			}
			else
			{
			$encodedString = $encodedString . $value;	
			}
		}
	return $encodedString;
	}
	
	private function decodeString($encodedString)
	{
	$characterValuesArray = str_split($encodedString);
	$unencodedString = "";
		foreach ($characterValuesArray as $key => $value)
		{
			if (array_key_exists($value, $this->encryptionDecoderArray))
			{
			$unencodedString = $unencodedString . $this->encryptionDecoderArray["$value"];	
			}
			else
			{
			$unencodedString = $unencodedString . $value;	
			}
		}
	return $unencodedString;	
	}
	
	private function __clone()
    {
	$this->errorLogging->logInfo(__CLASS__ , __METHOD__, "Called.");
    }

    private function __wakeup()
    {
	$this->errorLogging->logInfo(__CLASS__,  __METHOD__, "Called.");
    }
}
?>
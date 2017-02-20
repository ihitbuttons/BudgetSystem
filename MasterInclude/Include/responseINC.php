<?php
//ADD IN API TRACKING
//ADD IN API RATE LIMITING
class ResponseClass
{
	public $apiResponseJson;
	
	private $errorLogging;
	private $sessionHeaderArray;
	private $apiHeaderArray;
	private $errorHeaderArray;
	
	protected function __construct()
	{
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'loggingINC.php');
	$this->errorLogging = LoggingClass::getInstance();
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		
	$this->sessionHeaderArray = array("sessionID" => "Not Specified", "response" => "Not Specified");
	$this->apiHeaderArray = array("apiCall" => "Not Specified", "response" => "Not Specified");
	$this->errorHeaderArray = array("error" => "False", "response" => "Not Specified");
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
	
	public function sessionResponse($sessionID, $response_message)
	{
	$this->setSessionHeaderValue("sessionID", "$sessionID");
	$this->setSessionHeaderValue("response", $response_message);	
	}
	
	public function apiCall($apiCall)
	{
	$this->setAPIHeaderValue("apiCall", $apiCall);	
	}
	
	public function apiResponse($response_message)
	{
	$this->setAPIHeaderValue("response", $response_message);
	}
	
	public function apiError($error_message)
	{
	$this->setErrorHeaderValue("error", "True");
	$this->setErrorHeaderValue("response", $error_message);
	}
	
	private function setSessionHeaderValue($header, $headerValue)
	{
		if (array_key_exists($header, $this->sessionHeaderArray))
		{
		$this->sessionHeaderArray["$header"] = $headerValue;
		}
		else
		{
		return false;
		}
	return true;
	}
	
	private function setAPIHeaderValue($header, $headerValue)
	{
		if (array_key_exists($header, $this->apiHeaderArray))
		{
		$this->apiHeaderArray["$header"] = $headerValue;
		}
		else
		{
		return false;
		}
	return true;
	}
	
	private function setErrorHeaderValue($header, $headerValue)
	{
		if (array_key_exists($header, $this->errorHeaderArray))
		{
		$this->errorHeaderArray["$header"] = $headerValue;
		}
		else
		{
		return false;
		}
	return true;
	}
	
	public function returnResponse()
	{
	//this should engage the response interface for formatting the response!
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");

	$final_array = array("session" => $this->sessionHeaderArray, "apiResponse" => $this->apiHeaderArray, "error" => $this->errorHeaderArray);
		
		if (!$responseJson = json_encode($final_array))
		{
		$this->errorLogging->logError(__CLASS__, __METHOD__, "Failed json_encode.");

		}
	
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, $responseJson);	
	return $responseJson;
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
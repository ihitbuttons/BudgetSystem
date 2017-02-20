<?php
class UserClass
{
	public $apiResponseJson;
	
	private $dbConnection;
	private $responseClass;
	private $errorLogging;
	private $dateClass;

	
	public function UserClass()
	{
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'responseINC.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'databaseINC.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'loggingINC.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Dates' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'datesINC.php');
		
		$this->responseClass = ResponseClass::getInstance();

		$this->dbConnection = new DatabaseClass(SESSION_DATABASE);
		$this->dateClass = new ProceduralDates();
		$this->errorLogging = LoggingClass::getInstance();
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	}
	
	public function argumentList($action)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$masterListArray = array();
	
	//$masterListArray["listPurchasers_array"] = ""; //*
	$masterListArray["createNewUser"] = array("userName" => "string", "emailAddress" => "string", "invationCode" => "string"); //*
	
		//check if it is a discovery method
		if ($action === "argumentList")
		{
		return $masterListArray;
		}
		else
		{
		$action = $action . "_array";	
		}
	
		//See if the method is callable by the API
		if(array_key_exists($action, $masterListArray))
		{
		return $masterListArray["$action"];
		}
		else
		{
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Failed for find argument list for action $action.");
		return false;
		}
	}	
	
	//This method will allow you to obfuscate Method names, so the API action doesn't directly correlate to an actual method name
	public function obfuscateMethodNames($action)
	{
	return $action;	
	}
	
	public function createNewUser($userName, $emailAddress, $invationCode)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	$sanitizeIt = SanitizeClass::getInstance();	
	$sanitizeIt->setCryptoKey(USER_CRYPTO_KEY);	
	
	$decodedCode = $sanitizeIt->sanitizeValues($invationCode, TRUE, TRUE);	
	$explodedCode = explode("==", $decodedCode);
	$finalExploded = array();
		foreach($explodedCode as $explodedKey => $explodedValue)
		{
		$explodedAgain = explode("=", $explodedValue);
				foreach ($explodedAgain as $explodedAgainKey => $explodedAgainValue)
				{
				$finalExploded[] = $explodedAgainValue;
				}
		}
		
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, $finalExploded);
		
	$insert_array = array(
	"table" => 'usertable', 
	"columns" => array(
	"usergroup",
	"userid",
	"username",
	"emailaddress",
	"password",
	"pin"),
	"values" => array(
	"$userGroup",
	"$userid",
	"$userName",
	"$emailAddress",
	"$password",
	"$pin")
	);
	
	//$returnedArray = $this->dbConnection->ConstructInsert($insert_array);
	//$this->responseClass->apiResponse($returnedArray);
	return true;
	
	$sanitizeIt->setCryptoKey();
	}
	
}



?>
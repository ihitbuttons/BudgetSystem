<?php
class UnitTestClass
{
	public $apiResponseJson;
	
	private $dbConnection;
	private $responseClass;
	private $errorLogging;
	private $dateClass;

	
	public function UnitTestClass()
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
	$masterListArray["listMethodInputs_array"] = array("api" => "string", "action" => "string"); //*
	
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
	
	public function listMethodInputs($api, $action)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$action_called = str_replace("_array", "", $action);
	$api = str_replace("API", "", $api);
	
		switch($api)
		{
			case "budget":
				DEFINE('METHODINPUTSINCLUDE', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Budget' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'budgetINC.php');
				$APICLASS = 'BudgetClass';
				$singleton = false;
				break;
				
			case "session":
				DEFINE('METHODINPUTSINCLUDE', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Session' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'sessionINC.php');
				$APICLASS = 'SessionClass';
				$singleton = true;
				break;
				
			case "dates":
				DEFINE('METHODINPUTSINCLUDE', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Dates' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'datesINC.php');
				$APICLASS = 'ProceduralDates';
				$singleton = false;
				break;
				
			case "recipie":
				DEFINE('METHODINPUTSINCLUDE', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Recipie' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'recipieINC.php');
				$APICLASS = 'RecipieClass';
				$singleton = false;
				break;
			
			case "user":
				DEFINE('METHODINPUTSINCLUDE', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'User' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'userINC.php');
				$APICLASS = 'UserClass';
				$singleton = false;
				break;
			
			default:
				genericAPIError("Unsupported Action 4 $api $action");
				break;			
		}
		
		require_once(METHODINPUTSINCLUDE);
		//lets see if it is a singleton, as this will change how we get the class
		if ($singleton === true)
		{
		$default_api_class = $APICLASS::getInstance();	
		}
		else
		{
		$default_api_class = new $APICLASS();
		}
		
	$methodArguments = $default_api_class->argumentList($action_called);
	
	$this->responseClass->apiResponse($methodArguments);	
	}
	
}



?>
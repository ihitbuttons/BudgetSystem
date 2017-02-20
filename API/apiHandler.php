<?php
/*
API Handler
This file will handle all API requests and, include the appropriate files, run the appropriate methods, and respond to the front end.
*/
//These are required for all API calls
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'master.inc.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Session' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'sessionINC.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'responseINC.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'sanitizeINC.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'loggingINC.php');

$errorLogging = LoggingClass::getInstance();

//Sanatize POST / GET / COOKIE
$sanitizeIt = SanitizeClass::getInstance();	
$_GET = $sanitizeIt->sanitizeValues($_GET);
$_COOKIE = $sanitizeIt->sanitizeValues($_COOKIE);
$_POST = $sanitizeIt->sanitizeValues($_POST);

	//Get the specified action and sessionToken
	if (array_key_exists("action", $_GET) && array_key_exists("sessionToken", $_POST) && array_key_exists("api", $_GET))
	{//all required elements are fullfilled
	$action = $_GET["action"];	
	$sessionToken = $_POST["sessionToken"];
	$api = $_GET["api"];	
	}
	elseif (array_key_exists("api", $_GET) && array_key_exists("action", $_GET))
	{//missing sessionToken. This should only be valid for the session API
		if ($_GET["api"] == "session")
		{
		$action = $_GET["action"];	
		$sessionToken = "Invalid";	
		$api = $_GET["api"];
		}
		else
		{
		genericAPIError("Missing action (GET) or sessionToken (POST)");
		}
	}
	else
	{
	genericAPIError("Missing action (GET) or sessionToken (POST)");
	}

	//Determine the includes
	switch($api)
	{
		case "budget":
			DEFINE('APIINCLUDE', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Budget' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'budgetINC.php');
			$APICLASS = 'BudgetClass';
			$singleton = false;
			break;
			
		case "session":
			DEFINE('APIINCLUDE', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Session' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'sessionINC.php');
			$APICLASS = 'SessionClass';
			$singleton = true;
			break;
			
		case "dates":
			DEFINE('APIINCLUDE', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Dates' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'datesINC.php');
			$APICLASS = 'ProceduralDates';
			$singleton = false;
			break;
			
		case "recipie":
			DEFINE('APIINCLUDE', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Recipie' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'recipieINC.php');
			$APICLASS = 'RecipieClass';
			$singleton = false;
			break;
		
		case "user":
			DEFINE('APIINCLUDE', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'User' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'userINC.php');
			$APICLASS = 'UserClass';
			$singleton = false;
			break;
			
		case "unittest":
			DEFINE('APIINCLUDE', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'UnitTest' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'unitTestINC.php');
			$APICLASS = 'UnitTestClass';
			$singleton = false;
			break;
		
		default:
			genericAPIError("Unsupported Action 1");
			break;			
	}

//create the response class
$responseClass = ResponseClass::getInstance();
$responseClass->apiCall("$api::$action");
	
	//We can skip this step for the session class itself
	if ($api !== "session") 
	{//Check the supplied session sessionToken
	$session_check_session = SessionClass::getInstance();
		//Check if the supplied session token is valid
		if (!$session_check_session->checkSession($sessionToken))
		{/****It is noteworthy the know that the checkSession method also controls the API rate limit, and report an invalid session if the API rate limit is reached ****/
		echo $responseClass->returnResponse();
		die();
		}
	$sanitizeIt->setCryptoKey();
	}
	else
	{
	$sanitizeIt->setCryptoKey(USER_CRYPTO_KEY);	
	}
	
	//Validate and run the appropriate API method
	switch ($action)
	{	
		//The Default case should take any standard API method. New cases could be made for specific circumstances, if need be.
		default:
			require_once(APIINCLUDE);
				//lets see if it is a singleton, as this will change how we get the class
				if ($singleton === true)
				{
				$default_api_class = $APICLASS::getInstance();	
				}
				else
				{
				$default_api_class = new $APICLASS();
				}	
			
			//set the array for the is_callable method
			$methodCallable = array($default_api_class, $action);
			
				if (is_callable($methodCallable, true))
				{//it is a callable method, lets see if it is "public" to the API
				//lets call the argument list for the action specified, see if it returns a value
				$argument_list = $default_api_class->argumentList($action);					
					if (is_array($argument_list))
					{//it is an array, there are multiple required elements	
						//lets go through the array, see if all the elements are present
						if ($action !== "argumentList")
						{
							foreach($argument_list as $argumentKey => $argumentValue)
							{
								//Check if the element exists
								if (array_key_exists($argumentKey, $_POST))
								{	//The element exists, lets check if the type is correct
									if(checkType($_POST["$argumentKey"], $argumentValue))
									{
									$argument_array["$argumentKey"] = $_POST["$argumentKey"];
									}
									else
									{//the element is present, but is of the wrong type. Stop and return an error
									genericAPIError("Element $argumentKey is of the wrong type: should be $argumentValue");	
									}
								}
								else
								{//the element is not present, stop and return an error
								genericAPIError("Missing Required Elements: $argumentKey of type $argumentValue");
								}
							}
						//all the elements are present, lets get the unobfuscated method name
						$actualMethod = $default_api_class->obfuscateMethodNames($action);
						//lets call the function and pass in the arguments
						$returned = call_user_func_array(array($default_api_class, $actualMethod), $argument_array);
						}
						else
						{
						$responseClass->apiResponse($argument_list);	
						}										
					}
					elseif ($argument_list !== false)
					{//the list was returned, but with no elements. This call does not require arguments
					$returned = $default_api_class->$action();
					}
					else
					{//the result was false. We should return an error
					genericAPIError("Unsupported Action 2");
					}
				echo $responseClass->returnResponse();
				}
				else
				{//This is not a callable method, return an error
				genericAPIError("Unsupported Action 3");
				}
			break;
	}
	
	//This is a function to return API errors when no actual API call is made.
	function genericAPIError($errorMessage)
	{
	global $action;
	global $sessionToken;
	global $api;
	$responseClass = ResponseClass::getInstance();
	
	$responseClass->sessionResponse($sessionToken, "none");
	$responseClass->apiCall("$api::$action");
	$responseClass->apiResponse("none");
	$responseClass->apiError("$errorMessage");
	
	echo $responseClass->returnResponse();
	die();		
	}
	
	//This is a function to validate the argument pass is of the appropriate type
	function checkType($value, $type)
	{
	return TRUE;
	}
?>
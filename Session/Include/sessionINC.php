<?php
/*
If the session is expired, see if a cookie is set
	if the cookie IS set, then have the user log in by pin AND token
	if the pin is valid, extend the session
*/

class SessionClass
{
	public $base_dir;
	
	private $dbConnection;
	private $responseClass;
	private $errorLogging;
	
	private $sessionInterface;
	
	private $cookieName;

	protected function __construct()
	{
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'loggingINC.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'responseINC.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Session' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR . 'sessionInterface.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Session' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR . 'Implementors' . DIRECTORY_SEPARATOR . 'Session' . SESSION_STORE . '.php');
		
		$this->responseClass = ResponseClass::getInstance();
		$this->errorLogging = LoggingClass::getInstance();
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		
		$sessionInterface = "Session" . SESSION_STORE;
		
		$this->sessionInterface = new $sessionInterface;
		
		$this->cookieName = COOKIE_NAME;
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
	
	public function argumentList($action)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$masterListArray = array();
	
	$masterListArray["checkForSession_array"] = "";
	$masterListArray["forceLogout_array"] = "";
	
	$masterListArray["checkSession_array"] = array("sessionToken" => "string");	
	$masterListArray["login_array"] = array("uname" => "string", "pword" => "string");	
	$masterListArray["loginByPin_array"] = array("pin" => "int");
	$masterListArray["logOutSession_array"] = array("token" => "string");		
	
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
	
	public function checkForSession()
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		if(array_key_exists($this->cookieName, $_COOKIE))
		{
		$ebwSession = $_COOKIE["$this->cookieName"];
		}
		else
		{
		$ebwSession = FALSE;	
		}
		
	$sessionID = $this->sessionInterface->sessionIDByCookie($ebwSession);	
	
		if ($sessionID)
		{
		$tokensArray = $this->sessionInterface->pullValueSession($sessionID, "tokens");
		$validSession = $this->sessionInterface->pullValueSession($sessionID, "valid");
		
			if (is_array($tokensArray) && $validSession === 1)
			{
			reset($tokensArray);
			$token = key($tokensArray);
				
			$this->responseClass->sessionResponse($token, "Logged In");
			$this->responseClass->apiResponse("Session Active");
			return true;
			}
			else
			{
			$this->responseClass->sessionResponse("Invalid", "Logged Out");
			$this->responseClass->apiResponse("No Active Session");	
			return false;
			}
		}
		else
		{
		$this->responseClass->sessionResponse("Invalid", "Logged Out");
		$this->responseClass->apiResponse("No Active Session");	
		return false;	
		}		
	}
	
	public function login($user = 'null', $password = 'null')
	{	
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'databaseINC.php');
	$this->dbConnection = new DatabaseClass(SESSION_DATABASE);
	
	$select_array = array(
	"table" => 'usertable', 
	"where" => 'WHERE', 
	"columns" => array(
	"username",
	"userid",
	"usergroup",
	"pin"),
	"returns" => array(
	"username",
	"userid",
	"usergroup",
	"pin"),
	"conditions" => array(
		array(
		"column" => "username",
		"operator" => "=",
		"value" => "$user",
		"concat" => ""),
		array(
		"column" => "password",
		"operator" => "=",
		"value" => "$password",
		"concat" => "AND"),
		
	),
	"endingQuery" => ""
	);
		
	$returnedArray = $this->dbConnection->ConstructSelect($select_array);
	
		if (is_array($returnedArray))
		{
		$userid = $returnedArray["0"]["userid"];
		$usergroup = $returnedArray["0"]["usergroup"];
		$pin = $returnedArray["0"]["pin"];
		$token = $this->createSession($userid, $usergroup, $pin, FALSE, $_COOKIE["$this->cookieName"]);
		$this->responseClass->sessionResponse($token, "Logged In");
		$this->responseClass->apiResponse("Logged In");
		return true;
		}
		else
		{
		$this->errorLogging->logWarning(__CLASS__, __METHOD__, "Failed account lookup (array not returned)");
		$this->responseClass->sessionResponse("Invalid", "Logged Out");
		$this->responseClass->apiResponse("Username / Password Mismatch");
		return false;
		}
	}
	
	public function loginByPin($pin)
	{	
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		if(array_key_exists($this->cookieName, $_COOKIE))
		{
		$ebwSession = $_COOKIE["$this->cookieName"];
		}
		else
		{
		$ebwSession = FALSE;	
		}
		
	$sessionID = $this->sessionInterface->sessionIDByCookie($ebwSession);
	$sessionPin = $this->sessionInterface->pullValueSession($sessionID, "pin");

		if ($sessionPin == $pin)
		{
		//this is a valid pin, extend the session
		$userid = $this->sessionInterface->pullValueSession($sessionID, "userid");
		$usergroup = $this->sessionInterface->pullValueSession($sessionID, "usergroup");
		$token = $this->createSession($userid, $usergroup, $pin, FALSE, $ebwSession);
		$this->responseClass->sessionResponse($token, "Logged In");
		$this->responseClass->apiResponse("Session Extended");
		return true;
		}
		else
		{
		$this->responseClass->apiError("Invalid Pin");
		$this->responseClass->apiResponse("Invalid Pin");
		return false;
		}
	}
	
	public function logOutSession($token)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	//set default value for $sessionID
	$sessionID = false;
	
		//lets get an existing sessionID from the token
		if($token !== false)
		{
		$sessionID = $this->sessionInterface->sessionIDByToken($token);
		}
		
		//If it is a valid sessionID, lets check for expiration
		if($sessionID)
		{
		$this->responseClass->sessionResponse("Invalid", "Logged Out");
		$this->responseClass->apiResponse("Logged Out");
		
		//return $this->sessionInterface->endSession($sessionID);
		return true;
		}
	
	$this->responseClass->sessionResponse("Invalid", "Logged Out");
	$this->responseClass->apiError("Invalid Token");
	$this->responseClass->apiResponse("Invalid Token");	
	return false;
	}
	
	public function forceLogout()
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");

	$this->sessionInterface->forceEndSession();
	$this->responseClass->sessionResponse("Invalid", "Logged Out");
	$this->responseClass->apiResponse("Session Forceably Killed");	
	return true;
	}
	
	public function checkSession($token)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");	
	
	$sessionID = $this->sessionInterface->sessionIDByToken($token);
	
		if($this->validateSessionID($sessionID))
		{//It is a valid session, lets check the API rate limit
			if(!$this->apiRateLimit($sessionID))
			{
			return true;	
			}
			else
			{
			$this->responseClass->sessionResponse("Invalid", "API rate limit reached");
			$this->responseClass->apiError("API Rate Limit");
			$this->responseClass->apiResponse("API Rate Limit Reached");
			return false;	
			}
		}
		else
		{
		return false;	
		}
	}
	
	private function createSession($userid, $usergroup, $pin, $token, $cookie)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	//set default value for $sessionID
	$sessionID = false;
	
		//lets get an existing sessionID from the token
		if($token !== false)
		{
		$sessionID = $this->sessionInterface->sessionIDByToken($token);
		}
		
		if(!$sessionID)
		{
		//there was no sessionID for this token, lets get
			if($cookie !== false)
			{
			$sessionID = $this->sessionInterface->sessionIDByCookie($cookie);
			}
		}
	
		if(!$sessionID)
		{
		$sessionID = $this->sessionInterface->startSession();
		}
	
	$sessionToken = $this->GenerateToken(false);
	$currentTime = date("U");
		
	$this->sessionInterface->addValueToSession($sessionID, "userid", $userid);
	$this->sessionInterface->addValueToSession($sessionID, "usergroup", $usergroup);
	$this->sessionInterface->addValueToSession($sessionID, "expiration", $currentTime);
	$this->sessionInterface->addValueToSession($sessionID, "pin", $pin);
	$this->sessionInterface->addValueToSession($sessionID, "valid", 1);
	$this->sessionInterface->addValueToSession($sessionID, "numberApiCalls", 0);
	$this->sessionInterface->addValueToSession($sessionID, "timerApiCalls", $currentTime);
	
	$this->sessionInterface->addSessionIDByToken($sessionID, $sessionToken);
	$this->sessionInterface->addSessionIDByCookie($sessionID, $cookie);	
	
	return $sessionToken;
	}
	
	private function validateSessionID($sessionID)
	{
		//If it is a valid sessionID, lets check for expiration
		if($sessionID)
		{	
		//there is a current session
		$current_time = date("U");
		$session_time = $this->sessionInterface->pullValueSession($sessionID, "expiration");
		$in_seconds = $current_time - $session_time;
		$in_minutes = $in_seconds / 60;

			//check if the session has expired
			if ($in_minutes > SESSION_EXPIRATION_TIME)
			{
			//session has expired, send api response
			$this->sessionInterface->endSession($sessionID);
			
			$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Session expired");
			$this->responseClass->sessionResponse("Invalid", "Logged Out");
			$this->responseClass->apiError("Session expired");
			$this->responseClass->apiResponse("Session expired");
			return false;
			}
			else
			{
			//validate session			
			$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Sessions matched");
			$tokensArray = $this->sessionInterface->pullValueSession($sessionID, "tokens");
			
				if (is_array($tokensArray))
				{
				reset($tokensArray);
				$token = key($tokensArray);
					
				$this->responseClass->sessionResponse($token, "Logged In");
				$this->responseClass->apiResponse("Session Active");
				return true;
				}
				else
				{
				$this->responseClass->sessionResponse("Invalid", "Logged Out");
				$this->responseClass->apiResponse("No Active Session");	
				return false;
				}
			}		
		}
		else
		{
		$this->errorLogging->logWarning(__CLASS__, __METHOD__, "Session did not exist");
		$this->responseClass->sessionResponse("Invalid", "Logged Out");
		$this->responseClass->apiResponse("Session Invalid");
		return false;
		}		
	}
	
	private function ExtendSession($token)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	//set default value for $sessionID
	$sessionID = false;
	
		//lets get an existing sessionID from the token
		if($token !== false)
		{
		$sessionID = $this->sessionInterface->sessionIDByToken($token);
		}
	
		//if it is not false, set the new value
		if($sessionID)
		{
		$currentTime = date("U");
		return $this->sessionInterface->addValueToSession($sessionID, "expiration", $currentTime);
		}
		
	//if we made it here, there was a problem
	return false;
	}
	
	private function apiRateLimit($sessionID)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");	
	//get the current time
	$currentTime = date("U");	
		
	//get the last time the API call timer was reset
	$timerApiCalls = $this->sessionInterface->pullValueSession($sessionID, "timerApiCalls");

		if(($currentTime - $timerApiCalls) >= 60)
		{//It has been at least 60 seconds since the timer has been reset, reset the timer
		$numberApiCalls = 0;
		$this->sessionInterface->addValueToSession($sessionID, "numberApiCalls", 0);
		$this->sessionInterface->addValueToSession($sessionID, "timerApiCalls", $currentTime);
		}
		else
		{//it has not been 60 seconds since the last reset, lets look at how many calls have been made
		$numberApiCalls = $this->sessionInterface->pullValueSession($sessionID, "numberApiCalls");			
		}
	 
		if($numberApiCalls >= API_RATE_LIMIT)
		{	
		return true;	
		}
		else
		{
		$numberApiCalls++;	
		$this->sessionInterface->addValueToSession($sessionID, "numberApiCalls", $numberApiCalls);
		return false;
		}	
	}
	
	private function GenerateToken($time = false)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$alphabet_array = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
			
	$countto = 16;
	$count = 0;
	$token = "";
	
		while ($count <> $countto)
		{
		$selection_rand = mt_rand(0, 3);
			switch($selection_rand)
			{
				case 3:
					$selected_rand = mt_rand(0, 9);
					break;
				
				case 2:
					$alpha_rand = mt_rand(0, 25);
					$lower_rand = $alphabet_array["$alpha_rand"];
					$selected_rand = ucfirst($lower_rand);
					break;
												
				default:
					$alpha_rand = mt_rand(0, 25);
					$selected_rand = $alphabet_array["$alpha_rand"];
					break;			
			}
		
		$token = $token . $selected_rand;
		$count++;
		}
			
		if ($time === true)
		{
		$time = date("U");

		$token = $token . "_" . $time;			
		}
		
	return $token;
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
<?php
class SessionPHP implements SessionInterfaces
{
	private $errorLogging;
	
	public function __construct()
	{
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'loggingINC.php');
	$this->errorLogging = LoggingClass::getInstance();
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	}
	
	public function startSession()
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
		if(session_id() == '' || !isset($_SESSION))
		{
		session_start();
		return session_id();
		}
		else
		{
		session_unset();
		session_destroy();
		session_start();
		return session_id();
		}
	}
	
	public function endSession($sessionID)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	$this->removeSessionIDToken($sessionID);
	$this->addValueToSession($sessionID, "valid", 0);
	}
	
	public function forceEndSession()
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	$this->removeSessionIDToken($sessionID);
	$this->removeSessionIDCookie($sessionID);

	session_start();
	session_unset();
	session_destroy();
	}
	
	public function dumpValuesSession($sessionID)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$this->startPHPSession();
	
		if ($this->validateSessionID($sessionID))
		{
		$output = $_SESSION;
		}
		else
		{
		$output = false;	
		}
		
	$this->endPHPSession();
	return $output;
	}
	
	public function sessionIDByToken($token)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$this->startPHPSession();
	
		if (array_key_exists("tokens", $_SESSION))
		{
		$tokenArray = $_SESSION["tokens"];
		}
		else
		{
		$tokenArray = array();	
		}
	
		if(array_key_exists($token, $tokenArray))
		{
		$output = session_id();
		}
		else
		{
		$output = false;	
		}
		
	$this->endPHPSession();
	return $output;		
	}
	
	public function sessionIDByCookie($cookie)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$this->startPHPSession();
	
		if (array_key_exists("cookies", $_SESSION))
		{
		$cookieArray = $_SESSION["cookies"];
		}
		else
		{
		$cookieArray = array();	
		}
		
		if(array_key_exists($cookie, $cookieArray))
		{
		$output = session_id();
		}
		else
		{
		$output = false;	
		}
		
	$this->endPHPSession();
	return $output;
	}
	
	public function addSessionIDByToken($sessionID, $token)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	return $this->addElementToSession($sessionID, "tokens", $token, $sessionID);
	}
	
	public function addSessionIDByCookie($sessionID, $cookie)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	return $this->addElementToSession($sessionID, "cookies", $cookie, $sessionID);
	}
	
	public function addValueToSession($sessionID, $key, $value)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$this->startPHPSession();
	
		if ($this->validateSessionID($sessionID))
		{
		$_SESSION["$key"] = $value;
		$output = true;
		}
		else
		{
		$output = false;	
		}
		
	$this->endPHPSession();
	return $output;
	}
	
	public function removeValueFromSession($sessionID, $key)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$this->startPHPSession();
	
		if ($this->validateSessionID($sessionID))
		{
		unset($_SESSION["$key"]);
		$output = true;
		}
		else
		{
		$output = false;	
		}
		
	$this->endPHPSession();
	return $output;
	}
	
	public function pullValueSession($sessionID, $key)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$this->startPHPSession();
	
		if ($this->validateSessionID($sessionID))
		{
		@$output = $_SESSION["$key"];
		}
		else
		{
		$output = false;	
		}
		
	$this->endPHPSession();
	return $output;
	}

	public function addElementToSession($sessionID, $element, $key, $value)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$this->startPHPSession();
	
		if ($this->validateSessionID($sessionID))
		{
		$_SESSION["$element"]["$key"] = $value;
		$output = true;
		}
		else
		{
		$output = false;	
		}
		
	$this->endPHPSession();
	return $output;
	}
	
	public function removeElementFromSession($sessionID, $element, $key)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$this->startPHPSession();
	
		if ($this->validateSessionID($sessionID))
		{
		unset($_SESSION["$element"]["$key"]);
		$output = true;
		}
		else
		{
		$output = false;	
		}
		
	$this->endPHPSession();
	return $output;
	}
	
	public function pullElementSession($sessionID, $element, $key)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$this->startPHPSession();
	
		if ($this->validateSessionID($sessionID))
		{
		$output = $_SESSION["$element"]["$key"];
		}
		else
		{
		$output = false;	
		}
		
	$this->endPHPSession();
	return $output;
	}
	
	private function removeSessionIDToken($sessionID)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$this->removeValueFromSession($sessionID, "tokens");
	return true;
	}
	
	private function removeSessionIDCookie($sessionID)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	$this->removeValueFromSession($sessionID, "cookies");
	return true;
	}
	
	private function startPHPSession()
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	session_start();
	
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, $_SESSION);
	}

	private function endPHPSession()
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		
	session_commit();	
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, $_SESSION);
	}
	
	private function validateSessionID($sessionID)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
		if($sessionID === session_id())
		{
		return true;	
		}
		else
		{
		return false;
		}
	}
}

/*
class SessionDatabase implements SessionInterfaces
{	
}

class SessionRedis implements SessionInterfaces
{	
}
*/
?>
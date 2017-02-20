<?php
/* 
LOGGING VARIABLES
1 = Info logging Only *
2 = Warning Logging Only *
3 = Info / Warning Logging Only **
4 = Error Logging Only *
5 = Info / Error Logging Only **
6 = Warning / Error Logging Only **
7 = Info / Warning / Error Logging ***
*/ 

//ADD MILISECOND TIMING
//ADD MEMORY USAGE


class LoggingClass
{
	private $masterlogfile;
	private $masterlogging;
	
	private $logErrorArray;
	private $logWarningArray;
	private $logInfoArray;
	
	private $loggingInterface;
	private $loggingID;
	
	protected function __construct()
	{
	$loggingMethod = LOGGING_METHOD;
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR . 'loggingInterface.php');
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR . 'Implementors' . DIRECTORY_SEPARATOR . $loggingMethod .'Implementor.php');
	
	$this->loggingInterface = new $loggingMethod;
	
	$this->masterlogging = MASTER_LOGGING;
	$this->masterlogfile = LOG_DIRECTORY . "masterlog.log";
	
	$this->logErrorArray = array("4" => true, "5" => true, "6" => true, "7" => true);
	$this->logWarningArray = array("2" => true, "3" => true, "5" => true, "7" => true);
	$this->logInfoArray = array("1" => true, "3" => true, "5" => true, "7" => true);
	
	$this->loggingID = $this->generateLoggingID();
	}
	
	public static function getInstance()
    {
    //this creates one instances of the class if it doesn't exist, and if it does it returns that one instance
	//set an instance ID for this class, to differentiate separate concurrent processes
    static $instance = null;

        if (null === $instance) 
        {
        $instance = new static();
        }

	return $instance;
    }

	public function logError($classCalled, $methodCalled, $message)
	{
	$type = "ERROR";
	$message = $this->normalizeMessage($message);
		if (array_key_exists($this->loggingLevel($classCalled), $this->logErrorArray))
		{
		$this->loggingInterface->logData($this->loggingID, $classCalled, $methodCalled, $message, $type);	
		}
	}
	
	public function logWarning($classCalled, $methodCalled, $message)
	{
	$type = "WARNING";
	$message = $this->normalizeMessage($message);
		if (array_key_exists($this->loggingLevel($classCalled), $this->logWarningArray))
		{
		$this->loggingInterface->logData($this->loggingID, $classCalled, $methodCalled, $message, $type);	
		}
	}
	
	public function logInfo($classCalled, $methodCalled, $message)
	{
	$type = "INFO";	
	$message = $this->normalizeMessage($message);
		if (array_key_exists($this->loggingLevel($classCalled), $this->logInfoArray))
		{
		$this->loggingInterface->logData($this->loggingID, $classCalled, $methodCalled, $message, $type);	
		}
	}
	
	private function loggingLevel($classCalled)
	{
		if ($this->masterlogging < 1)
		{
		$classCalled = strtoupper($classCalled);
		$defineName = $classCalled . "_LOGGING";
			
		defined("$defineName") or define("$defineName", 0);
		
		return constant($defineName);
		}
		else
		{
		return $this->masterlogging;	
		}			
	}
	
	private function normalizeMessage($message)
	{
		if (is_array($message))
		{
		$message = json_encode($message);
		}		
	return $message;		
	}
	
	private function generateLoggingID()
	{
	$alphabet_array = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
			
	$countto = 8;
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
			
	return $token;
	}
	
	private function __clone()
    {
	}

    private function __wakeup()
    {
	}
}
?>
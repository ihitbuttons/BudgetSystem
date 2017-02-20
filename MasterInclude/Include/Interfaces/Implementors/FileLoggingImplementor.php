<?php
class FileLogging implements LoggingInterfaces
{
	private $logFileArray;
	
	public function __construct()
	{		
	}
	
    public function logData($loggingID, $classCalled, $methodCalled, $message, $type)
	{
	//die("$loggingID, $classCalled, $methodCalled, $message, $type");
	$logFile = $this->getLogFile($classCalled);
	$timestamp = date("U");

	file_put_contents($logFile, $timestamp . "/n $loggingID /n Type: $type /n Class: $classCalled /n Method: $methodCalled /n Message: $message /n/n", FILE_APPEND);		
	}

	public function clearLogs($clearArray)
	{
		if(is_array($clearArray))
		{
		//go through each specified log and clear it	
		}
		else
		{
			if($clearArray === "all")
			{
				foreach($this->logFileArray as $logFileKey => $logFileValue)
				{
				file_put_contents($logFileValue, '');
				}
			}
			else
			{
			file_put_contents($clearArray, '');	
			}
		}
	}
	
	private function getLogFile($classCalled)
	{
		if(MASTER_LOGGING > 0)
		{
		$logFile = LOG_DIRECTORY . "masterlog.log";		
		}
		else
		{
		$logFileName = $classCalled . ".log";
		$logFile = LOG_DIRECTORY . $logFileName;
		}	
	return $logFile;
	}
}
?>
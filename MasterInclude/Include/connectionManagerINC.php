<?php
/*
Connection Manager 

This class just stores and serves the connection
the connections are actually created by the relevant interfaces
*/

//include required interfaces
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR .  'Include' . DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR . 'connectionManagerInterface.php');

//This is a singleton instance
class ConnectionManager
{
	private $connectionArray;
	private $errorLogging;
	
	protected function __construct()
    {
	$this->errorLogging = LoggingClass::getInstance();
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");	
    //this prevents duplicate copies of the class from being created
    }

    public static function getInstance()
    {
	//$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
    //this creates one instances of the class if it doesn't exist, and if it does it returns that one instance
    static $instance = null;

        if (null === $instance) 
        {
        $instance = new static();
        }

	return $instance;
    }

    public function getConnection($connectionProperties)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	//determine what interface class should be used
	$connection = $connectionProperties["service"];
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR .  'Include' . DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR . 'Implementors' . DIRECTORY_SEPARATOR . $connection . 'Implementor.php');
	
			if (class_exists("$connection"))
			{	
				if(isset($this->connectionArray))
				{
					if(array_key_exists("$connection", $this->connectionArray))
					{
					$connectionHandler = $this->connectionArray["$connection"];	
					}
					else
					{
					$this->connectionArray["$connection"] = new $connection;
					$connectionHandler = $this->connectionArray["$connection"];
					}			
				}
				else
				{
				$this->connectionArray["$connection"] = new $connection;
				$connectionHandler = $this->connectionArray["$connection"];
				}
			}
			else
			{
			//This is not a valid service				
			}

	return $connectionHandler->createConnection($connectionProperties);		
	}

    private function __clone()
    {
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
    }

    private function __wakeup()
    {
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
    }
}
?>
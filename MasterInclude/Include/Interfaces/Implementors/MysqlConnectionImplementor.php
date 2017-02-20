<?php
class MysqlConnection implements ConnectionInterface
{
	private $connectionArray;
	private $errorLogging;
	
	public function __construct()
	{
	$this->errorLogging = LoggingClass::getInstance();
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	}
	
	public function createConnection($connectionInfo)
	{
	/* We are going to get a connection if it exists and test it. If there isn't a connection or the test fails, we are going to create a new one and test it */
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$type = $connectionInfo["type"];
	$database = $connectionInfo["database"];
	
	$connectAttempt = 0;
	$maxNumberOfAttempts = 3;
	
		while ($connectAttempt < $maxNumberOfAttempts)
		{	
			//check if this type/order combo exists
			if($connection = $this->getConnection($database, $type))	
			{
				//test the connection (ping), return if successful
				if($this->testConnection($connection))
				{
				//looks like it is responding, let send it back
				return $connection;	
				}
			
			//it didn't connect! Lets unset the connection and try again
			$this->closeConnection($connectionInfo);				
			$connectAttempt++;	
			}
			else
			{
			//something went wrong!
			return false;	
			}
		}
		
	//At this point we couldn't connect. We should return some error	
	return false;
	}
	
	public function closeConnection($connectionInfo)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$type = $connectionInfo["type"];
	$database = $connectionInfo["database"];
	
	//find the resource specified and close it 
	unset($this->connectionArray["$datase$type"]);
	return true;
	}
	
	private function getConnection($database, $type)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");

		if(isset($this->connectionArray))
		{
			if(!array_key_exists("$database$type", $this->connectionArray))
			{
			//we do not have a connection, we should create one and store it
			$this->errorLogging->logInfo(__CLASS__, __METHOD__, "ACTUAL NEW CONNECTION.");
			$this->createNewConnection($database,$type);
			}
		}
		else
		{
		//the connection array has not been created. Create it and a new connection!
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "ACTUAL NEW CONNECTION.");
		$this->createNewConnection($database,$type);
		}

	return $this->connectionArray["$database$type"];		
	}
	
	private function createNewConnection($database, $type)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		switch($type)
		{
			case "READONLY":
				$this->connectionArray["$database$type"] = new mysqli(DATABASE_HOST, DATABASE_USER_READONLY, DATABASE_PASS_READONLY, $database, DATABASE_PORT);
				break;
			
			case "MODIFY":
				$this->connectionArray["$database$type"] = new mysqli(DATABASE_HOST, DATABASE_USER_MODIFY, DATABASE_PASS_MODIFY, $database, DATABASE_PORT);
				break;
				
			case "DELETE":
				$this->connectionArray["$database$type"] = new mysqli(DATABASE_HOST, DATABASE_USER_DELETE, DATABASE_PASS_DELETE, $database, DATABASE_PORT);
				break;
		}
	}
	
	private function testConnection($connection)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
		//test the connection
		if ($connection->ping()) 
		{
		return $connection;
		}
		else 
		{
		return false;
		}		
	}
}
?>
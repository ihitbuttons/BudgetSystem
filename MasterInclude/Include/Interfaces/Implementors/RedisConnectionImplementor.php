<?php
class RedisConnection implements ConnectionInterface
{
	private $connectionArray;
	private $tableToKeyValueArray;
	private $errorLogging;
	
	public function __construct()
	{
	$this->errorLogging = LoggingClass::getInstance();
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	$this->tableToKeyValueArray = array(
		"ACCOUNTS-USER_GROUP-!!user_group" => array(
			array(
			"host" => "127.0.0.1",
			"port" => "6379"),
			array(
			"host" => "127.0.0.1",
			"port" => "6379")
			)
		);	
	}
	
	public function createConnection($connectionInfo)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	//$connectionInfo = array("service" => "RedisConnection", "cacheKey" => $cacheKey, "hashable" => $hashable);
	$cacheKey  = $connectionInfo["cacheKey"];
	$hasable = $connectionInfo["hashable"];
	
	//we are going to attempt to find a connection, test it, and if it fails we are going to try again (x number of times)
	$finished = false;
	$order = 0;
	$connectAttempt = 0;
	$numberOfAttempts = 3;
	
		while ($finished === false)
		{	
			//check if this table/type/order combo exists
			if($connection = $this->getConnection($cacheKey, $hasable, $order))		
			{
				//test the connection (ping), return if successful
				if($this->testConnection($connection))
				{
				//looks like it is responding, let send it back
				return $connection;	
				}
			
				//it didn't connect. Lets try that again $numberOfAttempts times
				if($connectAttempt < $numberOfAttempts)
				{
				$connectAttempt++;	
				}
				else
				{
				//try again with a different connection if there is one
				$order++;
				}
			}
			else
			{
			$finished = true;	
			}
		}
	//At this point we couldn't connect. We should return some error	
	}
	
	public function closeConnection($connectionInfo)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	//find the resource specified
	//close the connection
	}
	
	private function getConnection($cacheKey, $hasable, $order)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
		//check the table for the database information
		if(array_key_exists($order, $this->tableToKeyValueArray["$cacheKey"]))
		{
		$selectedServer = $this->tableToKeyValueArray["$cacheKey"]["$order"]["host"] . ":" . $this->tableToKeyValueArray["$cacheKey"]["$order"]["port"];	
		}
		else
		{
		return false;	
		}
		
		if(isset($this->connectionArray))
		{
			if(array_key_exists("$selectedServer$hasable", $this->connectionArray))
			{
			//we have already have a connection, return it
			$connection = $this->connectionArray["$selectedServer$hasable"];	
			}
			else
			{
			//we do not have a connection, we should create one and store it
			$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "ACTUAL NEW REDIS CONNECTION.");
			
			require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Predis' . DIRECTORY_SEPARATOR . 'autoload.php');
			Predis\Autoloader::register();
			
			$this->connectionArray["$selectedServer$hasable"] = new Predis\Client("tcp://" . $this->tableToKeyValueArray["$cacheKey"]["$order"]["host"] . ":" . $this->tableToKeyValueArray["$cacheKey"]["$order"]["port"]);
			$connection = $this->connectionArray["$selectedServer$hasable"];
			}
		}
		else
		{
		//the connection array has not been created. Create it and a new connection!
		$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "ACTUAL NEW REDIS CONNECTION.");
		
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Predis' . DIRECTORY_SEPARATOR . 'autoload.php');
		Predis\Autoloader::register();
		
		$this->connectionArray["$selectedServer$hasable"] = new Predis\Client("tcp://" . $this->tableToKeyValueArray["$cacheKey"]["$order"]["host"] . ":" . $this->tableToKeyValueArray["$cacheKey"]["$order"]["port"]);
		$connection = $this->connectionArray["$selectedServer$hasable"];
		}

	return $connection;		
	}
	
	private function testConnection($connection)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	//test the connection
	return $connection;			
	}
}
?>
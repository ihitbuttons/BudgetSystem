<?php

/*
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Predis' . DIRECTORY_SEPARATOR . 'autoload.php');
Predis\Autoloader::register();

$client = new Predis\Client();
$client->set('2015-02-01', '300.00');
$value = $client->get('2015-02-01');

echo $value;

?>
*/

class RedissClasss
{	

	private $redisConnection;
	private $responseClass;
	private $errorLogging;

	public function RedissClasss()
	{
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'master.inc.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'loggingINC.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'responseINC.php');
		
		$this->responseClass = ResponseClass::getInstance();
		$this->errorLogging = LoggingClass::getInstance();
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");	
	}
	
	function __destruct()
	{
	$this->destroyConnection();	
	}
	
	public function renewRedisKey($redisKey, $redisExpiration=3600)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
		if (!isset($this->redisConnection))
		{
		$this->createConnection();
		}
		
		if($redisValue = $this->getRedisValue($redisKey))
		{		
			if($keySet = $this->redisConnection->set($redisKey, $redisValue))
			{
				if($expirationSet = $this->redisConnection->expire($redisKey, $redisExpiration))
				{
				return true;
				}
				else
				{
				return false;	
				}
			}
			else
			{
			return false;
			}
		}
		else
		{
		return false;	
		}		
	}
	
	public function setRedisValue($redisKey, $redisValue, $redisExpiration=3600)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
		if (!isset($this->redisConnection))
		{
		$this->createConnection();
		}

		if($keySet = $this->redisConnection->set($redisKey, $redisValue))
		{
			if($expirationSet = $this->redisConnection->expire($redisKey, $redisExpiration))
			{
			return true;
			}
			else
			{
			return false;	
			}
		}
		else
		{
		return false;
		}
	}
	
	public function getRedisValue($redisKey)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
		if (!isset($this->redisConnection))
		{
		$this->createConnection();
		}
	
		if($results = $this->redisConnection->get($redisKey))
		{
		return $results;	
		}
		else
		{
		return false;
		}
	}
	
	private function createConnection()
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Predis' . DIRECTORY_SEPARATOR . 'autoload.php');
	Predis\Autoloader::register();
	$this->redisConnection = new Predis\Client();		
	}
	
	private function destroyConnection()
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
		if (!isset($this->redisConnection))
		{
		unset($this->redisConnection);
		}		
	}

}

?>
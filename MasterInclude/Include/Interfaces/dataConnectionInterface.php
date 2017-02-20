<?php
interface DataInterface
{
	//This adds data to the data set
    public function dataAdd($properties, $values, $connection);
	
	//This is used for any sort of "search", "find", "select"
	public function dataFind($properties, $values, $connection);
	
	//This modifies data in the data set
	public function dataModify($properties, $values, $connection);
	
	//This removes data from the data set
	public function dataDelete($properties, $values, $connection);
}

class RedisService implements DataInterface
{
	private $connectionManager;
	
	private $errorLogging;
	
	public function __construct()
	{
	//lets get the instance of the connection manager (and create it if it doesn't exist)
	$this->errorLogging = LoggingClass::getInstance();
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	$this->connectionManager = ConnectionManager::getInstance();
	}
	
    public function dataAdd($properties, $values, $connection)
	{
	//$client->set('2015-02-01', '300.00');
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	$cacheKey = $properties["cacheKey"];
	$hashable = $properties["hashable"];
	$valueArray = $properties["valueArray"];
	
	$connectionArray = array("service" => "RedisConnection", "cacheKey" => "$cacheKey", "hashable" => $hashable);
	$redisConnection = $this->connectionManager->getConnection($connectionArray);
	
		foreach ($values as $valueKey => $valueValue)
		{
		$variable_string = "!!$valueKey";
		$cacheKey = str_replace("$variable_string", "$valueValue", $cacheKey);
			if(is_array($valueArray))
			{
				if (array_key_exists($valueKey, $valueArray))
				{
				$valueArray[$valueKey] = str_replace("$variable_string", "$valueValue", $valueArray[$valueKey]);	
				}
			}
		}
	
	//convert values into json array
	$values = json_encode($values);
	
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Cache Key: $cacheKey, Values: $values");
	
		if($redisConnection->set($cacheKey, $values))
		{
		return true;
		}
		else
		{
		return false;	
		}
	}
	
	public function dataFind($properties, $values, $connection)
	{
	//$value = $client->get('2015-02-01');
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	$cacheKey = $properties["cacheKey"];
	$hashable = $properties["hashable"];
	
	$connectionArray = array("service" => "RedisConnection", "cacheKey" => "$cacheKey", "hashable" => $hashable);
	$redisConnection = $this->connectionManager->getConnection($connectionArray);
	
		foreach ($values as $valueKey => $valueValue)
		{
		$variable_string = "!!$valueKey";
		$cacheKey = str_replace("$variable_string", "$valueValue", $cacheKey);
		}
	
	//convert values into json array
	$values = json_encode($values);
	
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Cache Key: $cacheKey, Values: $values");
	
	$returned = $redisConnection->get($cacheKey);
	return $returned;
	}
	
	public function dataModify($properties, $values, $connection)
	{
	//$value = $client->get('2015-02-01');	
	//$client->set('2015-02-01', '300.00');
	}
	
	public function dataDelete($properties, $values, $connection)
	{
	//$client->del($key)	
	}
}

class MysqlService implements DataInterface
{
	private $connectionManager;
	private $offline = false;
	
	private $errorLogging;
	
	public function __construct()
	{
	//lets get the instance of the connection manager (and create it if it doesn't exist)
	$this->errorLogging = LoggingClass::getInstance();
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	$this->connectionManager = ConnectionManager::getInstance();
	}
	
    public function dataAdd($properties, $values, $connection)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
		if ($this->offline === false)
		{
		return $this->dbInsert($insert, $table);
		}
		else
		{
		return $this->offlineQuery($insert, $table, true);
		}
	}
	
	public function dataFind($properties, $values, $connection)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
		//test to see if the query is provided or if it needs to be assembled
		if(is_array($properties["query"]))
		{
		$query = $this->constructSelect($properties["query"]);				
		}
		else
		{
		$query = $properties["query"];	
		}
		
		foreach ($values as $valueKey => $valueValue)
		{
		$variable_string = "!!$valueKey";
		$query = str_replace("$variable_string", "$valueValue", $query);			
		}
		
		if ($this->offline === false)
		{
		return $this->dbSelect($query, $connection);
		}
		else
		{
		return $this->offlineQuery($query, $connection, false);
		}
	}
	
	public function dataModify($properties, $values, $connection)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
		
	}
	
	public function dataDelete($properties, $values, $connection)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
		
	}

	/************** PUBLIC CONTRUCT FUNCTIONS **************/
	
	private function constructSelect($select_array)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	$table = $select_array["table"];
	$where = $select_array["where"];
	$endingQuery = $select_array["endingQuery"];
		
		if (is_array($select_array["columns"]))
		{
		$columns = "";
			foreach ($select_array["columns"] as $colKey => $colValue)
			{
				if(substr($colValue, 0, 1) === "{")
				{
				$colValue = str_replace("{", "", $colValue);
				$colValue = str_replace("}", "", $colValue);
				$columns = $columns . ", " . $colValue . " AS '" . $select_array["returns"]["$colKey"] . "' ";
				}
				else
				{
				$columns = $columns . ", `" . $colValue . "` AS '" . $select_array["returns"]["$colKey"] . "' "; 
				}
			}
		$columns = ltrim($columns, ",");
		}
		else
		{
		$columns = "`" . $select_array["columns"] . "`";
		}
		
		if (is_array($select_array["conditions"]))
		{
		$conditions = "";
			foreach ($select_array["conditions"] as $conKey => $conValue)
			{
			$column = $conValue["column"];
			$operator = $conValue["operator"];
			$value = $conValue["value"];
			$concat = $conValue["concat"];
			
				if (!is_numeric($value) && substr($value, 0, 1) <> "(" && substr($value, 0, 1) <> "{")
				{
				$valueCorrected = "'" . $value . "'";
				}
				elseif(substr($value, 0, 1) === "{")
				{
				$valueCorrected = str_replace("{", "", $value);
				$valueCorrected = str_replace("}", "", $valueCorrected);
				}
				else
				{
				$valueCorrected = $value;
				}
				
				if ($concat === "")
				{
				$concatCorrected = $concat;
				}
				else
				{
				$concatCorrected = " $concat ";
				}
				
			$conditions = $conditions .  $concatCorrected . "`$column` " . $operator . " " . $valueCorrected;
			}
		$conditions = $conditions . " " . $endingQuery;
		$conditions = ltrim($conditions, ",");
		}
		else
		{
		$conditions = "`" . $select_array["conditions"] . "`";
		}
				
	$select = "SELECT " . $columns . " FROM `" . $table . "` " . $where . " " . $conditions;
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Select :: $select");	
	
	return $select;
	}
	
	private function constructInsert($insert_array)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	$table = $insert_array["table"];
	$setcommand = "SET ";
	
		foreach ($insert_array["columns"] as $colKey => $colValue)
		{
		$value = $insert_array["values"]["$colKey"];
			if (!is_numeric($value) && substr($value, 0, 1) <> "(" && substr($value, 0, 1) <> "{")
			{
			$valueCorrected = "'" . $value . "'";
			}
			elseif(substr($value, 0, 1) === "{")
			{
			$valueCorrected = str_replace("{", "", $value);
			$valueCorrected = str_replace("}", "", $valueCorrected);
			}
			else
			{
			$valueCorrected = $value;
			}
		$setcommand = $setcommand . " `" . $colValue ."` = " . $valueCorrected . ", ";
		}
		
	$setcommand = trim($setcommand, ", ");
	//need a replace into / insert into toggle
	$insert = "INSERT INTO `" . $table ."` " . $setcommand;
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Insert :: $insert");
	return $insert;
	}
	
	private function constructUpdate($update_array)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
		$table = $update_array["table"];

		$setcommand = "SET ";
		$where = $update_array["where"];
		$endingQuery = $update_array["endingQuery"];
	
			foreach ($update_array["columns"] as $colKey => $colValue)
			{
			$value = $update_array["values"]["$colKey"];
				if (!is_numeric($value) && substr($value, 0, 1) <> "(" && substr($value, 0, 1) <> "{")
				{
				$valueCorrected = "'" . $value . "'";
				}
				elseif(substr($value, 0, 1) === "{")
				{
				$valueCorrected = str_replace("{", "", $value);
				$valueCorrected = str_replace("}", "", $valueCorrected);
				}
				else
				{
				$valueCorrected = $value;
				}
			$setcommand = $setcommand . " `" . $colValue ."` = " . $valueCorrected . ", ";
			}
		$setcommand = trim($setcommand, ", ");
			
		if (is_array($update_array["conditions"]))
		{
		$conditions = "";
			foreach ($update_array["conditions"] as $conKey => $conValue)
			{
			$column = $conValue["column"];
			$operator = $conValue["operator"];
			$value = $conValue["value"];
			$concat = $conValue["concat"];
			
				if (!is_numeric($value) && substr($value, 0, 1) <> "(" && substr($value, 0, 1) <> "{")
				{
				$valueCorrected = "'" . $value . "'";
				}
				elseif(substr($value, 0, 1) === "{")
				{
				$valueCorrected = str_replace("{", "", $value);
				$valueCorrected = str_replace("}", "", $valueCorrected);
				}
				else
				{
				$valueCorrected = $value;
				}
				
				if ($concat === "")
				{
				$concatCorrected = $concat;
				}
				else
				{
				$concatCorrected = " $concat ";
				}
				
			$conditions = $conditions .  $concatCorrected . "`$column` " . $operator . " " . $valueCorrected;
			}
		$conditions = $conditions . " " . $endingQuery;
		$conditions = ltrim($conditions, ",");
		}
		else
		{
		$conditions = "`" . $select_array["conditions"] . "`";
		}
		
		$update = "UPDATE `" . $table ."` " . $setcommand . " $where " . $conditions;
		$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "update :: $update");
		
		if ($this->offline === false)
		{
		return $this->dbUpdate($update, $table);
		}
		else
		{
		return $this->offlineQuery($update, $table, true);
		}
	}
	
	/************** PRIVATE SELECT / INSERT / UPDATE / DELETE **************/
	
	private function dbSelect($select, $table)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	$returnarray = "";
	
	//get connection
	$connectionArray = array("service" => "MysqlConnection", "type" => "read", "table" => "$table");
	$readConnection = $this->connectionManager->getConnection($connectionArray);
	
	//clean the select
	$clean_select = $this->CleanSelect($select);

		//run the query
		if($returned = $readConnection->query($clean_select))
		{
		$returned->data_seek(0);
			while($row = $returned->fetch_assoc())
			{
			$returnarray[] = $row;
			}
		}
		else
		{
		$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Failed query. !! $clean_select");
		$returnarray = "false";
		}
	//This should be the response class	
	//$returnarray = $this->sanitizeClass->sanitizeValues($returnarray, true);
	return $returnarray;
	}
	
	private function dbInsert($insert, $table)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");

	$connectionArray = array("service" => "MysqlConnection", "type" => "write", "table" => "$table");
	$writeConnection = $this->connectionManager->getConnection($connectionArray);

	$returnarray = false;
	
	$clean_insert = $this->cleanInsert($insert);

		if($returned = $writeConnection->query($clean_insert))
		{
		//need primary key
		$get_primary_key = "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'";
			if($primary_key_result = $writeConnection->query($get_primary_key))
			{
			$primary_key_result->data_seek(0);
			$primary_key = $primary_key_result->fetch_assoc();
			$pk = $primary_key["Column_name"];
			}
			else
			{
			$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "show keys RETURNED FALSE");
			}
		
		$select_last_inserted = "SELECT MAX(`$pk`) AS 'pk' FROM `$table`;";

		if($last_inserted_row = $writeConnection->query($select_last_inserted))
			{
			$last_inserted_row->data_seek(0);
			$last_inserted = $last_inserted_row->fetch_assoc();
			$returnarray = $last_inserted["pk"];
			}
			else
			{
			$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "select max RETURNED FALSE");
			}
		}
		else
		{
		$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "query RETURNED FALSE !! $clean_insert");
		}
		
	return $returnarray;
	}
	
	private function dbUpdate($update)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	$connectionArray = array("service" => "MysqlConnection", "type" => "write", "table" => "$table");
	$writeConnection = $this->connectionManager->getConnection($connectionArray);
	
	$returnarray = "";
	
	$clean_update = $this->cleanUpdate($update);

		if($returned = $writeConnection->query($clean_update))
		{
		return true;
		}
		else
		{
		$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Failed query. !! $clean_update");
		return false;
		}
	}
	
	private function dbDelete($delete)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	$connectionArray = array("service" => "MysqlConnection", "type" => "write", "table" => "$table");
	$writeConnection = $this->connectionManager->getConnection($connectionArray);
	
	$returnarray = "";
	
	$clean_update = $this->cleanDelete($delete);

		if($returned = $writeConnection->query($clean_update))
		{
		return true;
		}
		else
		{
		$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Failed query. !! $clean_update");
		return false;
		}
	}
	
	/************** Cleaning Functions **************/
	private function cleanSelect($select)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	$cleaned_select = str_replace("/*", "&#47;&#42;", $select);
	$cleaned_select = str_replace("*/", "&#42;&#47;", $cleaned_select);
	$cleaned_select = str_replace("--", "&#45;&#45;", $cleaned_select);
	$cleaned_select = str_replace("----", "&#45;&#45;", $cleaned_select);
	
	return $cleaned_select;
	}
	
	private function cleanInsert($insert)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	$cleaned_insert = str_replace("/*", "&#47;&#42;", $insert);
	$cleaned_insert = str_replace("*/", "&#42;&#47;", $cleaned_insert);
	$cleaned_insert = str_replace("--", "&#45;&#45;", $cleaned_insert);
	$cleaned_insert = str_replace("----", "&#45;&#45;", $cleaned_insert);

	return $cleaned_insert;
	}
	
	private function cleanUpdate($update)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	$cleaned_update = str_replace("/*", "&#47;&#42;", $update);
	$cleaned_update = str_replace("*/", "&#42;&#47;", $cleaned_update);
	$cleaned_update = str_replace("--", "&#45;&#45;", $cleaned_update);
	$cleaned_update = str_replace("----", "&#45;&#45;", $cleaned_update);

	return $cleaned_update;
	}
	
	private function cleanDelete($delete)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	$cleaned_delete = str_replace("/*", "&#47;&#42;", $delete);
	$cleaned_delete = str_replace("*/", "&#42;&#47;", $cleaned_delete);
	$cleaned_delete = str_replace("--", "&#45;&#45;", $cleaned_delete);
	$cleaned_delete = str_replace("----", "&#45;&#45;", $cleaned_delete);

	return $cleaned_delete;
	}
}


?>
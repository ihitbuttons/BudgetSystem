<?php
	/*
	$select_array = array(
	"table" => 'keepermain', 
	"where" => 'WHERE', 
	"columns" => array(
	"idkeepermain",
	"title"),
	"conditions" => array(
		array(
		"column" => "userID",
		"operator" => "=",
		"value" => $userID,
		"concat" => "")
	),
	"endingQuery" => "ORDER BY `title` DESC"
	);
	
	$insert_array = array(
	"table" => 'accounts', 
	"columns" => array(
	"accountnumber",
	"accounttypepk",
	"accountname"),
	"values" => array(
	"$accountnumber",
	"$accounttypepk",
	"$accountname")
	);
	*/

class DatabaseClass
{	
	private $connectionManager;
	private $selectedDatabase;

	private $errorLogging;
	private $sanitizeClass;
	
	public function DatabaseClass($database)
	{
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'master.inc.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'loggingINC.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'sanitizeINC.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'connectionManagerINC.php');
		
		$this->errorLogging = LoggingClass::getInstance();
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		
		$this->sanitizeClass = SanitizeClass::getInstance();
		$this->connectionManager = ConnectionManager::getInstance();
		$this->selectedDatabase = $database;
	}

	/************** PUBLIC CONTRUCT FUNCTIONS **************/
	public function ConstructSelect($select_array)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
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
			$value = $this->sanitizeClass->sanitizeValues($conValue["value"], false, true);
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
		$conditions = $select_array["conditions"] . " " . $endingQuery;
		}
		
		$select = "SELECT " . $columns . " FROM `" . $table . "` " . $where . " " . $conditions;
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Select :: $select");

	return $this->dbSelect($select);
	}
	
	public function ConstructInsert($insert_array)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		$table = $insert_array["table"];

		$setcommand = "SET ";
			foreach ($insert_array["columns"] as $colKey => $colValue)
			{
			$value = $this->sanitizeClass->sanitizeValues($insert_array["values"]["$colKey"], false, true);
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
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Insert :: $insert");
		
	return $this->dbInsert($insert, $table);
	}
	
	public function ConstructUpdate($update_array)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		$table = $update_array["table"];

		$setcommand = "SET ";
		$where = $update_array["where"];
		$endingQuery = $update_array["endingQuery"];
	
			foreach ($update_array["columns"] as $colKey => $colValue)
			{
			$value = $this->sanitizeClass->sanitizeValues($update_array["values"]["$colKey"], false, true);
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
			//$value = $conValue["value"];
			$value = $this->sanitizeClass->sanitizeValues($conValue["value"], false, true);
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
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "update :: $update");
		
	return $this->dbUpdate($update, $table);
	}
/*
	public function runQuerySelect($query)
	{
	return $this->dbSelect($query);	
	}
	
	public function runQueryUpdate($query)
	{
	return $this->dbUpdate($query);	
	}
/*		
	/************** PRIVATE CONNECTION FUNCTIONS **************/
	private function createConnection($type)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$connectionProperties = array(
		"service" => "MysqlConnection",
		"database" => "$this->selectedDatabase",
		"type" => "$type"
	);
	
	return $this->connectionManager->getConnection($connectionProperties);
	}
	
	/************** PRIVATE SELECT / INSERT / UPDATE / DELETE **************/
	private function dbSelect($select)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	//ERROR HANDLING
	
	$connection = $this->createConnection("READONLY");
	
	$returnarray = "";
	
	$clean_select = $this->CleanSelect($select);

		if($returned = $connection->query($clean_select))
		{
		$returned->data_seek(0);
			while($row = $returned->fetch_assoc())
			{
			$returnarray[] = $row;
			}
		}
		else
		{
		$this->errorLogging->logError(__CLASS__, __METHOD__, "Failed query. !! $clean_select");
		$returnarray = "false";
		}
		
	$returnarray = $this->sanitizeClass->sanitizeValues($returnarray, true, true);
	return $returnarray;
	}
	
	private function dbInsert($insert, $table)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");

	$writeConnection = $this->createConnection("MODIFY");
	$readConnection = $this->createConnection("READONLY");
	
	$returnarray = false;
	
	$clean_insert = $this->CleanInsert($insert);

		if($returned = $writeConnection->query($clean_insert))
		{
		//THIS NEEDS TO BE A LOCKING TRANSACTION
		//need primary key
		$get_primary_key = "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'";
			if($primary_key_result = $readConnection->query($get_primary_key))
			{
			$primary_key_result->data_seek(0);
			$primary_key = $primary_key_result->fetch_assoc();
			$pk = $primary_key["Column_name"];
			}
			else
			{
			$this->errorLogging->logError(__CLASS__, __METHOD__, "show keys RETURNED FALSE");
			}
		
		$select_last_inserted = "SELECT MAX(`$pk`) AS 'pk' FROM `$table`;";
			if($last_inserted_row = $readConnection->query($select_last_inserted))
			{
			$last_inserted_row->data_seek(0);
			$last_inserted = $last_inserted_row->fetch_assoc();
			$returnarray = $last_inserted["pk"];
			}
			else
			{
			$this->errorLogging->logError(__CLASS__, __METHOD__, "select max RETURNED FALSE");
			}
		}
		else
		{
		$this->errorLogging->logError(__CLASS__, __METHOD__, "query RETURNED FALSE !! $clean_insert");
		}

	return $returnarray;
	}
	
	private function dbUpdate($update)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$connection = $this->createConnection("MODIFY");
	
	$returnarray = "";
	
	$clean_update = $this->CleanUpdate($update);

		if($returned = $connection->query($clean_update))
		{
		return true;
		}
		else
		{
		$this->errorLogging->logError(__CLASS__, __METHOD__, "Failed query. !! $clean_update");
		return false;
		}
	}
	
	/************** Cleaning Functions **************/
	private function CleanSelect($select)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	$cleaned_select = str_replace("/*", "&#47;&#42;", $select);
	$cleaned_select = str_replace("*/", "&#42;&#47;", $cleaned_select);
	$cleaned_select = str_replace("--", "&#45;&#45;", $cleaned_select);
	$cleaned_select = str_replace("----", "&#45;&#45;", $cleaned_select);
	
	return $cleaned_select;
	}
	
	private function CleanInsert($insert)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	$cleaned_insert = str_replace("/*", "&#47;&#42;", $insert);
	$cleaned_insert = str_replace("*/", "&#42;&#47;", $cleaned_insert);
	$cleaned_insert = str_replace("--", "&#45;&#45;", $cleaned_insert);
	$cleaned_insert = str_replace("----", "&#45;&#45;", $cleaned_insert);

	return $cleaned_insert;
	}
	
	private function CleanUpdate($update)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	$cleaned_update = str_replace("/*", "&#47;&#42;", $update);
	$cleaned_update = str_replace("*/", "&#42;&#47;", $cleaned_update);
	$cleaned_update = str_replace("--", "&#45;&#45;", $cleaned_update);
	$cleaned_update = str_replace("----", "&#45;&#45;", $cleaned_update);

	return $cleaned_update;
	}
}
?>

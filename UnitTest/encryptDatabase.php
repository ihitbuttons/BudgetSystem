<?php
die('disabled');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'master.inc.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'loggingINC.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'sanitizeINC.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'databaseINC.php');

$errorLogging = LoggingClass::getInstance();
$errorLogging->logInfo('TEST', 'TEST', "Called.");
		
$dbConnection = new DatabaseClass(SESSION_DATABASE);
		
$sanitizeClass = SanitizeClass::getInstance();

$table_array = array(
	"accounts",
	"accounttype",
	"balances",
	"categories",
	"paymentmethod",
	"purchasers",
	"relatedtransactions",
	"reoccurringtransactions",
	"transactionhistory",
	"transactions",
	"transactiontype",
	"usertable"
);

	foreach($table_array as $tableKey => $tableValue)
	{
	$get_primary_key = "SHOW KEYS FROM `$tableValue` WHERE Key_name = 'PRIMARY'";
	$primaryKeyArray = $dbConnection->runQuerySelect($get_primary_key);
		if (is_array($primaryKeyArray))
		{
		$pk = $primaryKeyArray["0"]["Column_name"];
		echo "$tableValue :: $pk<br/>";
		}
		else
		{
		echo "$tableValue returned no primary keys!!<br/><br/>";	
		}

	$select = "SELECT * FROM `$tableValue`";
	$returnedArray = $dbConnection->runQuerySelect($select);
		if (is_array($returnedArray))
		{
			foreach($returnedArray as $returnedKey => $returnedValue)
			{
			$update = "UPDATE `$tableValue` SET";
				foreach($returnedValue as $rrKey => $rrValue)
				{
					if ($rrKey !== "$pk")
					{
					$encrypted = $sanitizeClass->sanitizeValues($rrValue, false, true);
					$update = $update . " `$rrKey` = '$encrypted',";
					}
				}
			$update = rtrim($update, ",");
			$pkValue = $returnedValue["$pk"];
			$update = $update . " WHERE `$pk` = '$pkValue'";
				if(!$dbConnection->runQueryUpdate($update))
				{
				die($update);	
				}
			}		
		}
		else
		{
		echo "$tableValue returned no results!!<br/><br/>";	
		}
	}
	
echo "Process Complete";
?>
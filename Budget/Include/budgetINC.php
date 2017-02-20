<?php
class BudgetClass
{
	public $apiResponseJson;
	
	private $dbConnection;
	private $responseClass;
	private $errorLogging;
	private $dateClass;

	
	public function BudgetClass()
	{
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'responseINC.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'databaseINC.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'loggingINC.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Dates' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'datesINC.php');
		
		$this->responseClass = ResponseClass::getInstance();
		$this->dbConnection = new DatabaseClass(BUDGET_DATABASE);
		$this->dateClass = new ProceduralDates();
		$this->errorLogging = LoggingClass::getInstance();
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	}
	
	public function argumentList($action)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$masterListArray = array();
	
	$masterListArray["listPurchasers_array"] = ""; //*
	$masterListArray["listPaymentMethods_array"] = array("accountPK" => "int"); //*
	$masterListArray["listCategories_array"] = ""; //*
	$masterListArray["listAccountIds_array"] = ""; //*
	$masterListArray["listAccountTypeIds"] = array("acountTypePK" => "int"); //*
	$masterListArray["listAccounts_array"] = ""; //*
	$masterListArray["listAccountTypes_array"] = "";
	$masterListArray["accountDetails_array"] = array("accountPK" => "int");	//*
	$masterListArray["categoryDetails_array"] = array("categoryPK" => "int");
	$masterListArray["listAccountAssoc_array"] = ""; //*
	$masterListArray["listTransactionTypes_array"] = ""; //*
	$masterListArray["listTransactions_array"] = array("accountPK" => "int", "categoryPK" => "int", "posted" => "int", "active" => "int", "startDate" =>"date", "endDate" => "date");
	$masterListArray["transactionDetails_array"] = array("transactionPK" => "int");
	$masterListArray["addAccount_array"] = array("accountNumber" => "int", "accountTypePK" => "int", "accountName" => "string"); //*
	$masterListArray["addCategory_array"] = array("categoryName" => "string");
	
	
	$masterListArray["addTransaction_array"] = array("transactionTypePK" => "int", "amount" => "int", "purchaserPK" => "int", "paymentMethodPK" => "int", "originalDate" => "date", "fromAccountPK" => "int", "toAccountPK" => "int", "posted" => "int", "categoryPK" => "int", "description" => "string");
	$masterListArray["addReoccurringTransaciton_array"] = array("startDate" => "date", "endDate" => "date", "transactionTypePK" => "int", "amount" => "int", "purchaserPK" => "int", "paymentMethodPK" => "int", "fromAccountPK" => "int", "toAccountPK" => "int", "posted" => "int", "categoryPK" => "int", "description" => "string");
	
	$masterListArray["modifyTransaction_array"] = array("transactionPK" => "int", "amount" => "int", "purchaserPK" => "int", "paymentMethodPK" => "int", "modifiedDate" => "date", "fromAccountPK" => "int", "toAccountPK" => "int", "active" => "int", "posted" => "int", "postedDate" => "int", "categoryPK" => "int", "description" => "string");
	$masterListArray["modifyAccount_array"] = array("accountPK" => "int", "accountNumber" => "string", "accountTypePK" => "int", "accountName" => "accountName");
	$masterListArray["modifyCategory_array"] = array("categoryPK" => "int", "categoryName" => "string");
	
		//check if it is a discovery method
		if ($action === "argumentList")
		{
		return $masterListArray;
		}
		else
		{
		$action = $action . "_array";	
		}
	
		//See if the method is callable by the API
		if(array_key_exists($action, $masterListArray))
		{
		return $masterListArray["$action"];
		}
		else
		{
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Failed for find argument list for action $action.");
		return false;
		}
	}	
	
	//This method will allow you to obfuscate Method names, so the API action doesn't directly correlate to an actual method name
	public function obfuscateMethodNames($action)
	{
	return $action;	
	}
	
	/************** List Functions **************/
	public function listPurchasers() //*
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$userGroup = $_SESSION["usergroup"];

	$select_array = array(
	"table" => 'purchasers', 
	"where" => 'WHERE', 
	"columns" => array(
	"purchaser_pk",
	"purchaser_name",),
	"returns" => array(
	"purchaserPK",
	"purchaserName"),
	"conditions" => array(
		array(
		"column" => "user_group",
		"operator" => "=",
		"value" => "$userGroup",
		"concat" => ""),
	),
	"endingQuery" => ""
	);
	
	$returnedArray = $this->dbConnection->ConstructSelect($select_array); //errors handled by dbase class
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
		
	public function listPaymentMethods($accountPK) //*
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$userGroup = $_SESSION["usergroup"];
	
		if ($accountPK == 0)
		{
		$select_array = array(
		"table" => 'paymentmethod', 
		"where" => 'WHERE', 
		"columns" => array(
		"payment_method_pk",
		"payment_name"),
		"returns" => array(
		"paymentMethodPK",
		"methodName"),
		"conditions" => array(
			array(
			"column" => "user_group",
			"operator" => "=",
			"value" => "$userGroup",
			"concat" => ""),
			array(
			"column" => "user_group",
			"operator" => "=",
			"value" => "ALL",
			"concat" => "OR"),
		),
		"endingQuery" => ""
		);
		}
		else
		{
		$accountTypePK = $this->accountType($accountPK);
		
		$select_array = array(
		"table" => 'paymentmethod', 
		"where" => 'WHERE', 
		"columns" => array(
		"payment_method_pk",
		"payment_name"),
		"returns" => array(
		"paymentMethodPK",
		"methodName"),
		"conditions" => array(
			array(
			"column" => "account_type_pk",
			"operator" => "=",
			"value" => "$accountTypePK",
			"concat" => ""),
		),
		"endingQuery" => "AND (`user_group` = '$userGroup' OR `user_group` = 'ALL')"
		);
		}
	$returnedArray = $this->dbConnection->ConstructSelect($select_array); //errors handled by dbase class
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function listCategories() //*
	{
	//list account id's for specific types (1, 2, 3 or 4)
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$userGroup = $_SESSION["usergroup"];

	$select_array = array(
	"table" => 'categories', 
	"where" => 'WHERE', 
	"columns" => array(
	"category_pk",
	"category_name",),
	"returns" => array(
	"categoryPK",
	"categoryName"),
	"conditions" => array(
		array(
		"column" => "user_group",
		"operator" => "=",
		"value" => "$userGroup",
		"concat" => "")
	),
	"endingQuery" => ""
	);
	
	$returnedArray = $this->dbConnection->ConstructSelect($select_array); //errors handled by dbase class
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function listAccountIds() //*
	{
	//list account id's for specific types (1, 2, 3 or 4)
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$userGroup = $_SESSION["usergroup"];

	$select_array = array(
	"table" => 'accounts', 
	"where" => 'WHERE', 
	"columns" => array(
	"account_pk",
	"account_name",),
	"returns" => array(
	"accountPK",
	"accountName"),
	"conditions" => array(
		array(
		"column" => "user_group",
		"operator" => "=",
		"value" => "$userGroup",
		"concat" => "")
	),
	"endingQuery" => ""
	);
	
	$returnedArray = $this->dbConnection->ConstructSelect($select_array); //errors handled by dbase class
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function listAccountTypeIds($acountTypePK) //*
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$userGroup = $_SESSION["usergroup"];

	$select_array = array(
	"table" => 'accounts', 
	"where" => 'WHERE', 
	"columns" => array(
	"account_pk",
	"account_name",),
	"returns" => array(
	"accountPK",
	"accountName"),
	"conditions" => array(
		array(
		"column" => "account_type_pk",
		"operator" => "=",
		"value" => "$acountTypePK",
		"concat" => ""),
	),
	"endingQuery" => ""
	);
	
	$returnedArray = $this->dbConnection->ConstructSelect($select_array); //errors handled by dbase class
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function listAccounts() //*
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$userGroup = $_SESSION["usergroup"];
	
	$select_array = array(
	"table" => 'accounts', 
	"where" => 'WHERE', 
	"columns" => array(
	"account_number",
	"account_name"),
	"returns" => array(
	"Account Number",
	"Account Name"),
	"conditions" => array(
		array(
		"column" => "user_group",
		"operator" => "=",
		"value" => "$userGroup",
		"concat" => ""),
		array(
		"column" => "user_group",
		"operator" => "=",
		"value" => "$userGroup",
		"concat" => ""),
		array(
		"column" => "user_group",
		"operator" => "=",
		"value" => "ALL",
		"concat" => "OR")
	),
	"endingQuery" => ""
	);
	
	$returnedArray = $this->dbConnection->ConstructSelect($select_array);
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function listAccountTypes()
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$userGroup = $_SESSION["usergroup"];
	
	$select_array = array(
	"table" => 'accounttype', 
	"where" => 'WHERE', 
	"columns" => array(
	"account_type_pk",
	"type_name"),
	"returns" => array(
	"accountTypePK",
	"typeName"),
	"conditions" => array(
		array(
		"column" => "user_group",
		"operator" => "=",
		"value" => "$userGroup",
		"concat" => ""),
		array(
		"column" => "user_group",
		"operator" => "=",
		"value" => "ALL",
		"concat" => "OR"),
	),
	"endingQuery" => ""
	);
	
	$returnedArray = $this->dbConnection->ConstructSelect($select_array);
	$this->responseClass->apiResponse($returnedArray);
	return true;	
	}
	
	public function accountDetails($accountPK) //*
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$userGroup = $_SESSION["usergroup"];
	
	$select_array = array(
	"table" => 'accounts', 
	"where" => 'WHERE', 
	"columns" => array(
	"account_pk",
	"account_number",
	"account_type_pk",
	"account_name"),
	"returns" => array(
	"accountPK",
	"accountNumber",
	"accountTypePK",
	"accountName"),
	"conditions" => array(
		array(
		"column" => "user_group",
		"operator" => "=",
		"value" => "$userGroup",
		"concat" => ""),
		array(
		"column" => "account_pk",
		"operator" => "=",
		"value" => "$accountPK",
		"concat" => "AND"),
		
	),
	"endingQuery" => ""
	);
		
	$returnedArray = $this->dbConnection->ConstructSelect($select_array);
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function categoryDetails($categoryPK) //*
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$userGroup = $_SESSION["usergroup"];
	
	$select_array = array(
	"table" => 'categories', 
	"where" => 'WHERE', 
	"columns" => array(
	"category_name"),
	"returns" => array(
	"categoryName"),
	"conditions" => array(
		array(
		"column" => "user_group",
		"operator" => "=",
		"value" => "$userGroup",
		"concat" => ""),
		array(
		"column" => "category_pk",
		"operator" => "=",
		"value" => "$categoryPK",
		"concat" => "AND"),
		
	),
	"endingQuery" => ""
	);
		
	$returnedArray = $this->dbConnection->ConstructSelect($select_array);
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}

	public function listAccountAssoc() //*
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$userGroup = $_SESSION["usergroup"];
	
	$select_permissions_array = array(
	"table" => 'accounts', 
	"where" => 'WHERE', 
	"columns" => array(
	"account_pk",
	"account_name",
	"user_group"),
	"returns" => array(
	"Account PK",
	"Account Name",
	"Associated With"),
	"conditions" => array(
		array(
		"column" => "user_group",
		"operator" => "=",
		"value" => "$userGroup",
		"concat" => "")
	),
	"endingQuery" => ""
	);

	$returnedArray = $this->dbConnection->ConstructSelect($select_permissions_array);
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function listTransactionTypes() //*
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		
	$select_transactiontypes_array = array(
	"table" => 'transactiontype', 
	"where" => '', 
	"columns" => array(
	"transaction_type_pk",
	"type_name"),
	"returns" => array(
	"transactionTypePK",
	"typeName"),
	"conditions" => "",
	"endingQuery" => ""	);

	$returnedArray = $this->dbConnection->ConstructSelect($select_transactiontypes_array);
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function listTransactions($fromAccount, $category, $posted, $active, $dateOne, $dateTwo)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");

	$userGroup = $_SESSION["usergroup"];
	
	//Lets ensure the dates are in the proper order
	$this->dateClass->orderDatesPublic($dateOne, $dateTwo);
	$startDate = $this->dateClass->startDatePublic;
	$endDate = $this->dateClass->endDatePublic;

	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Start Date: $startDate /nEnd Date: $endDate.");
	
	//We need to move the start date to the "beggining" of the week
	$beginingBalanceDateArray = $this->dateClass->weekStartDate($startDate);
	reset($beginingBalanceDateArray);
	$beginingBalanceDate = key($beginingBalanceDateArray);
	
	//We need to move the ending date to the "end" of the week
	$endingBalanceDateArray = $this->dateClass->weekEndDate($endDate);
	reset($endingBalanceDateArray);
	$endingBalanceDate = key($endingBalanceDateArray);
	
	//lets get the start year and the start yday
	$startYear = $this->dateClass->returnYearPublic($beginingBalanceDate);
	$startYDay = $this->dateClass->returnYdayPublic($beginingBalanceDate);

	//lets get the end year
	$endYear = $this->dateClass->returnYearPublic($endingBalanceDate);
	
	//Lets get the nessisary date information to put the budget results into
	$frontEndArray = $this->dateClass->nDaysPatternPadded($startDate, $endDate, 1, 1, "internal");
	$internalDatesArray = $this->dateClass->nDaysPatternPadded($startDate, $endDate, 1, 1, true);	

		if($fromAccount > 0)
		{
		$endingQuery =  "AND (`from_account_pk` = $fromAccount OR `to_account_pk` = $fromAccount)";
		}
		else
		{
		$endingQuery =  "";
		}
		
		if($category > 0)
		{
		$categoryOperator = "=";
		$categoryValue = "$category";	
		}
		else
		{
		$categoryOperator = ">=";
		$categoryValue = "0";	
		}
	
		if($posted > 0)
		{
			switch($posted)
			{
				case 1: //Everything that is not posted
					$postedOperator = "=";
					$postedValue = "0";
					break;
					
				case 2: //Everything that is posted
					$postedOperator = ">";
					$postedValue = "0";
					break;
					
				case 3: //Posted and Not Posted transactions
					$postedOperator = ">=";
					$postedValue = "0";
					break;
			}
		}
		else
		{
		$postedOperator = "=";
		$postedValue = "0";	
		}
		
		if($active > 0)
		{
			switch($active)
			{
				case 1: //Everything that is not active
					$activeOperator = "=";
					$activeValue = "0";
					break;
					
				case 2: //Everything that is active
					$activeOperator = ">";
					$activeValue = "0";
					break;
					
				case 3: //Active and Not Active
					$activeOperator = ">=";
					$activeValue = "0";
					break;
			}
		}
		else
		{
		$activeOperator = "=";
		$activeValue = "0";	
		}
		
	$combinedArray = array();
	$noResultsCheck = false;

		while ($startYear <= $endYear) 
		{
			if ($startYear < $endYear)
			{
			//last day of the year
			$endYDay = $this->dateClass->returnYdayPublic("12/31/$startYear");	
			}
			
			if ($startYear == $endYear)
			{
			//we are at the end, set the requested end date
			$endYDay = $this->dateClass->returnYdayPublic($endingBalanceDate);
			}
			
			$select_transaction_array = array(
				"table" => 'transactions',
				"where" => 'WHERE',
				"columns" => array(
				"transaction_pk",
				"from_account_pk",
				"to_account_pk",
				"amount",
				"description",
				"category_pk",
				"posted",
				"modified_date"),
				"returns" => array(
				"transactionPK",
				"fromAccountPK",
				"toAccountPK",
				"amount",
				"description",
				"categoryPK",
				"posted",
				"modifiedDate"),
				"conditions" => array(
						array(
						"column" => "modified_year",
						"operator" => "=",
						"value" => "$startYear",
						"concat" => ""),
						array(
						"column" => "modified_y_day",
						"operator" => ">=",
						"value" => "$startYDay",
						"concat" => "AND"),
						array(
						"column" => "modified_y_day",
						"operator" => "<=",
						"value" => "$endYDay",
						"concat" => "AND"),
						array(
						"column" => "posted",
						"operator" => "$postedOperator",
						"value" => "$postedValue",
						"concat" => "AND"),
						array(
						"column" => "active",
						"operator" => "$activeOperator",
						"value" => "$activeValue",
						"concat" => "AND"),
						array(
						"column" => "category_pk",
						"operator" => "$categoryOperator",
						"value" => "$categoryValue",
						"concat" => "AND"),
						array(
						"column" => "user_group",
						"operator" => "=",
						"value" => "$userGroup",
						"concat" => "AND")
				),
				"endingQuery" => $endingQuery
				);
				
			$returnedArray = $this->dbConnection->ConstructSelect($select_transaction_array);
			$this->errorLogging->logInfo(__CLASS__, __METHOD__, $returnedArray);			
			
				if (is_array($returnedArray))
				{
				$combinedArray[] = $returnedArray;
				$noResultsCheck = true;
				}
			
			unset($returnedArray);
			
			if ($startYear <= $endYear)
			{
			$startYear++;
			$startYDay = 0;
			}
		}
	
	$accountNamesArray = array();
	
		if ($noResultsCheck)
		{
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Database Returned Array");
			foreach($combinedArray as $indexKey => $returnedArray)
			{
				foreach($returnedArray as $returnedKey => $returnedValue)
				{
					$modifieddate = $returnedValue["modifiedDate"];

					if(array_key_exists($modifieddate, $internalDatesArray))
					{
					$transactionPK = $returnedValue["transactionPK"];
					$week_number = $internalDatesArray["$modifieddate"][0];
					$day_number = $internalDatesArray["$modifieddate"][1];
					$frontEndArray[$week_number][$day_number][0]["dailyBalance"] = 0;
					
					$fromAccountNumber = $returnedValue["fromAccountPK"];
					$toAccountNumber = $returnedValue["toAccountPK"];
					
					//translate the account names
					if(array_key_exists($fromAccountNumber, $accountNamesArray))
					{
					$fromAccountName = $accountNamesArray["$fromAccountNumber"];	
					}
					else
					{
					$fromAccountName = $this->accountName($fromAccountNumber);
					$accountNamesArray["$fromAccountNumber"] = $fromAccountName;
					}
					
					if(array_key_exists($toAccountNumber, $accountNamesArray))
					{
					$toAccountName = $accountNamesArray["$toAccountNumber"];	
					}
					else
					{
					$toAccountName = $this->accountName($toAccountNumber);
					$accountNamesArray["$toAccountNumber"] = $toAccountName;
					}
					
					$fromBalance = 0; 
					$toAmount = $returnedValue["amount"];
					//If the transaction was to the selected account, flip the value
						if($fromAccount == $toAccountNumber)
						{
						$toAmount = 0 - $toAmount;
						}
					$description = $returnedValue["description"];
					$category = $this->categoryName($returnedValue["categoryPK"]);
					
						if ($returnedValue["posted"] == 0)
						{
						$posted = "yellow";
						}
						else
						{
						$posted = "green";
						}

					$frontEndArray[$week_number][$day_number][] = array("fromAccount" => "$fromAccountName", "toAccount" => "$toAccountName", "fromBalance" => "$fromBalance", "toAmount" => "$toAmount", "description" => "$description", "category" => "$category", "posted" => "$posted", "transactionPK" => $transactionPK);	
					}
				}
			}
			
			//go through the array, caclulate daily balances
			if($categoryValue > 0)
			{
			$balance = 0;	
			}
			else
			{
			$balance = $this->begginingBalanceOnDay($fromAccount, $beginingBalanceDate);	
			}
			
			$frontEndArrayCopy = $frontEndArray;

			foreach ($frontEndArrayCopy as $week_number => $week_array)
			{
				foreach($week_array as $day_number => $day_array)
				{
					if (array_key_exists("dailyBalance", $day_array[0]))
					{
						foreach($day_array as $day_item => $day_itemArray)
						{
							if ($day_item <> 0)
							{
							$amount = $day_itemArray["toAmount"];
							$balance = $balance - $amount;
							$frontEndArray[$week_number][$day_number][$day_item]["fromBalance"] = $balance;
							}
						}
					$frontEndArray[$week_number][$day_number][0]["dailyBalance"] = $balance;
						if ($balance < 0)
						{
						$frontEndArray[$week_number][$day_number][0]["balanceNegative"] = "red";
						}
						else
						{
						$frontEndArray[$week_number][$day_number][0]["balanceNegative"] = "none";
						}
					}
					else
					{
					$frontEndArray[$week_number][$day_number][0]["dailyBalance"] = $balance;
						if ($balance <= 0)
						{
						$frontEndArray[$week_number][$day_number][0]["balanceNegative"] = "red";
						}
						else
						{
						$frontEndArray[$week_number][$day_number][0]["balanceNegative"] = "none";
						}
					}
				}
			}
		}

	$this->responseClass->apiResponse($frontEndArray);
	return true;		
	}

/*	

sessionToken:63lBi5vecUc6fBNl
accountPK:2
categoryPK:0
posted:3
active:2
startDate:09/01/2015
endDate:09/30/2015
2, 0, 3, 2, 09/01/2015, 09/30/2015
	public function listTransactions($fromAccount, $dateOne, $dateTwo) //*
	{
	//0 - all categories
	//3 - posted and unposted
	//2 - only active
	return $this->internalListTransactions($fromAccount, 0, 3, 2, $dateOne, $dateTwo);
	}
	
	public function listTransactionsCategory($category, $dateOne, $dateTwo) //*
	{
	$fromAccount = 2; //THIS NEEDS TO CHANGE
	//3 - posted and unposted
	//2 - only active
	return $this->internalListTransactions($fromAccount, $category, 3, 2, $dateOne, $dateTwo);
	}
	
	public function listTransactionsPosted($fromAccount, $dateOne, $dateTwo) //*
	{
	//0 - all categories
	//2 - only posted
	//2 - only active
	return $this->internalListTransactions($fromAccount, 0, 2, 2, $dateOne, $dateTwo);
	}

	public function listTransactionsUnPosted($fromAccount, $dateOne, $dateTwo) //*
	{
	//0 - all categories
	//1 - only unposted
	//2 - only active
	return $this->internalListTransactions($fromAccount, 0, 1, 2, $dateOne, $dateTwo);
	}
*/	
	public function transactionDetails($transactionPK) //
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$select_array = array(
	"table" => 'transactions', 
	"where" => 'WHERE', 
	"columns" => array(
	"amount",
	"purchaser_pk",
	"payment_method_pk",
	"original_date",
	"modified_date",
	"from_account_pk",
	"to_account_pk",
	"active",
	"posted",
	"posted_date",
	"category_pk",
	"description"),
	"returns" => array(
	"amount",
	"purchaserPK",
	"paymentMethodPK",
	"originalDate",
	"modifiedDate",
	"fromAccountPK",
	"toAccountPK",
	"active",
	"posted",
	"postedDate",
	"categoryPK",
	"description"),
	"conditions" => array(
		array(
		"column" => "transaction_pk",
		"operator" => "=",
		"value" => "$transactionPK",
		"concat" => "")
	),
	"endingQuery" => ""
	);
	
	$returnedArray = $this->dbConnection->ConstructSelect($select_array);
	$resultsArray = $returnedArray[0];
	
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	/************** Add Functions **************/
	public function addAccount($accountNumber, $accountTypePK, $accountName)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$userGroup = $_SESSION["usergroup"];
	
	$insert_array = array(
	"table" => 'accounts', 
	"columns" => array(
	"account_number",
	"account_type_pk",
	"account_name",
	"user_group"),
	"values" => array(
	"$accountNumber",
	"$accountTypePK",
	"$accountName",
	"$userGroup")
	);
	
	$returnedArray = $this->dbConnection->ConstructInsert($insert_array);
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function addCategory($categoryName)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$userGroup = $_SESSION["usergroup"];
	
	$insert_array = array(
	"table" => 'categories', 
	"columns" => array(
	"category_name",
	"user_group"),
	"values" => array(
	"$categoryName",
	"$userGroup")
	);
	
	$returnedArray = $this->dbConnection->ConstructInsert($insert_array);
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}

	public function addTransaction($transactionTypePK, $amount, $purchaserPK, $paymentMethodPK, $originalDate, $fromAccountPK, $toAccountPK, $posted, $categoryPK, $description) 
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	//file_put_contents(LOG_DIRECTORY .  __CLASS__  . '.listTransactions', ' ');
	
	$userGroup = $_SESSION["usergroup"];

	$active = 1;

	//verify $transactionTypePK is numeric and an appropriate choice
	//convert $amount to decimal
	//verify $purchaserPK is numeric and an appropriate choice
	//verify $paymentMethodPK is numeric an appropriate choice
	//verify $fromAccountPK is numeric and an appropriate choice
	//verify toAccountPK is numeric
	
	$originalDate = $this->dateClass->dateReturnFormatPublic($originalDate);
	
		if ($originalDate === false)
		{
		return $this->responseClass->jsonify("", false);
		}
		else
		{
		$modifiedDate = $originalDate;

		$modifiedYear = $this->dateClass->returnYearPublic($modifiedDate);
		$modifiedYDay = $this->dateClass->returnYdayPublic($modifiedDate);
		}
	
	if (is_numeric($posted))
	{
		if ($posted == 1)
		{
		$postedDate = $originalDate;
		//$postedDate = ""; // need to add a get current date to the date api
		}
		else
		{
		$postedDate = $originalDate;
		}
	}
	else
	{
	return $this->responseClass->jsonify("", false);
	}	

	$insert_array = array(
	"table" => 'transactions', 
	"columns" => array(
	"transaction_type_pk",
	"amount",
	"purchaser_pk",
	"payment_method_pk",
	"original_date",
	"modified_date",
	"modified_year",
	"modified_y_day",
	"from_account_pk",
	"to_account_pk",
	"active",
	"posted",
	"posted_date",
	"category_pk",
	"description",
	"user_group"),
	"values" => array(
	"$transactionTypePK",
	"$amount",
	"$purchaserPK",
	"$paymentMethodPK",
	"$originalDate",
	"$modifiedDate",
	"$modifiedYear",
	"$modifiedYDay",
	"$fromAccountPK",
	"$toAccountPK",
	"$active",
	"$posted",
	"$postedDate",
	"$categoryPK",
	"$description",
	"$userGroup"));

	$returnedArray = $this->dbConnection->ConstructInsert($insert_array);

		if ($returnedArray !== false)
		{
		$fromBalanceUpdated = $this->updateBalance($fromAccountPK, $modifiedDate);
		$toBalanceUpdated = $this->updateBalance($toAccountPK, $modifiedDate);
		}

	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function addReoccurringTransaciton($startDate, $endDate, $transactionTypePK, $amount, $purchaserPK, $paymentMethodPK, $fromAccountPK, $toAccountPK, $posted, $categoryPK, $description) //*
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	//file_put_contents(LOG_DIRECTORY .  __CLASS__  . '.listTransactions', ' ');
	
	$active = 1;
	
	$userGroup = $_SESSION["usergroup"];
	
	$this->dateClass->orderDatesPublic($startDate, $endDate);
	$startdate = $this->dateClass->startDatePublic;
	$enddate = $this->dateClass->endDatePublic;
	
	$select_transactiontypes_array = array(
	"table" => 'transactiontype', 
	"where" => 'WHERE', 
	"columns" => array(
	"ndays",
	"operator"),
	"returns" => array(
	"ndays",
	"operator"),
	"conditions" => array(
	array(
	"column" => "transaction_type_pk",
	"operator" => "=",
	"value" => "$transactionTypePK",
	"concat" => "")
	),
	"endingQuery" => ""	
	);

	$returnedArray = $this->dbConnection->ConstructSelect($select_transactiontypes_array);
	
		if (is_array($returnedArray))
		{
		$nDays = $returnedArray["0"]["ndays"];
		$operator = $returnedArray["0"]["operator"];
		}
		else
		{
		return $this->responseClass->jsonify("", false);
		}
			
	$internalDatesArray = $this->dateClass->nDaysPattern($startdate, $enddate, $nDays, $operator, true);
	
	$insert_array = array(
	"table" => 'reoccurringtransactions', 
	"columns" => array(
	"transaction_type_pk",
	"start_date",
	"end_date"),
	"values" => array(
	"$transactionTypePK",
	"$startdate",
	"$enddate")
	);
		
	$reoccuringTransactionPK = $this->dbConnection->ConstructInsert($insert_array);
	
		if(!is_numeric($reoccuringTransactionPK))
		{
		return $this->responseClass->jsonify("", false);
		}
	
		if (is_array($internalDatesArray))
		{
			foreach($internalDatesArray as $currentDate => $info_array)
			{
			$originalDate = $this->dateClass->dateReturnFormatPublic($currentDate);
			$postedDate = $originalDate;
		
				if ($originalDate === false)
				{
				return $this->responseClass->jsonify("", false);
				}
				else
				{
				$modifiedDate = $originalDate;

				$modifiedYear = $this->dateClass->returnYearPublic($modifiedDate);
				$modifiedYDay = $this->dateClass->returnYdayPublic($modifiedDate);
				}
			
			$insert_array = array(
			"table" => 'transactions', 
			"columns" => array(
			"transaction_type_pk",
			"amount",
			"purchaser_pk",
			"payment_method_pk",
			"original_date",
			"modified_date",
			"modified_year",
			"modified_y_day",
			"from_account_pk",
			"to_account_pk",
			"active",
			"posted",
			"posted_date",
			"category_pk",
			"description",
			"user_group"),
			"values" => array(
			"$transactionTypePK",
			"$amount",
			"$purchaserPK",
			"$paymentMethodPK",
			"$originalDate",
			"$modifiedDate",
			"$modifiedYear",
			"$modifiedYDay",
			"$fromAccountPK",
			"$toAccountPK",
			"$active",
			"$posted",
			"$postedDate",
			"$categoryPK",
			"$description",
			"$userGroup"));
	
			$transactionPK = $this->dbConnection->ConstructInsert($insert_array);
			
				if (!is_numeric($transactionPK))
				{
				return $this->responseClass->jsonify("", false);
				}
				else
				{
				$frombalanceUpdated = $this->updateBalance($fromAccountPK, $currentDate);
				$tobalanceUpdated = $this->updateBalance($toAccountPK, $currentDate);
				
				$insert_array = array(
				"table" => 'relatedtransactions', 
				"columns" => array(
				"reoccurring_transaction_pk",
				"transaction_pk"),
				"values" => array(
				"$reoccuringTransactionPK",
				"$transactionPK")
				);
					
				$relatedTransactionPK = $this->dbConnection->ConstructInsert($insert_array);
				}		
			}
		}
		
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}	

	/************** Modify Functions **************/	
	public function modifyAccountAssoc($accountpk)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	return false;
	}

	public function modifyTransaction($transactionPK, $amount, $purchaserPK, $paymentMethodPK, $modifiedDate, $fromAccountPK, $toAccountPK, $active, $posted, $postedDate, $categoryPK, $description) 
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	//file_put_contents(LOG_DIRECTORY .  __CLASS__  . '.listTransactions', ' ');
		
	//pull transaction from transactions table
	$select_array = array(
	"table" => 'transactions', 
	"where" => 'WHERE', 
	"columns" => array(
	"amount",
	"purchaser_pk",
	"payment_method_pk",
	"original_date",
	"modified_date",
	"from_account_pk",
	"to_account_pk",
	"active",
	"posted",
	"posted_date",
	"category_pk",
	"description"),
	"returns" => array(
	"amount",
	"purchaserPK",
	"paymentMethodPK",
	"originalDate",
	"modifiedDate",
	"fromAccountPK",
	"toAccountPK",
	"active",
	"posted",
	"postedDate",
	"categoryPK",
	"description"),
	"conditions" => array(
		array(
		"column" => "transaction_pk",
		"operator" => "=",
		"value" => "$transactionPK",
		"concat" => "")
	),
	"endingQuery" => ""
	);
	
	$returnedArray = $this->dbConnection->ConstructSelect($select_array);
	
		if ($returnedArray === false)
		{
		return $this->responseClass->jsonify("", false);
		}
		
	$transactionArray = $returnedArray[0];	
		
	$transactionArray["modifiedDate"] = $this->dateClass->dateReturnFormatPublic($transactionArray["modifiedDate"]);
	$transactionArray["postedDate"] = $this->dateClass->dateReturnFormatPublic($transactionArray["postedDate"]);
	
	//this is a shitty way of doing this ... I was in a pinch, I swear. Don't be mad at me future Aaron. 
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' .  DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'sanitizeINC.php');
	$SanitizeClass = SanitizeClass::getInstance();
	
	$descriptionSanatized = $SanitizeClass->sanitizeValues($transactionArray["description"], false);
	
	//insert information in transactionhistory table
	$insert_array = array(
	"table" => 'transactionhistory', 
	"columns" => array(
	"transaction_pk",
	"amount",
	"purchaser_pk",
	"payment_method_pk",
	"last_modified_date",
	"from_account_pk",
	"to_account_pk",
	"active",
	"posted",
	"posted_date",
	"category_pk",
	"description"),
	"values" => array(
	$transactionPK,
	$transactionArray["amount"],
	$transactionArray["purchaserPK"],
	$transactionArray["paymentMethodPK"],
	$transactionArray["modifiedDate"],
	$transactionArray["fromAccountPK"],
	$transactionArray["toAccountPK"],
	$transactionArray["active"],
	$transactionArray["posted"],
	$transactionArray["postedDate"],
	$transactionArray["categoryPK"],
	$descriptionSanatized));

	$returnedArray = $this->dbConnection->ConstructInsert($insert_array);
	
	$modifiedDate = $this->dateClass->dateReturnFormatPublic($modifiedDate);
	$modifiedYear = $this->dateClass->returnYearPublic($modifiedDate);
	$modifiedYDay = $this->dateClass->returnYdayPublic($modifiedDate);

	$postedDate = $this->dateClass->dateReturnFormatPublic($postedDate);
	
		if ($returnedArray === false)
		{
		return $this->responseClass->jsonify("", false);
		}
	
	//modify entry in transactions table
	$update_array = array(
	"table" => 'transactions',
	"where" => "WHERE",
	"columns" => array(
	"amount",
	"purchaser_pk",
	"payment_method_pk",
	"modified_date",
	"modified_year",
	"modified_y_day",
	"from_account_pk",
	"to_account_pk",
	"active",
	"posted",
	"posted_date",
	"category_pk",
	"description"	),
	"values" => array(
	"$amount",
	"$purchaserPK",
	"$paymentMethodPK",
	"$modifiedDate",
	"$modifiedYear",
	"$modifiedYDay",
	"$fromAccountPK",
	"$toAccountPK",
	"$active",
	"$posted",
	"$postedDate",
	"$categoryPK",
	"$description"),
	"conditions" => array(
	array(
	"column" => "transaction_pk",
	"operator" => "=",
	"value" => "$transactionPK",
	"concat" => "")
	),
	"endingQuery" => ""
	);
		
	$returnedArray = $this->dbConnection->ConstructUpdate($update_array);

		if ($returnedArray !== false)
		{
		$fromBalanceUpdated_original = $this->updateBalance($fromAccountPK, $transactionArray["modifiedDate"]);
		$toBalanceUpdated_original = $this->updateBalance($toAccountPK, $transactionArray["modifiedDate"]);
		
		$fromBalanceUpdated = $this->updateBalance($fromAccountPK, $modifiedDate);
		$toBalanceUpdated = $this->updateBalance($toAccountPK, $modifiedDate);
		}	

	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function modifyAccount($accountPK, $accountNumber, $accountTypePK, $accountName)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	//modify entry in the accounts table
	$update_array = array(
	"table" => 'accounts',
	"where" => "WHERE",
	"columns" => array(
	"account_number",
	"account_type_pk",
	"account_name", 
	),
	"values" => array(
	"$accountNumber",
	"$accountTypePK",
	"$accountName"
	),
	"conditions" => array(
	array(
	"column" => "account_pk",
	"operator" => "=",
	"value" => "$accountPK",
	"concat" => "")
	),
	"endingQuery" => ""
	);
		
	$returnedArray = $this->dbConnection->ConstructUpdate($update_array);
	
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function modifyCategory($categoryPK, $categoryName)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	//modify entry in the categories table
	$update_array = array(
	"table" => 'categories',
	"where" => "WHERE",
	"columns" => array(
	"category_name",
	),
	"values" => array(
	"$categoryName",
	),
	"conditions" => array(
	array(
	"column" => "category_pk",
	"operator" => "=",
	"value" => "$categoryPK",
	"concat" => "")
	),
	"endingQuery" => ""
	);
		
	$returnedArray = $this->dbConnection->ConstructUpdate($update_array);
	
	$this->responseClass->apiResponse($returnedArray);
	return true;
	}
	
	public function accountAssocDetails()
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	return false;
	}

	private function updateBalance($accountPK, $date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	$balance = $this->endingBalance($accountPK, $date);
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Balance: $balance.");

	$firstDate = $this->dateClass->firstDayMonth($date);	
	$nextDate = $this->dateClass->addNDaysPublic($firstDate, 1, 2);
	$nextYear = $this->dateClass->returnYearPublic($nextDate);
	$nextMonth = $this->dateClass->returnMonthPublic($nextDate);

	//check if the balance exists. If not, create, if so update
	$select_array = array(
	"table" => 'balances', 
	"where" => 'WHERE', 
	"columns" => array(
	"balance"),
	"returns" => array(
	"balance"),
	"conditions" => array(
		array(
		"column" => "account_pk",
		"operator" => "=",
		"value" => "$accountPK",
		"concat" => ""),
		array(
		"column" => "month",
		"operator" => "=",
		"value" => "$nextMonth",
		"concat" => "AND"),
		array(
		"column" => "year",
		"operator" => "=",
		"value" => "$nextYear",
		"concat" => "AND")

	),
	"endingQuery" => ""
	);

	$returnedArray = $this->dbConnection->ConstructSelect($select_array);
		
		if (is_array($returnedArray))
		{
		$update_array = array(
		"table" => 'balances',
		"where" => "WHERE",
		"columns" => array(
		"balance"),
		"values" => array(
		"$balance"),
		"conditions" => array(
			array(
			"column" => "account_pk",
			"operator" => "=",
			"value" => "$accountPK",
			"concat" => ""),
			array(
			"column" => "month",
			"operator" => "=",
			"value" => "$nextMonth",
			"concat" => "AND"),
			array(
			"column" => "year",
			"operator" => "=",
			"value" => "$nextYear",
			"concat" => "AND")
			),
		"endingQuery" => ""
		);

		$returnedArray = $this->dbConnection->ConstructUpdate($update_array);
		}
		else
		{
		$insert_array = array(
		"table" => 'balances', 
		"columns" => array(
		"account_pk",
		"month",
		"year",
		"balance"),
		"values" => array(
		"$accountPK",
		"$nextMonth",
		"$nextYear",
		"$balance"));

		$returnedArray = $this->dbConnection->ConstructInsert($insert_array);
		}

	return $returnedArray;
	}

	private function endingBalance($accountPK, $dateOne) //*
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$originalYear = $this->dateClass->returnYearPublic($dateOne);

	$dateFirst = $this->dateClass->firstDayMonth($dateOne);

	$dateLast = $this->dateClass->lastDayMonth($dateFirst);

	$startYear = $this->dateClass->returnYearPublic($dateFirst);
	$startYDay = $this->dateClass->returnYdayPublic($dateFirst);

	$endYear = $this->dateClass->returnYearPublic($dateLast);
	$endYDay = $this->dateClass->returnYdayPublic($dateLast);

	$internalDatesArray = $this->dateClass->nDaysPattern($dateFirst, $dateLast, 1, 1, true);
		//year roll back
		if ($startYear < $originalYear)
		{
		$select_transaction_array = array(
			"table" => 'transactions',
			"where" => 'WHERE',
			"columns" => array(
			"transaction_pk",
			"from_account_pk",
			"to_account_pk",
			"amount",
			"description",
			"category_pk",
			"posted",
			"modified_date"),
			"returns" => array(
			"transactionPK",
			"fromAccountPK",
			"toAccountPK",
			"amount",
			"description",
			"categoryPK",
			"posted",
			"modifiedDate"),
			"conditions" => "((modified_year >= $startYear AND modified_y_day >= $startYDay) OR (modified_year >= $originalYear AND modified_y_day >= 0)) AND (modified_year <= $endYear AND modified_y_day <= $endYDay) AND active >= 1",
			"endingQuery" => "AND (`from_account_pk` = $accountPK OR `to_account_pk` = $accountPK)"
			);
		}        

		//Same year
		if ($startYear == $originalYear)
		{
		$select_transaction_array = array(
			"table" => 'transactions',
			"where" => 'WHERE',
			"columns" => array(
			"transaction_pk",
			"from_account_pk",
			"to_account_pk",
			"amount",
			"description",
			"category_pk",
			"posted",
			"modified_date"),
			"returns" => array(
			"transactionPK",
			"fromAccountPK",
			"toAccountPK",
			"amount",
			"description",
			"categoryPK",
			"posted",
			"modifiedDate"),
			"conditions" => array(
					array(
					"column" => "modified_year",
					"operator" => ">=",
					"value" => "$startYear",
					"concat" => ""),
					array(
					"column" => "modified_y_day",
					"operator" => ">=",
					"value" => "$startYDay",
					"concat" => "AND"),
					array(
					"column" => "modified_year",
					"operator" => "<=",
					"value" => "$endYear",
					"concat" => "AND"),
					array(
					"column" => "modified_y_day",
					"operator" => "<=",
					"value" => "$endYDay",
					"concat" => "AND"),
					array(
					"column" => "active",
					"operator" => ">=",
					"value" => "1",
					"concat" => "AND")
			),
			"endingQuery" => "AND (`from_account_pk` = $accountPK OR `to_account_pk` = $accountPK)"
			);
		}

		//year roll forward
		if ($startYear > $originalYear)
		{
		$select_transaction_array = array(
			"table" => 'transactions',
			"where" => 'WHERE',
			"columns" => array(
			"transaction_pk",
			"from_account_pk",
			"to_account_pk",
			"amount",
			"description",
			"category_pk",
			"posted",
			"modified_date"),
			"returns" => array(
			"transactionPK",
			"fromAccountPK",
			"toAccountPK",
			"amount",
			"description",
			"categoryPK",
			"posted",
			"modifiedDate"),
			"conditions" => "((modified_year >= $startYear AND modified_y_day >= $startYDay) OR (modified_year >= $originalYear AND modified_y_day >= 0)) AND (modified_year <= $endYear AND modified_y_day <= $endYDay) AND active >= 1",
			"endingQuery" => "AND (`from_account_pk` = $accountPK OR `to_account_pk` = $accountPK)"
			);
		}
	
	$returnedArray = $this->dbConnection->ConstructSelect($select_transaction_array);

		if (is_array($returnedArray))
		{
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Database Returned Array");
		$balance = $this->openingBalance($accountPK, $dateFirst);
			foreach($returnedArray as $returnedKey => $returnedValue)
			{
				$modifieddate = $returnedValue["modifiedDate"];
												
				if(array_key_exists($modifieddate, $internalDatesArray))
				{
				$amount = $returnedValue["amount"];
					if($accountPK == $returnedValue["toAccountPK"])
					{
					$amount = 0 - $returnedValue["amount"];
					}
				$balance = $balance - $amount;
				}
			}			
		}
		else
		{
		$balance = 0;
		}

	return $balance;
	}

	private function openingBalance($accountPK, $date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	//get this information from the database!
	$year = $this->dateClass->returnYearPublic($date);
	$month = $this->dateClass->returnMonthPublic($date);

	$select_array = array(
	"table" => 'balances', 
	"where" => 'WHERE', 
	"columns" => array(
	"balance"),
	"returns" => array(
	"balance"),
	"conditions" => array(
		array(
		"column" => "account_pk",
		"operator" => "=",
		"value" => "$accountPK",
		"concat" => ""),
		array(
		"column" => "month",
		"operator" => "=",
		"value" => "$month",
		"concat" => "AND"),
		array(
		"column" => "year",
		"operator" => "=",
		"value" => "$year",
		"concat" => "AND")

	),
	"endingQuery" => ""
	);

	$returnedArray = $this->dbConnection->ConstructSelect($select_array);
		
		if (is_array($returnedArray))
		{
		$balance = $returnedArray[0]["balance"];
		}
		else
		{
		$balance = 0;
		}

	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Opening Balance $date: $balance.");
	return $balance;
	}

	private function balanceOnDay($accountPK, $date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");

	$originalYear = $this->dateClass->returnYearPublic($date);
	
	$dateFirst = $this->dateClass->firstDayMonth($date);

	$dateLast = $date;

	$startYear = $this->dateClass->returnYearPublic($dateFirst);
	$startYDay = $this->dateClass->returnYdayPublic($dateFirst);

	$endYear = $this->dateClass->returnYearPublic($dateLast);
	$endYDay = $this->dateClass->returnYdayPublic($dateLast);

	$internalDatesArray = $this->dateClass->nDaysPattern($dateFirst, $dateLast, 1, 1, true);

		//year roll back
		if ($startYear < $originalYear)
		{
		$select_transaction_array = array(
			"table" => 'transactions',
			"where" => 'WHERE',
			"columns" => array(
			"transaction_pk",
			"from_account_pk",
			"to_account_pk",
			"amount",
			"description",
			"category_pk",
			"posted",
			"modified_date"),
			"returns" => array(
			"transactionPK",
			"fromAccountPK",
			"toAccountPK",
			"amount",
			"description",
			"categoryPK",
			"posted",
			"modifiedDate"),
			"conditions" => "((modified_year >= $startYear AND modified_y_day >= $startYDay) OR (modified_year >= $originalYear AND modified_y_day >= 0)) AND (modified_year <= $endYear AND modified_y_day <= $endYDay) AND active >= 1",
			"endingQuery" => "AND (`from_account_pk` = $accountPK OR `to_account_pk` = $accountPK)"
			);
		}        

		//Same year
		if ($startYear == $originalYear)
		{
		$select_transaction_array = array(
			"table" => 'transactions',
			"where" => 'WHERE',
			"columns" => array(
			"transaction_pk",
			"from_account_pk",
			"to_account_pk",
			"amount",
			"description",
			"category_pk",
			"posted",
			"modified_date"),
			"returns" => array(
			"transactionPK",
			"fromAccountPK",
			"toAccountPK",
			"amount",
			"description",
			"categoryPK",
			"posted",
			"modifiedDate"),
			"conditions" => array(
					array(
					"column" => "modified_year",
					"operator" => ">=",
					"value" => "$startYear",
					"concat" => ""),
					array(
					"column" => "modified_y_day",
					"operator" => ">=",
					"value" => "$startYDay",
					"concat" => "AND"),
					array(
					"column" => "modified_year",
					"operator" => "<=",
					"value" => "$endYear",
					"concat" => "AND"),
					array(
					"column" => "modified_y_day",
					"operator" => "<=",
					"value" => "$endYDay",
					"concat" => "AND"),
					array(
					"column" => "active",
					"operator" => ">=",
					"value" => "1",
					"concat" => "AND")
			),
			"endingQuery" => "AND (`from_account_pk` = $accountPK OR `to_account_pk` = $accountPK)"
			);
		}

		//year roll forward
		if ($startYear > $originalYear)
		{
		$select_transaction_array = array(
			"table" => 'transactions',
			"where" => 'WHERE',
			"columns" => array(
			"transaction_pk",
			"from_account_pk",
			"to_account_pk",
			"amount",
			"description",
			"category_pk",
			"posted",
			"modified_date"),
			"returns" => array(
			"transactionPK",
			"fromAccountPK",
			"toAccountPK",
			"amount",
			"description",
			"categoryPK",
			"posted",
			"modifiedDate"),
			"conditions" => "((modified_year >= $startYear AND modified_y_day >= $startYDay) OR (modified_year >= $originalYear AND modified_y_day >= 0)) AND (modified_year <= $endYear AND modified_y_day <= $endYDay) AND active >= 1",
			"endingQuery" => "AND (`from_account_pk` = $accountPK OR `to_account_pk` = $accountPK)"
			);
		}
	
	$returnedArray = $this->dbConnection->ConstructSelect($select_transaction_array);

		if (is_array($returnedArray))
		{
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Database Returned Array");
		$balance = $this->openingBalance($accountPK, $dateFirst);
			foreach($returnedArray as $returnedKey => $returnedValue)
			{
				$modifieddate = $returnedValue["modifiedDate"];

				if(array_key_exists($modifieddate, $internalDatesArray))
				{
				$amount = $returnedValue["amount"];
					if($accountPK == $returnedValue["toAccountPK"])
					{
					$amount = 0 - $amount;
					}
				$balance = $balance - $amount;
				}
			}			
		}
		else
		{
		$balance = 0;
		}

	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Balance on Day $date: $balance.");
	return $balance;
	}
	
	private function begginingBalanceOnDay($accountPK, $date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");

	$originalYear = $this->dateClass->returnYearPublic($date);
		
	$dateFirst = $this->dateClass->firstDayMonth($date);

	$dateLast = $date;
	
	$excludeDate = $this->dateClass->monthYearDateFormatPublic($date);

	$startYear = $this->dateClass->returnYearPublic($dateFirst);
	$startYDay = $this->dateClass->returnYdayPublic($dateFirst);

	$endYear = $this->dateClass->returnYearPublic($dateLast);
	$endYDay = $this->dateClass->returnYdayPublic($dateLast);
	
		if ($startYear == $endYear && $startYDay == $endYDay) //?
		{
		$balance = $this->openingBalance($accountPK, $dateFirst);
		}
		else
		{
		$internalDatesArray = $this->dateClass->nDaysPattern($dateFirst, $dateLast, 1, 1, true);

		//year roll back
		if ($startYear < $originalYear)
		{
		$select_transaction_array = array(
			"table" => 'transactions',
			"where" => 'WHERE',
			"columns" => array(
			"transaction_pk",
			"from_account_pk",
			"to_account_pk",
			"amount",
			"description",
			"category_pk",
			"posted",
			"modified_date"),
			"returns" => array(
			"transactionPK",
			"fromAccountPK",
			"toAccountPK",
			"amount",
			"description",
			"categoryPK",
			"posted",
			"modifiedDate"),
			"conditions" => "((modified_year >= $startYear AND modified_y_day >= $startYDay) OR (modified_year >= $originalYear AND modified_y_day >= 0)) AND (modified_year <= $endYear AND modified_y_day <= $endYDay) AND active >= 1",
			"endingQuery" => "AND (`from_account_pk` = $accountPK OR `to_account_pk` = $accountPK)"
			);
		}        

		//Same year
		if ($startYear == $originalYear)
		{
		$select_transaction_array = array(
			"table" => 'transactions',
			"where" => 'WHERE',
			"columns" => array(
			"transaction_pk",
			"from_account_pk",
			"to_account_pk",
			"amount",
			"description",
			"category_pk",
			"posted",
			"modified_date"),
			"returns" => array(
			"transactionPK",
			"fromAccountPK",
			"toAccountPK",
			"amount",
			"description",
			"categoryPK",
			"posted",
			"modifiedDate"),
			"conditions" => array(
					array(
					"column" => "modified_year",
					"operator" => ">=",
					"value" => "$startYear",
					"concat" => ""),
					array(
					"column" => "modified_y_day",
					"operator" => ">=",
					"value" => "$startYDay",
					"concat" => "AND"),
					array(
					"column" => "modified_year",
					"operator" => "<=",
					"value" => "$endYear",
					"concat" => "AND"),
					array(
					"column" => "modified_y_day",
					"operator" => "<=",
					"value" => "$endYDay",
					"concat" => "AND"),
					array(
					"column" => "active",
					"operator" => ">=",
					"value" => "1",
					"concat" => "AND")
			),
			"endingQuery" => "AND (`from_account_pk` = $accountPK OR `to_account_pk` = $accountPK)"
			);
		}

		//year roll forward
		if ($startYear > $originalYear)
		{
		$select_transaction_array = array(
			"table" => 'transactions',
			"where" => 'WHERE',
			"columns" => array(
			"transaction_pk",
			"from_account_pk",
			"to_account_pk",
			"amount",
			"description",
			"category_pk",
			"posted",
			"modified_date"),
			"returns" => array(
			"transactionPK",
			"fromAccountPK",
			"toAccountPK",
			"amount",
			"description",
			"categoryPK",
			"posted",
			"modifiedDate"),
			"conditions" => "((modified_year >= $startYear AND modified_y_day >= $startYDay) OR (modified_year >= $originalYear AND modified_y_day >= 0)) AND (modified_year <= $endYear AND modified_y_day <= $endYDay) AND active >= 1",
			"endingQuery" => "AND (`from_account_pk` = $accountPK OR `to_account_pk` = $accountPK)"
			);
		}

		$this->errorLogging->logInfo(__CLASS__, __METHOD__, $select_transaction_array);
		
		$returnedArray = $this->dbConnection->ConstructSelect($select_transaction_array);

			if (is_array($returnedArray))
			{
			$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Database Returned Array");
			$balance = $this->openingBalance($accountPK, $dateFirst);
				foreach($returnedArray as $returnedKey => $returnedValue)
				{
					$modifieddate = $returnedValue["modifiedDate"];

					if(array_key_exists($modifieddate, $internalDatesArray) && $modifieddate != $excludeDate)
					{
					$amount = $returnedValue["amount"];
						if($accountPK == $returnedValue["toAccountPK"])
						{
						$amount = 0 - $amount;
						}
					$balance = $balance - $amount;
					}
				}			
			}
			else
			{
			$this->errorLogging->logInfo(__CLASS__, __METHOD__, "no array returned from the database");
			$balance = 0;
			}			
		}	

	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Balance on Day $date: $balance.");
	return $balance;
	}
	
	private function accountName($accountPK) //*
	{
	//check if an internal array exists, if not fetch it, if so cross reference the name
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		
	$select_array = array(
	"table" => 'accounts', 
	"where" => 'WHERE', 
	"columns" => array(
	"account_name"),
	"returns" => array(
	"accountName"),
	"conditions" => array(
		array(
		"column" => "account_pk",
		"operator" => "=",
		"value" => "$accountPK",
		"concat" => "")		
	),
	"endingQuery" => ""
	);
		
	$returnedArray = $this->dbConnection->ConstructSelect($select_array);
	$accountName = $returnedArray[0]["accountName"];
	
	return $accountName;
	}
	
	private function categoryName($categoryPK) //*
	{
	//check if an internal array exists, if not fetch it, if so cross reference the name
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		
	$select_array = array(
	"table" => 'categories', 
	"where" => 'WHERE', 
	"columns" => array(
	"category_name"),
	"returns" => array(
	"categoryName"),
	"conditions" => array(
		array(
		"column" => "category_pk",
		"operator" => "=",
		"value" => "$categoryPK",
		"concat" => "")		
	),
	"endingQuery" => ""
	);
		
	$returnedArray = $this->dbConnection->ConstructSelect($select_array);
	$categoryName = $returnedArray[0]["categoryName"];
	
	return $categoryName;
	}
	
	private function accountType($accountPK) //*
	{
	//check if an internal array exists, if not fetch it, if so cross reference the name
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
		
	$select_array = array(
	"table" => 'accounts', 
	"where" => 'WHERE', 
	"columns" => array(
	"account_type_pk"),
	"returns" => array(
	"accountTypePK"),
	"conditions" => array(
		array(
		"column" => "account_pk",
		"operator" => "=",
		"value" => "$accountPK",
		"concat" => "")		
	),
	"endingQuery" => ""
	);
		
	$returnedArray = $this->dbConnection->ConstructSelect($select_array);
		if (is_array($returnedArray ))
		{
		$accountTypePK = $returnedArray[0]["accountTypePK"];
		}
		else
		{
		$accountTypePK = 5;
		}
	return $accountTypePK;
	}
}
?>
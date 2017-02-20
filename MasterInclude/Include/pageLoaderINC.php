<?php
class PageLoaderClass
{
	//MODULARIZED
	public static function loadJavascriptExtenders($system, $page)
	{
	$system = ucfirst($system);
	$page = ucfirst($page);
	$scriptsBase = $system . DIRECTORY_SEPARATOR . 'Extenders' . DIRECTORY_SEPARATOR;
	
		switch($page)
		{				
			default:
				$page = $scriptsBase . $page . '_Extender.php';
				return PageLoaderClass::requirePage($page);
				break;
		}
	}
	
	public static function loadSystemPage($system, $page, $type)
	{
		$system = ucfirst($system);
		$page = ucfirst($page);
		$type = ucfirst($type);
		
		switch($page)
		{
			default:
				$page = $system . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $page . "_" . $type . ".php";
				return PageLoaderClass::requirePage($page);
				break;			
		}		
	}
	
	public static function setVariables($page)
	{
		switch($page)
		{
			case "CalendarBudget":
				require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Dates' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'datesINC.php');
				$dateClass = new ProceduralDates();
				$startDate = $dateClass->monthYearDateFormatPublic($dateClass->getCurrentDate());
				$endDate = $dateClass->monthYearDateFormatPublic($dateClass->lastDayMonth($startDate));
				
				//NEED A CALL TO A DEFAULT ACCOUNT AND CATEGORY BASED ON UID
				$variable_list_array = array(
					"start" => array("startDate", $startDate), 
					"end" => array("endDate", $endDate), 
					"account" => array("account", 2), 
					"posted" => array("posted", 3), 
					"category" => array("category", 0),
					"active" => array("active", 2),
					"system" => array("system", "budget")
					);

				return PageLoaderClass::variablesArray($variable_list_array);
				break;
				
				case "TransactionForm":
				require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'Dates' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'datesINC.php');
				$dateClass = new ProceduralDates();
				$startDate = $dateClass->monthYearDateFormatPublic($dateClass->getCurrentDate());
				
				$variable_list_array = array(
					"transactionPK" => array("transactionPK", "new"),
					"system" => array("system", "budget"),
					"date" => array("date", "$startDate"),
					"account" => array("account", "2")
					);

				return PageLoaderClass::variablesArray($variable_list_array);
				break;
				
				case "AccountForm":				
				$variable_list_array = array(
					"accountTypePK" => array("accountTypePK", "new"),
					"system" => array("system", "budget")
					);

				return PageLoaderClass::variablesArray($variable_list_array);
				break;
				
				case "CategoryForm":				
				$variable_list_array = array(
					"categoryTypePK" => array("categoryTypePK", "new"),
					"system" => array("system", "budget")
					);

				return PageLoaderClass::variablesArray($variable_list_array);
				break;
				
				case "genericTest":				
				$variable_list_array = array(
					"system" => array("system", "UnitTest")
					);

				return PageLoaderClass::variablesArray($variable_list_array);
				break;
			
			default:
				return false;
				break;
		}
	}
	
	private static function requirePage($page)
	{
	return realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . $page;		
	}

	private static function variablesArray($variable_list_array)
	{
	$return_array = array();
	$argumentString = "&";
		foreach($variable_list_array as $getName => $argumentNameArray)
		{
		$argumentName = $argumentNameArray["0"];
		$argumentDefaultValue = $argumentNameArray["1"];
		
			if(array_key_exists($getName, $_GET))
			{
			$return_array["$argumentName"] = $_GET["$getName"];	
			$argumentString = $argumentString . "$getName=" .  $_GET["$getName"] . "&";
			}
			else
			{
			$return_array["$argumentName"] = $argumentDefaultValue;	
			$argumentString = $argumentString . "$getName=" .  $argumentDefaultValue . "&";	
			}			
		}
		
	$argumentString = substr($argumentString, 0, -1);
	$return_array["argumentString"] = $argumentString;
	
	return $return_array;
	}
}
?>
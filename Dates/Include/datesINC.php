<?php
/*
Usage:
$dates = new ProceduralDates();
$last_day = $dates->lastDayMonth($month, $year); //returns string
$week_array = $dates->weeksOfTheYear($year);  //returns array
$pattern_array = $dates->nDaysPattern($start_date, $end_date, $nDays, $operator); //returns array
*/

//a better (universal?) check if the dates aren't valid. I don't wnat to have to check in each function!
class ProceduralDates
{
	public $startDatePublic;
	public $endDatePublic;

	private $startDate;
	private $endDate;
	private $responseClass;
	private $errorLogging;
	
	public function ProceduralDates()
	{
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'responseINC.php');
		require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'loggingINC.php');
		
		$this->responseClass = ResponseClass::getInstance();
		$this->errorLogging = LoggingClass::getInstance();
		$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	}
	
	public function argumentList($action)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$masterListArray = array();
	
	$masterListArray["nDaysPattern_array"] = array("dateOne" => "date", "dateTwo" => "date", "nDays" => "int", "operator" => "int");
	$masterListArray["nDaysPatternPadded_array"] = array("dateOne" => "date", "dateTwo" => "date", "nDays" => "int", "operator" => "int");
	$masterListArray["numberOfDays_array"] = array("date" => "date");
	$masterListArray["lastDayMonth_array"] = array("date" => "date");
	$masterListArray["weekStartDate_array"] = array("date" => "date");
	$masterListArray["weekEndDate_array"] = array("date" => "date");
	$masterListArray["weeksOfTheYear_array"] = array("year" => "int");
	$masterListArray["getDateCurrent_array"] = "";
	
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
	
	public function monthYearDateFormatPublic($date)
	{
	return $this->monthYearDateFormat($date);
	}
	
	public function getCurrentDate()
	{
	$year = date('Y');
	$month = date('m');
	$date = "$year-$month-01";		
	return $this->dateReturnFormat($date);
	}
	
	public function getDateCurrent()
	{
	$year = date('Y');
	$month = date('m');
	$day = date('d');
	$date = "$month/$day/$year";	
	$formated_date =  $this->monthYearDateFormat($date);		
	$this->responseClass->apiResponse($formated_date);
	return true;
	}
	
	public function addNDaysPublic($dateOne, $nDays, $operator)
	{
	return $this->addNDays($dateOne, $nDays, $operator);
	}

	public function verifyDatePublic($supplied_date)
	{
	return $this->verifyDate($supplied_date);
	}
	
	public function dateReturnFormatPublic($date)
	{
	return $this->dateReturnFormat($date);
	}
	
	public function orderDatesPublic($dateOne, $dateTwo)
	{
	$this->orderDates($dateOne, $dateTwo);
	
	$this->startDatePublic = $this->startDate;
	$this->endDatePublic = $this->endDate;	
	}
	
	public function returnYearPublic($date)
	{
	return $this->returnYear($date);
	}

	public function returnMonthPublic($date)
	{
	return $this->returnMonth($date);
	}
	
	public function returnYdayPublic($date)
	{
	return $this->returnYday($date);
	}
	
	public function nDaysPattern($dateOne, $dateTwo, $nDays, $operator, $public = false)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	return $this->nDaysPatternPrivate($dateOne, $dateTwo, $nDays, $operator, $public);
	}
	
	public function nDaysPatternPadded($dateOne, $dateTwo, $nDays, $operator, $public = false)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$this->orderDates($dateOne, $dateTwo);
	$startDate = $this->startDate;
	$endDate = $this->endDate;
	
	$weekStartArray = $this->weekStartDatePrivate($startDate, true);
	$weekEndArray = $this->weekEndDatePrivate($endDate, true);
	
	reset($weekStartArray);
	$weekStart = key($weekStartArray);

	reset($weekEndArray);
	$weekEnd = key($weekEndArray);

	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Week Start: $weekStart /nWeek End: $weekEnd");

	return $this->nDaysPatternPrivate($weekStart, $weekEnd, $nDays, $operator, $public);
	}

	public function numberOfDays($date, $internal = false)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Date: $date");
	
	$date_year = $this->verifyDate($date);
		if ($date_year !== false)
		{
		$year = $date_year->format('Y');
		}
		else
		{
		$this->responseClass->apiResponse("Invalid Date");
		return false;
		}

	$year_start = $year . "-01-01";
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "DIFF = $year_start -> $date");
	$date1 = $this->verifyDate($year_start); //= new DateTime($year_start);
	$date2 = $this->verifyDate($date); //= new DateTime($date);

	$diff = $date2->diff($date1)->format("%a");
	$diff++;
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "DIFF ==> $diff");
	
		if ($internal === false)
		{
		$return_array = array("yday" => $diff);

		$this->responseClass->apiResponse($return_array);
		return true;
		}
		else
		{
		return $diff;
		}
	}
	
	public function lastDayMonth($date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	$this->responseClass->apiResponse($this->lastDayMonthPrivate($date, false));
	
	return $this->dateReturnFormat($this->lastDayMonthPrivate($date, false));
	}

	public function firstDayMonth($date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$month = $this->returnMonth($date);
		if ($month === false)
		{
		$this->responseClass->apiResponse("Invalid Date");
		return false;
		}
	$year = $this->returnYear($date);
	
	$first_date = "$year/$month/01";

	return $this->dateReturnFormat($first_date);
	}
	
	public function weekStartDate($date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	return $this->weekStartDatePrivate($date, true);
	}
	
	public function weekEndDate($date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	return $this->weekEndDatePrivate($date, true);
	}
	
	//should this be a date?
	public function weeksOfTheYear($year)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	//function for creating an array containing the start date of each week of the year

	//define the dates encompasing a year
	$start_date = "$year" . "-01-01";
	$end_date = "$year" . "-12-31";

	//find the numerical day of the week of the start date
	$date_one = $this->verifyDate($start_date); // = new DateTime($start_date);
		if ($date_one === false)
		{
		$this->responseClass->apiResponse("Invalid Date");
		return false;
		}

	$year_start_dow = $date_one->format('w');

	//find the numerical day of the week of the end date
	$date_one = $this->verifyDate($end_date); // = new DateTime($end_date);
	$year_end_dow = $date_one->format('w');

		//take the start date back to sunday (numerically)
		while ($year_start_dow <> 1)
		{
		$date_one = $this->verifyDate($start_date); // = new DateTime($start_date);
		$date_one->sub(new DateInterval("P1D"));
		$year_start_dow = $date_one->format('w');
		$start_date = $date_one->format('Y-m-d');
		}

		//take the end date out to saturday (numerically)
		while ($year_end_dow <> 5)
		{
		$date_one = $this->verifyDate($end_date); // = new DateTime($end_date);
		$date_one->add(new DateInterval("P1D"));
		$year_end_dow = $date_one->format('w');
		$end_date = $date_one->format('Y-m-d');
		}

	//set reoccurance pattern to happen every 7 days and itterate pattern
	$nDays = 7;
	$operator = 1;
	
	return $this->nDaysPatternPrivate($start_date, $end_date, $nDays, $operator, false);
	}
	
	private function weekStartDatePrivate($date, $internal)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");

	$date_one = $this->verifyDate($date);
		if ($date_one === false)
		{
		$this->responseClass->apiResponse("Invalid Date");
		return false;
		}

	$date_week_starts = $date_one->format('w');

		while ($date_week_starts > 0)
		{
		$date_one = $this->verifyDate($date);
		$date_one->sub(new DateInterval("P1D"));
		$date_week_starts = $date_one->format('w');
		$date = $date_one->format('Y-m-d');
		}

	return $this->nDaysPatternPrivate($date, $date, 1, 1, $internal);
	}
	
	private function weekEndDatePrivate($date, $internal)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$date_one = $this->verifyDate($date);
		if ($date_one === false)
		{
		$this->responseClass->apiResponse("Invalid Date");
		return false;
		}

	$date_week_ends = $date_one->format('w');

		while ($date_week_ends < 6)
		{
		$date_one = $this->verifyDate($date);
		$date_one->add(new DateInterval("P1D"));
		$date_week_ends = $date_one->format('w');
		$date = $date_one->format('Y-m-d');
		}
	
	return $this->nDaysPatternPrivate($date, $date, 1, 1, $internal);
	}
	
	private function lastDayMonthPrivate($date, $internal)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$month = $this->returnMonth($date);
		if ($month === false)
		{
		$this->responseClass->apiError("Invalid Dates");
		return false;
		}
	$year = $this->returnYear($date);
	
	//function for finding the last day of the month
	$end_date = "$year/$month/01";
	$date_one = $this->verifyDate($end_date); // = new DateTime($end_date);
	$last_day = $date_one->format('t');

	$end_date = "$year/$month/$last_day";
	$returnDate = $this->dateReturnFormat($end_date);	
	
	return $returnDate;
	}

	private function nDaysPatternPrivate($dateOne, $dateTwo, $nDays, $operator, $internal)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
		if (false === $this->orderDates($dateOne, $dateTwo))
		{
		$this->responseClass->apiError("Invalid Dates");
		}

	$start_date = $this->startDate;
	$end_date = $this->endDate;
	
	//**switch needs work**
		switch($operator)
		{
			case 1:
				$operatorA = "D";
				break;

			case 2:
				$operatorA = "M";
				break;

			case 3:
                                $operatorA = "Y";
                                break;

			default:
				$operatorA = "D";
		}

	$nDays = "P" . $nDays . "$operatorA";
	$dates_array = array();
	$count = 0;

	//start date classes for start date and end date
	$date1 = $this->verifyDate($start_date); // = new DateTime($start_date);
	$date2 = $this->verifyDate($end_date); // = new DateTime($end_date);	
	
		if ($internal === false)
		{
		$start_date = $date1->format('Y-m-d');
		$end_date = $date2->format('Y-m-d');
		}
		else
		{
		$start_date = $date1->format('m/d/Y');
		$end_date = $date2->format('m/d/Y');
		}

	//get the day of the week for the start day
	$date_week = $this->verifyDate($start_date); // = new DateTime($start_date);
	$week_date = $date_week->format('l');

	//****this is how I create the format****
	//should be $dates_array[week_number][date_number][item][property_name] = value;
	$week_number = 0;
	$day_number = 0;
	$dates_array[$week_number][$day_number][0] = array("numericDay" => $start_date, "weekDate" => $week_date);
	$dates_arrayInternal[$start_date] = array($week_number, $day_number);
	$current_date = "$start_date";

	//find the numeric number of days between start date and end date with leading +/-
	$interval = $date1->diff($date2);
	$difference_sign = $interval->format('%R');
	$difference_numeric = $interval->days;
	$interval_numeric = "$difference_sign" . "$difference_numeric";
	$interval_numeric = number_format($interval_numeric, 0);
		//itterate	through the $current_date to the $end_date
		while($interval_numeric >= 0)
		{
		$day_number++;

		//start date class for current date,
		$date_one = $this->verifyDate($current_date); // = new DateTime($current_date);
		//advance date by the interval specified
		$date_one->add(new DateInterval("$nDays"));
		//correct format
			if ($internal === false)
			{
			$next_date = $date_one->format('Y-m-d');
			}
			else
			{
			$next_date = $date_one->format('m/d/Y');
			}
		$week_date = $date_one->format('l');

		//set $current_date to $next_date for next itteration
		$current_date = $next_date;

		//find the numeric number of days between the current date and end date with leading +/-
		$date1 = $this->verifyDate($current_date); // = new DateTime($current_date);
		$interval = $date1->diff($date2);
		$difference_sign = $interval->format('%R');
		$difference_numeric = $interval->days;
		$interval_numeric = "$difference_sign" . "$difference_numeric";
		$interval_numeric = number_format($interval_numeric, 0);

			//if the pattern has not exceeded the end date, store it in the array
			if ($interval_numeric >= 0)
			{
			//****this is how I create the format****	
			$dates_arrayInternal[$current_date] = array($week_number, $day_number);
			$dates_array[$week_number][$day_number][0] = array("numericDay" => $current_date, "weekDate" => $week_date);
			}
			else
			{
			//consider breaking out of at this point	
			}

			if ($day_number == 6)
			{
			$day_number = -1;
			$week_number++;
			}
		}

	//return the array
		if ($internal === false)
		{
		$this->responseClass->apiResponse($dates_array);
		}
		else if ($internal === true)
		{
		return $dates_arrayInternal;
		}
		elseif ($internal == "internal") 
		{
		return $dates_array;
		}
	}

	private function addNDays($dateOne, $nDays, $operator)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	//**switch needs work**
		switch($operator)
		{
			case 1:
				$operatorA = "D";
				break;

			case 2:
				$operatorA = "M";
				break;

			default:
				$operatorA = "D";
		}

	$nDays = "P" . $nDays . "$operatorA";

	$date1 = $this->verifyDate($dateOne);
	$date1->add(new DateInterval("$nDays"));
	$next_date = $date1->format('m/d/Y');

	return $next_date;
	}

	private function returnYear($date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$date_year = $this->verifyDate($date);
		if ($date_year !== false)
		{
		$year = $date_year->format('Y');
		}
		else
		{
		$year = false;
		}
	return $year;
	}
	
	private function returnYday($date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$date_year = $this->verifyDate($date);
		if ($date_year !== false)
		{
		$yday = $date_year->format('z');
		}
		else
		{
		$yday = false;
		}
	return $yday;
	}

	private function returnMonth($date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$date_month = $this->verifyDate($date);
		if ($date_month !== false)
		{
		$month = $date_month->format('m');
		}
		else
		{
		$month = false;
		}
	return $month;
	}

	private function verifyDate($supplied_date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Supplied Date: $supplied_date");
	
	$result = true;
		try
		{
		$date = new DateTime($supplied_date);
		} 
		catch (Exception $e) 
		{
		$results = $e->getMessage();
		return FALSE;
		}
		
	return $date;
	}

	private function orderDates($dateOne, $dateTwo)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$dateOne_year = $this->returnYear($dateOne);
	$dateTwo_year = $this->returnYear($dateTwo);
		if ($dateOne_year === false || $dateTwo_year === false)
		{
		return false;
		}

	$dateOne_yday = $this->numberOfDays($dateOne, true);
	$dateTwo_yday = $this->numberOfDays($dateTwo, true);

		switch ($dateOne_year)
		{
			case ($dateOne_year > $dateTwo_year): //later year is the end date 
				$this->errorLogging->logInfo(__CLASS__, __METHOD__, "DateOne Year > DateTwo Year : $dateOne_year :: $dateTwo_year.");
				$end_date = $dateOne;
				$start_date = $dateTwo;
				break;
			
			case ($dateTwo_year > $dateOne_year): //later year is the end date
				$start_date = $dateOne;
				$end_date = $dateTwo;
				break;

			case ($dateTwo_year = $dateOne_year): //years are the same, go by the yday of the year
					if ($dateOne_yday > $dateTwo_yday)
					{
					$this->errorLogging->logInfo(__CLASS__, __METHOD__, "DateOne Yday > DateTwo Yday : $dateOne_yday :: $dateTwo_yday.");
					$end_date = $dateOne;
					$start_date = $dateTwo;
					}
					elseif ($dateOne_yday < $dateTwo_yday)
					{
					$start_date = $dateOne;
					$end_date = $dateTwo;
					}
					elseif ($dateOne_yday == $dateTwo_yday)
					{
					$start_date = $dateOne;
					$end_date = $dateTwo;
					}
				break;
		}

	$this->startDate = $this->dateReturnFormat($start_date);
	$this->endDate = $this->dateReturnFormat($end_date); //= $end_date;

	return true;
	}

	private function dateReturnFormat($date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$pre_formated_date = $this->verifyDate($date);
		if ($pre_formated_date === false)
		{
		return false;
		}
	$formatted_date = $pre_formated_date->format('Y-m-d');
	return $formatted_date;
	}

	private function monthYearDateFormat($date)
	{
	$this->errorLogging->logInfo(__CLASS__, __METHOD__, "Called.");
	
	$pre_formated_date = $this->verifyDate($date);
		if ($pre_formated_date === false)
		{
		return false;
		}
	$formatted_date = $pre_formated_date->format('m/d/Y');
	return $formatted_date;
	}		
}
?>

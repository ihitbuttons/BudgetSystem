//calendar budget
<?php
/*
	$listTransactions_array = array("fromAccount" => "int", "dateOne" => "date", "dateTwo" => "date"); //*
	$listTransactionsCategory_array = array("category" => "int", "dateOne" => "date", "dateTwo" => "date"); //*
	$listTransactionsPosted_array = array("fromAccount", "dateOne", "dateTwo"); //*
*/

$apiCall = "listTransactions";
$apiArgument_array = array("accountPK" => "$account", "categoryPK" => "$category", "posted" => "$posted", "active" => "$active", "startDate" =>"$startDate", "endDate" => "$endDate");

$arguments = "'"; 

	foreach ($apiArgument_array as $argumentName => $argumentValue) 
	{
	$arguments = $arguments . $argumentName . "', '" . $argumentValue . "', '"; 
	} 

$arguments = substr($arguments, 0, -3);			
	
$listenerArray = array(
	"$apiCall" => "nDaysPattern",
	"getDateCurrent" => "getDateCurrent",
	"listAccountIds" => "listAccountIds",
	"listCategories" => "listCategories"	
);


$initialCallsArray = array(
	"getDateCurrent" => "datesAPI",
	"listAccountIds" => "budgetAPI",
	"listCategories" => "budgetAPI"
);
	
?>
$scope.displayOptions = false;
$scope.loggedIN = true;
<?php
	foreach($listenerArray as $listerName => $listernValue)
	{
	echo '$scope.' . $listernValue . "Returned = []; \n";		
	}

	foreach($listenerArray as $listerName => $listernValue)
	{
	echo '$scope' . ".registerAPIListener('" . $listerName . "','" . $listernValue . "Returned'); \n";		
	}

?>

	//check for Logged In Condition
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.apiProcessing == "complete" && ($scope.apiLastCall == "checkForSession" || $scope.apiLastCall == "login" || $scope.apiLastCall == "loginByPin") && $scope.sessionLoggedIn == "Logged In" && $scope.loggedIN == true) 
		{
<?php
	foreach($initialCallsArray as $initialAPICall => $initialAPICalled)
	{
	echo '		$scope' . ".apicall('" . $initialAPICalled . "', '" . $initialAPICall . "'); \n";	
	}
?>
		$scope.apicall('budgetAPI', '<?php echo $apiCall; ?>', <?php echo $arguments; ?>);
		$scope.loggedIN = false;
		$scope.intervalCheckSession = $interval(function(){$scope.apicall('sessionAPI', 'checkSession');}, 30000);
		$scope.intervalListTransactions = $interval(function(){$scope.apicall('budgetAPI', '<?php echo $apiCall; ?>', <?php $arguments = "'"; foreach ($apiArgument_array as $argumentName => $argumentValue) {$arguments = $arguments . $argumentName . "', '" . $argumentValue . "', '"; } $arguments = substr($arguments, 0, -3); echo $arguments; ?>);}, 15000);
		$interval.cancel($scope.intervalCheckForSession);
		}
	});
	
	//Check for Logged Out Condition
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.apiProcessing == "complete" && ($scope.apiLastCall != "checkForSession" || $scope.apiLastCall != "login" || $scope.apiLastCall != "loginByPin") && $scope.sessionLoggedIn == "Logged Out" && $scope.loggedIN == false) 
		{
		$scope.loggedIN = true;
		$interval.cancel($scope.intervalCheckSession);
		$interval.cancel($scope.intervalListTransactions);
		$scope.intervalCheckForSession = $interval(function(){$scope.apicall('sessionAPI', 'checkForSession');}, 30000);
		}
	});		
	
	$scope.optionsDisplay = function(displayValue)
	{
		$scope.displayOptions = displayValue;		
	}
	
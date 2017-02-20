//line item budget
<?php
	if(!$category)
	{
	$apiCall = "listTransactions";
	$apiArgument = "fromAccount";
	$apiVariable = $account;
	}
	else
	{
	$apiCall = "listTransactionsCategory";
	$apiArgument = "category";
	$apiVariable = $category;		
	}
?>



$scope.nDaysPatternReturned = [];
$scope.getDateCurrentReturned = [];
$scope.listAccountIdsReturned = [];
$scope.listCategoryIdsReturned = [];

$scope.postedReturned = 'true';
$scope.displayOptions = 'false';

$scope.listTransactionsLongPoll = "none";

$scope.loggedIN = true;

	$scope.registerAPIListener('<?php echo $apiCall; ?>', 'nDaysPatternReturned');	
	$scope.registerAPIListener('listTransactionsPosted', 'nDaysPatternReturned');
	$scope.registerAPIListener('getDateCurrent', 'getDateCurrentReturned');
	$scope.registerAPIListener('listAccountIds', 'listAccountIdsReturned');	
	$scope.registerAPIListener('listCategories', 'listCategoryIdsReturned');
		
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.apiProcessing == "complete" && ($scope.apiLastCall == "checkForSession" || $scope.apiLastCall == "login" ) && $scope.loggedIn != "Logged Out") 
		{
		$scope.longPollListener('longPollerAPI', 'registerListener', 'api', 'BudgetClass', 'apiCall', '<?php echo $apiCall; ?>');
		$scope.apicall('datesAPI', 'getDateCurrent');
		$scope.apicall('budgetAPI', 'listAccountIds');
		$scope.apicall('budgetAPI', 'listCategories');
		$scope.apicall('budgetAPI', '<?php echo $apiCall; ?>', '<?php echo $apiArgument; ?>', <?php echo $apiVariable; ?>, 'dateOne', '<?php echo $startDate; ?>', 'dateTwo', '<?php echo $endDate; ?>');
		$scope.loggedIN = false;
		$scope.intervalCheckSession = $interval(function(){$scope.apicall('sessionAPI', 'checkSession', 'extend', false);}, 10000);
		$scope.intervalListTransactions = $interval(function(){$scope.apicall('budgetAPI', '<?php echo $apiCall; ?>', '<?php echo $apiArgument; ?>', <?php echo $apiVariable; ?>, 'dateOne', '<?php echo $startDate; ?>', 'dateTwo', '<?php echo $endDate; ?>');}, 15000);
		$interval.cancel($scope.intervalCheckForSession);
		}
	});
	
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.apiProcessing == "complete" && $scope.apiLastCall == "logOutSession") 
		{
		$scope.loggedIN = true;
		$interval.cancel($scope.intervalCheckSession);
		$interval.cancel($scope.intervalListTransactions);
		$interval.cancel($scope.intervalListTransactionsPosted);
		$scope.intervalCheckForSession = $interval(function(){$scope.apicall('sessionAPI', 'checkForSession');}, 10000);
		}
	});
		
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.apiProcessing == "complete" && $scope.apiLastCall == "checkSession") 
		{
			if($scope.loggedIn == "Invalid")
			{
			$scope.loggedIN = true;
			$interval.cancel($scope.intervalCheckSession);
			$scope.intervalCheckForSession = $interval(function(){$scope.apicall('sessionAPI', 'checkForSession');}, 10000);
			}
		}
	});
	
	$scope.$watch("postedReturned", function(newValue, oldValue) 
	{
		if (newValue == "false" && oldValue == "true" && $scope.loggedIn != "Logged Out") 
		{
		$interval.cancel($scope.intervalListTransactions);
		$scope.apicall('budgetAPI', 'listTransactionsPosted', '<?php echo $apiArgument; ?>', <?php echo $apiVariable; ?>, 'dateOne', '<?php echo $startDate; ?>', 'dateTwo', '<?php echo $endDate; ?>');
		$scope.intervalListTransactionsPosted = $interval(function(){$scope.apicall('budgetAPI', 'listTransactionsPosted', '<?php echo $apiArgument; ?>', <?php echo $apiVariable; ?>, 'dateOne', '<?php echo $startDate; ?>', 'dateTwo', '<?php echo $endDate; ?>');}, 15000);
		}
		
		if (newValue == "true" && oldValue == "false" && $scope.loggedIn != "Logged Out") 
		{
		$interval.cancel($scope.intervalListTransactionsPosted);
		$scope.apicall('budgetAPI', '<?php echo $apiCall; ?>', '<?php echo $apiArgument; ?>', <?php echo $apiVariable; ?>, 'dateOne', '<?php echo $startDate; ?>', 'dateTwo', '<?php echo $endDate; ?>');
		$scope.intervalListTransactions = $interval(function(){$scope.apicall('budgetAPI', '<?php echo $apiCall; ?>', '<?php echo $apiArgument; ?>', <?php echo $apiVariable; ?>, 'dateOne', '<?php echo $startDate; ?>', 'dateTwo', '<?php echo $endDate; ?>');}, 15000);
		}
	});	
	
	/*
	$scope.$watch("listTransactionsLongPoll", function(newValue, oldValue) 
	{		
		if ($scope.postedReturned == "false") 
		{
		$scope.apicall('budgetAPI', 'listTransactionsPosted', '<?php echo $apiArgument; ?>', <?php echo $account; ?>, 'dateOne', '<?php echo $startDate; ?>', 'dateTwo', '<?php echo $endDate; ?>');
		}
		
		if ($scope.postedReturned == "true") 
		{
		$scope.apicall('budgetAPI', 'listTransactions', '<?php echo $apiArgument; ?>', <?php echo $account; ?>, 'dateOne', '<?php echo $startDate; ?>', 'dateTwo', '<?php echo $endDate; ?>');
		}
	});
	*/
	
	$scope.changePosted = function(postedValue)
	{
		$scope.postedReturned = postedValue;
	}	
	
	$scope.optionsDisplay = function(displayValue)
	{
		$scope.displayOptions = displayValue;		
	}
	
	
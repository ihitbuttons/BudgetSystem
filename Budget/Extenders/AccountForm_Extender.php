//accounts form
$scope.loggedIN = true;
$scope.accountTypePK = "<?php echo $accountTypePK; ?>";
$scope.accountAdd = true;

<?php
/*
$scope.listAccountIdsReturned = [];
$scope.listAccountTypesReturned = [];
$scope.accountDetailsReturned = [];

	$scope.registerAPIListener('listAccountIds', 'listAccountIdsReturned');	
	$scope.registerAPIListener('listAccountTypes', 'listAccountTypesReturned');
	$scope.registerAPIListener('accountDetails', 'accountDetailsReturned');
*/

$listenerArray = array(
	"accountDetails" => "accountDetails",
	"listAccountTypes" => "listAccountTypes",
	"listAccountIds" => "listAccountIds"
);

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
		$scope.apicall('budgetAPI', 'listAccountIds');
		$scope.apicall('budgetAPI', 'listAccountTypes');
		$scope.loggedIN = false;
		$scope.intervalCheckSession = $interval(function(){$scope.apicall('sessionAPI', 'checkSession', 'extend', false);}, 10000);
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
	};
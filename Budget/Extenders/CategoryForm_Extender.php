//categories form
$scope.loggedIN = true;
$scope.categoryTypePK = "<?php echo $categoryTypePK; ?>";
$scope.categoryAdd = true;

<?php
/*
$scope.listCategoriesReturned = [];
$scope.registerAPIListener('listCategories', 'listCategoriesReturned');	

*/

$listenerArray = array(
	"categoryDetails" => "categoryDetails",
	"listCategories" => "listCategories",
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
		$scope.apicall('budgetAPI', 'listCategories');
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
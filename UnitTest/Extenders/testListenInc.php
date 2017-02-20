$scope.listAccountIdsReturned = [];
$scope.accountDetailsReturned = [];
$scope.listTransactionTypesReturned = [];
$scope.listCategoriesReturned = [];
$scope.listPaymentMethodsReturned = [];
$scope.listPurchasersReturned = [];
$scope.listAccountsReturned = [];
$scope.listAccountAssocReturned = [];
$scope.nDaysPatternReturned = [];

$scope.loggedIN = true;
$scope.accountsTEST = false;
$scope.datesTEST = true;
$scope.transactionsTEST = false;
$scope.reoccuringtransactionTEST = false;

	$scope.registerAPIListener('listAccountIds', 'listAccountIdsReturned');	
	$scope.registerAPIListener('accountDetails', 'accountDetailsReturned');	
	$scope.registerAPIListener('listTransactionTypes', 'listTransactionTypesReturned');	
	$scope.registerAPIListener('listCategories', 'listCategoriesReturned');
	$scope.registerAPIListener('listPurchasers', 'listPurchasersReturned');
	$scope.registerAPIListener('listPaymentMethods', 'listPaymentMethodsReturned');
	$scope.registerAPIListener('listAccounts', 'listAccountsReturned');	
	$scope.registerAPIListener('listAccountAssoc', 'listAccountAssocReturned');	
	$scope.registerAPIListener('nDaysPatternPadded', 'nDaysPatternReturned');	
	$scope.registerAPIListener('nDaysPattern', 'nDaysPatternReturned');	
	$scope.registerAPIListener('listTransactions', 'nDaysPatternReturned');	

	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.apiProcessing == "complete" && ($scope.apiLastCall == "checkForSession" || $scope.apiLastCall == "login" ) && $scope.sessiontoken != "none" && $scope.sessiontoken != "Invalid") 
		{
		$scope.apicall('budgetAPI', 'listAccountIds');
		$scope.apicall('datesAPI', 'nDaysPatternPadded', 'dateOne', '11/01/2014', 'dateTwo', '11/01/2014', 'nDays', 1, 'operator', 1);
		$scope.apicall('budgetAPI', 'listTransactionTypes');
		$scope.apicall('budgetAPI', 'listCategories');
		$scope.apicall('budgetAPI', 'listPaymentMethods', 'accountPK', 0);
		$scope.apicall('budgetAPI', 'listPurchasers');
		$scope.apicall('budgetAPI', 'listTransactions', 'fromAccount', 2, 'dateOne', '11/01/2014', 'dateTwo', '1/01/2015');
		$scope.loggedIN = false;
		$scope.intervalCheckSession = $interval(function(){$scope.apicall('sessionAPI', 'checkSession', 'extend', false);}, 10000);
		$interval.cancel($scope.intervalCheckForSession);
		}
	});
	
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.apiProcessing == "complete" && $scope.apiLastCall == "logOutSession") 
		{
		$scope.loggedIN = true;
		$interval.cancel($scope.intervalCheckSession);
		$scope.intervalCheckForSession = $interval(function(){$scope.apicall('sessionAPI', 'checkForSession');}, 10000);
		}
	});
		
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.apiProcessing == "complete" && $scope.apiLastCall == "checkSession") 
		{
			if($scope.sessiontoken == "Invalid")
			{
			$scope.loggedIN = true;
			$interval.cancel($scope.intervalCheckSession);
			$scope.intervalCheckForSession = $interval(function(){$scope.apicall('sessionAPI', 'checkForSession');}, 10000);
			}
		}
	});
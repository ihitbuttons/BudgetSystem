//transaction form
$scope.listAccountIdsReturned = [];
$scope.listTransactionTypesReturned = [];
$scope.listCategoriesReturned = [];
$scope.listPaymentMethodsReturned = [];
$scope.listPurchasersReturned = [];
$scope.listAccountsReturned = [];
$scope.postedTransactionValue = 0;

$scope.$watch("apiProcessing", function(newValue, oldValue) 
{
	if ($scope.apiProcessing == "complete" && $scope.apiLastCall == "transactionDetails") 
	{
	$scope.modifiedDateValue = $scope.apiResponse["0"]["modifiedDate"];
	$scope.amountValue = $scope.apiResponse["0"]["amount"];
	$scope.selectFromAccountValue = $scope.apiResponse["0"]["fromAccountPK"];
	$scope.selectToAccountValue = $scope.apiResponse["0"]["toAccountPK"];
	$scope.selectPaymentMethodValue = $scope.apiResponse["0"]["paymentMethodPK"];
	$scope.selectPurchaserValue = $scope.apiResponse["0"]["purchaserPK"];
	$scope.selectCategoryValue = $scope.apiResponse["0"]["categoryPK"];
	$scope.postedValue = $scope.apiResponse["0"]["posted"];
	$scope.postedDateValue = $scope.apiResponse["0"]["postedDate"];
	$scope.descriptionValue = $scope.apiResponse["0"]["description"];
	$scope.activeValue = $scope.apiResponse["0"]["active"];
	}
});	

$scope.transactionPKValue = "<?php echo $transactionPK; ?>";
$scope.displayOptions = false;
$scope.loggedIN = true;
$scope.postedValue = 0;
$scope.postedDateValue = "<?php echo $date; ?>";

	$scope.registerAPIListener('listAccountIds', 'listAccountIdsReturned');	
	$scope.registerAPIListener('listTransactionTypes', 'listTransactionTypesReturned');	
	$scope.registerAPIListener('listCategories', 'listCategoriesReturned');
	$scope.registerAPIListener('listPurchasers', 'listPurchasersReturned');
	$scope.registerAPIListener('listPaymentMethods', 'listPaymentMethodsReturned');
	$scope.registerAPIListener('listAccounts', 'listAccountsReturned');	

	//check for Logged In Condition
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.apiProcessing == "complete" && ($scope.apiLastCall == "checkForSession" || $scope.apiLastCall == "login" || $scope.apiLastCall == "loginByPin") && $scope.sessionLoggedIn == "Logged In" && $scope.loggedIN == true) 
		{
		$scope.apicall('budgetAPI', 'listAccountIds');
		$scope.apicall('budgetAPI', 'listTransactionTypes');
		$scope.apicall('budgetAPI', 'listCategories');
		$scope.apicall('budgetAPI', 'listPurchasers');
		$scope.apicall('budgetAPI', 'listPaymentMethods', 'accountPK', 0);		
		$scope.loggedIN = false;
		$scope.intervalCheckSession = $interval(function(){$scope.apicall('sessionAPI', 'checkSession');}, 30000);
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
		$scope.intervalCheckForSession = $interval(function(){$scope.apicall('sessionAPI', 'checkForSession');}, 30000);
		}
	});		
	
	$scope.optionsDisplay = function(displayValue)
	{
		$scope.displayOptions = displayValue;		
	}

$scope.apicall('sessionAPI', 'checkForSession');

	if($scope.transactionPKValue != "new" && $scope.transactionPKValue != "reoccurring")
	{
	$scope.apicall('budgetAPI', 'transactionDetails', 'transactionPK', $scope.transactionPKValue);
	}
	
	if($scope.transactionPKValue == "new" || $scope.transactionPKValue == "reoccurring")
	{
	$scope.modifiedDateValue = "<?php echo $date; ?>";
	$scope.selectFromAccountValue = "<?php echo $account; ?>";	
	$scope.selectTransactionTypePK = "2";
	}	
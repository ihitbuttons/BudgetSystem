//generic test form
$scope.loggedIN = true;
$scope.displayMethods = true;
$scope.displayOptions = false;
$scope.calledMethod = false;
$scope.formList = [];
$scope.listMethodsReturned = [];

$scope.registerAPIListener('argumentList', 'listMethodsReturned');

$scope.ListMethodInputsReturned = [];
$scope.registerAPIListener('listMethodInputs', 'ListMethodInputsReturned');

	$scope.restFormlist = function(methodName)
	{

	$scope.formList = [];
	$scope.formList.length = 0;
	}
	
	$scope.addFormList = function(inputName)
	{
		if($scope.formList.length > 0)
		{
		var c = $scope.formList.length + 1;
		}
		else
		{
		var c = 0;	
		}
	$scope.formList.splice(c, 0, inputName);
	//console.log(inputName);
	}
	
	$scope.dynamicAPICall = function(apiCall, methodName)
	{
	methodName = methodName.substring(0, methodName.length - 6);
	
	$scope.apiProcessing = "processing";
	$scope.ProcessingStart = Math.round(new Date().getTime() / 1000);
	
		var apiArguments = {
		sessionToken : $scope.sessionToken
		};

	random=Math.floor(Math.random()*1000);

	$scope.apiselect = apiCall;
	$scope.apiaction = methodName;
	apiArguments["sessionToken"] = $scope.sessionToken;
	$scope.random = random;

		if($scope.formList.length > 0)
		{
			for (var i = 0; i < $scope.formList.length; i++)
			{
			//console.log($scope.formList[i]);
			apiArguments[$scope.formList[i]] = $scope.loadById($scope.formList[i]);
			}
		}
		
		if($scope.formList.length == 1)
		{
		apiArguments[$scope.formList[0]] = $scope.loadById($scope.formList[0]);	
		}
		
			$http.post('<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/API/' + $scope.apiselect +'.php?action=' + $scope.apiaction + '&random=' + random, apiArguments, {timeout : <?php echo JAVASCRIPT_HTTP_TIMEOUT; ?>}).
			success(function(data, status, headers, config)
			{
				if (data != "false")
				{
					if ($scope.debug === true)
					{
					console.log(data);
					}
					
					if (data["session"].response == "Logged In")
					{
					$scope.sessionToken = data["session"].sessionID;
					}
					
					$scope.sessionLoggedIn = data["session"].response;
					
					if(data["error"].error == "False")
					{					
					$scope.testAPICall = data["apiResponse"].response;
					}
					else
					{
					$scope.apiError = data["error"].response;	
					}
				}
			$scope.apiProcessing = "complete";
			$scope.apiLastCall = $scope.apiaction;
			$scope.processRequests();
			}).
			error(function(data, status, headers, config)
			{
			$scope.apiError = "Last Request Failed";
			//timeouts should end up here along with status 500's
			$scope.apiProcessing = "complete";
			$scope.apiLastCall = $scope.apiaction + ' FAILED';
			console.log($scope.apiaction + ' Failed (status 500 or timeout)');
			$scope.processRequests();
			});
	};
  


	//check for Logged In Condition
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.apiProcessing == "complete" && ($scope.apiLastCall == "checkForSession" || $scope.apiLastCall == "login" || $scope.apiLastCall == "loginByPin") && $scope.sessionLoggedIn == "Logged In" && $scope.loggedIN == true) 
		{
		$scope.loggedIN = false;
		$interval.cancel($scope.intervalCheckForSession);
		$scope.intervalCheckSession = $interval(function(){$scope.apicall('sessionAPI', 'checkSession', 'extend', false);}, 10000);		
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
	};
	
	$scope.listMethodsDisplay = function(displayValue)
	{
		$scope.displayMethods = displayValue;		
	};
$scope.pollMessagesReturned = [];
$scope.chatid = "";
$scope.deviceid = "";

$scope.loggedIN = true;

	$scope.registerAPIListener('pollMessages', 'pollMessagesReturned');
	$scope.registerAPIListener('generateDeviceID', 'deviceid');

	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.debug === true)
		{
		console.log('check 1');
		}
		if ($scope.apiProcessing == "complete" && ($scope.apiLastCall == "checkForSession" || $scope.apiLastCall == "login" ) && $scope.sessiontoken != "none" && $scope.sessiontoken != "Invalid") 
		{
		$scope.loggedIN = false;
		$scope.intervalCheckSession = $interval(function(){$scope.apicall('sessionAPI', 'checkSession', 'extend', false);}, 10000);
		$interval.cancel($scope.intervalCheckForSession);
		}
	});
	
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.debug === true)
		{
		console.log('check 2');
		}
		if ($scope.apiProcessing == "complete" && $scope.apiLastCall == "logOutSession") 
		{
		$scope.loggedIN = true;
		$interval.cancel($scope.intervalCheckSession);
		$scope.intervalCheckForSession = $interval(function(){$scope.apicall('sessionAPI', 'checkForSession');}, 10000);
		}
	});
		
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.debug === true)
		{
		console.log('check 3');
		}
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
	
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.debug === true)
		{
		console.log('check 4');
		}
		if ($scope.apiProcessing == "complete" && $scope.apiLastCall == "postMessage") 
		{
		$scope.messageid = $scope.apiResponse;
		$scope.apicall('chatAPI', 'pollMessages', 'chatid', $scope.loadById('chatid'), 'messageid', $scope.loadById('messageid'), 'password', $scope.loadById('password'), 'deviceid', $scope.loadById('deviceid'));
		}
	});
	
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.debug === true)
		{
		console.log('check 5');
		}
		if ($scope.apiProcessing == "complete" && $scope.apiLastCall == "joinChat") 
		{
		$scope.messageid = $scope.apiResponse;
		$scope.apicall('chatAPI', 'pollMessages', 'chatid', $scope.loadById('chatid'), 'messageid', $scope.loadById('messageid'), 'password', $scope.loadById('password'), 'deviceid', $scope.loadById('deviceid'));
		$scope.intervalCheckForMessages = $interval(function(){$scope.apicall('chatAPI', 'pollMessages', 'chatid', $scope.loadById('chatid'), 'messageid', $scope.loadById('messageid'), 'password', $scope.loadById('password'), 'deviceid', $scope.loadById('deviceid'));}, 60000);
		
		$scope.listencall('longPollerAPI', 'chatListner', 'chatid', $scope.loadById('chatid'), 'password', $scope.loadById('password'), 'deviceid', $scope.loadById('deviceid'));
		}
	});
	
	$scope.$watch("apiProcessing", function(newValue, oldValue) 
	{
		if ($scope.debug === true)
		{
		console.log('check 6');
		}
		if ($scope.apiProcessing == "complete" && $scope.apiLastCall == "registerNewChat") 
		{
		$scope.chatid = $scope.apiResponse;
		$scope.intervalCheckForMessages = $interval(function(){$scope.apicall('chatAPI', 'pollMessages', 'chatid', $scope.loadById('chatid'), 'messageid', $scope.loadById('messageid'), 'password', $scope.loadById('password'), 'deviceid', $scope.loadById('deviceid'));}, 60000);
		
		$scope.listencall('longPollerAPI', 'chatListner', 'chatid', $scope.chatid, 'password', $scope.loadById('password'), 'deviceid', $scope.loadById('deviceid'));
		}
	});
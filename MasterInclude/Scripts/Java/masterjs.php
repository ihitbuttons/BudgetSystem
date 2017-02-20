<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'master.inc.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'sanitizeINC.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'pageLoaderINC.php');

$sanitizeIt = SanitizeClass::getInstance();	
$_GET = $sanitizeIt->sanitizeValues($_GET);
$_COOKIE = $sanitizeIt->sanitizeValues($_COOKIE);
$_POST = $sanitizeIt->sanitizeValues($_POST);

	if (array_key_exists("page", $_GET))
	{
	$page = $_GET["page"];
	}
	else
	{
	$page = "default";
	}
	
$variableArray = PageLoaderClass::setVariables($page);
	
	if(is_array($variableArray))
	{
		foreach($variableArray as $variableName => $variableValue)
		{
		${"$variableName"} = $variableValue;
		}
	}
?>
/* Initialize APP */
var app = angular.module("MyApp", [], function($httpProvider) 
{
/* More compatible HTTP provider */
$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
	var param = function(obj) 
	{
	var query = '', name, value, fullSubName, subName, subValue, innerObj, i;
		for(name in obj) 
		{
		value = obj[name];
			if(value instanceof Array) 
			{
				for(i=0; i<value.length; ++i) 
				{
				subValue = value[i];
				fullSubName = name + '[' + i + ']';
				innerObj = {};
				innerObj[fullSubName] = subValue;
				query += param(innerObj) + '&';
				}
			}
			else if(value instanceof Object) 
			{
				for(subName in value) 
				{
				subValue = value[subName];
				fullSubName = name + '[' + subName + ']';
				innerObj = {};
				innerObj[fullSubName] = subValue;
				query += param(innerObj) + '&';
				}
			}
			else if(value !== undefined && value !== null)
			{
			query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
			}
		}
	return query.length ? query.substr(0, query.length - 1) : query;
	};	
	$httpProvider.defaults.transformRequest = [function(data) 
	{
	return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
	}];
});

/* URL Parameter Function for the HTTP provider */
function getURLParameter(name)
{
  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
}

/* Main App controler */
app.controller("ApiCtrl", function($scope, $http, $interval, $q)
{
/* Set Variables */
/* API Responses */
$scope.sessionToken = "none";
$scope.sessionLoggedIn = "Logged Out";
$scope.apiResponse = "none";
$scope.apiError = "none";
$scope.apiError = "none";

/* Login Variables */
$scope.loginUName=''; 
$scope.loginPassword='';
		
/* Cookies */
$scope.myCookie = "123456a";

/* API Que */
$scope.apiLock = false;
$scope.apiQue = [];

/* API Requests*/
$scope.apiProcessing = "complete";
$scope.ProcessingStart = 0;
$scope.apiLastCall = "none";

/* API CAll Arguments*/
$scope.random = 0;
$scope.apiselect = "none";
$scope.apiaction = "none";

/* Calendar Interface Variables */
$scope.weekArray = [];

/* Debugging Flags */
$scope.debug = false;

	/* API Call Handler */
	$scope.apicall = function()
	{
		if ($scope.debug === true)
		{
		console.log('apicall');
		}
	//get all the arguments passed
	var args = Array.prototype.slice.call(arguments);

		//register a listener for the apiLock value
		var unregister = $scope.$watch("apiLock", function(newValue, oldValue)
		{	
			if ($scope.apiLock === false)
			{
			//it is unlocked, lock it, add the arguments to a que
			$scope.apiLock = true;

			//take the current depth value, set the arguments, then increment it by 1
			var queLength = $scope.apiQue.length;
			$scope.apiQue[queLength] = args;

			//unregister the listener, and unlock it
			unregister();
			$scope.apiLock = false;
			}		
		});	
	}
	
	/*Loads elements from the page*/
	$scope.loadById = function(elementID, clear)
	{
		if ($scope.debug === true)
		{
		console.log('loadById');
		}
		
	var elementValue = document.getElementById(elementID).value;
	
		if (clear === true)
		{
		document.getElementById(elementID).value = "";
		}
	return elementValue;
	};
	
	/* Main API Call Thread*/
	$scope.apicallRequest = function(apiArgs)
	{
		if ($scope.debug === true)
		{
		console.log('apicallRequest');
		}
	$scope.apiProcessing = "processing";
	$scope.ProcessingStart = Math.round(new Date().getTime() / 1000);
		var apiArguments = {
		sessionToken : $scope.sessionToken
		};

	random=Math.floor(Math.random()*1000);

	$scope.apiselect = apiArgs[0];
	$scope.apiaction = apiArgs[1];
	apiArguments["sessionToken"] = $scope.sessionToken;
	$scope.random = random;

			for (var i = 2; i < apiArgs.length; i++)
			{
				if (i % 2)
				{
				apiArguments[fieldValue] = apiArgs[i];
				}
				else
				{
				fieldValue = apiArgs[i];
				}
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
					$scope.apiResponse = data["apiResponse"].response;
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

	/* API Que Handler */ 
	$scope.processRequests = function()
	{
		if ($scope.debug === true)
		{
		console.log('processRequests');
		console.log($scope.sessionToken);
		}
		//register a listener for the apilock value
		var unregisterA = $scope.$watch("apiLock", function(newValue, oldValue)
		{
			if ($scope.apiLock === false && $scope.apiQue.length > 0)
			{
			//it is unlocked, lock it, execute the first item in the que and unregister the listener
			$scope.apiLock = true;
				for (var i = 0; $scope.apiQue.length; i++) 
				{
				//console.log($scope.apiQue);
				$scope.apicallRequest($scope.apiQue[i]);
				$scope.shiftQue(i);
				break;
				};			
			unregisterA();
			}		
		});
	}

	$scope.shiftQue = function(i)
	{
		if ($scope.debug === true)
		{
		console.log('shiftQue');
		}
		//register a listener for the api call complete check
		var unregisterB = $scope.$watch("apiProcessing", function(newValue, oldValue)
		{
			if ($scope.apiProcessing == "complete")
			{
			$scope.apiQue.splice(i, 1);
			$scope.apiLock = false;
			unregisterB();
			}
		});	

	}
	
	/* Listener Handlers */
	$scope.registerAPIListener = function(apiCall, scopeVariable)
	{
		if ($scope.debug === true)
		{
		console.log('registerAPIListener');
		}
		
		$scope.$watch("apiProcessing", function(newValue, oldValue) 
		{
			if ($scope.apiProcessing == "complete" && $scope.apiLastCall == apiCall) 
			{
			//console.log('hit on return');
			//console.log($scope.apiResponse);	
			$scope[scopeVariable] = $scope.apiResponse;
			}
		});	
	}
	
	$scope.isSelected = function(item, comparedTo)
	{
		if(item == comparedTo)
		{
		return true;
		}
		else
		{
		return false;	
		}		
	}	

    $scope.onKeyDown = function ($event) 
	{
		if(window.event.keyCode == 13)
		{
		$scope.apicall('sessionAPI', 'login', 'uname', $scope.loadById('uname'), 'pword', $scope.loadById('pword'));	
		$scope.loginUName=''; 
		$scope.loginPassword='';
		}
    }	
	
	/* Listner Includes */
<?php 
require_once(PageLoaderClass::loadJavascriptExtenders($system, $page));
?>

	$scope.apicall('sessionAPI', 'checkForSession');
	$scope.processRequests();
	$interval(function(){$scope.processRequests();}, 1000);
});

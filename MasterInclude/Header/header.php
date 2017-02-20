<?php
	if(is_array($variableArray))
	{
		foreach($variableArray as $variableName => $variableValue)
		{
		${"$variableName"} = $variableValue;
		}
	}

$cookie_name = COOKIE_NAME;

	if(!array_key_exists($cookie_name, $_COOKIE))
	{
	$cookie_value = GenerateToken();
	setcookie($cookie_name, $cookie_value, time() + (86400 * COOKIE_EXPIRATION), "/",VAR_SERVER_IS, FALSE, FALSE);
	} 

	function GenerateToken()
	{
	$alphabet_array = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
			
	$countto = 16;
	$count = 0;
	$token = "";
	
		while ($count <> $countto)
		{
		$selection_rand = mt_rand(0, 3);
			switch($selection_rand)
			{
				case 3:
					$selected_rand = mt_rand(0, 9);
					break;
				
				case 2:
					$alpha_rand = mt_rand(0, 25);
					$lower_rand = $alphabet_array["$alpha_rand"];
					$selected_rand = ucfirst($lower_rand);
					break;
												
				default:
					$alpha_rand = mt_rand(0, 25);
					$selected_rand = $alphabet_array["$alpha_rand"];
					break;			
			}
		
		$token = $token . $selected_rand;
		$count++;
		}
		
	return $token;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:ng="<?php echo HTTP; ?>//angularjs.org" id="ng-app" ng-app="MyApp">
	<head id="Head" runat="server">
	<!--[if lte IE 8]>
      <script>
        document.createElement('ng-include');
        document.createElement('ng-pluralize');
        document.createElement('ng-view');

        // Optionally these for CSS
        document.createElement('ng:include');
        document.createElement('ng:pluralize');
        document.createElement('ng:view');
      </script>
    <![endif]-->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
		<link rel="stylesheet" type="text/css" href="<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/MasterInclude/CSS/master.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/MasterInclude/CSS/calendar.css" />

		<script src="<?php echo HTTP; ?>//ajax.googleapis.com/ajax/libs/angularjs/1.2.9/angular.js"></script>
		<script src="<?php echo HTTP; ?>//ajax.googleapis.com/ajax/libs/angularjs/1.2.9/angular-resource.min.js"></script>
		<script src="<?php echo HTTP; ?>//ajax.googleapis.com/ajax/libs/angularjs/1.2.9/angular-route.min.js"></script>
		<script src="<?php echo HTTP; ?>//ajax.googleapis.com/ajax/libs/angularjs/1.2.9/angular-sanitize.min.js"></script>
		<script src="<?php echo HTTP; ?>//ajax.googleapis.com/ajax/libs/angularjs/1.2.9/angular-animate.js"></script>
		<script src="<?php echo HTTP; ?>//ajax.googleapis.com/ajax/libs/angularjs/1.2.9/angular-mocks.js"></script>
		<script src="<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/MasterInclude/Scripts/Java/masterjs.php?page=<?php echo $page; echo $argumentString; ?>"></script>
		<script src="<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/MasterInclude/Scripts/Java/calendar_usaA.js"></script>
	</head>
	
	<div ng-controller="ApiCtrl">
		<div id='headerLeft'>
			<button ng-click="optionsDisplay('false')" ng-show="displayOptions">Hide Options</button>
			<button ng-click="optionsDisplay('true')" ng-hide="displayOptions">Show Options</button>
		</div>	
		
		<div id='header'>
		</div>

		<div id='processing'>
			<img ng-src="<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/MasterInclude/img/{{apiProcessing}}.png"></img>
			<p>{{ apiError }}</p>
		</div>
		
		<body>
			<div id='container'>		
				<div id='LOGINTEST' ng-show="loggedIN">
					<p>Login / Session Test:</p>
					
					<p>Username:</p>
					<input type='text' id='uname' name='uname' ng-model="loginUName"></input>
					
					<p>Password:</p>
					<input type='password' id='pword' name='pword' ng-keydown="onKeyDown($event)" ng-model="loginPassword"></input>
					<br/><br/>
					<button ng-click="apicall('sessionAPI', 'login', 'uname', loadById('uname'), 'pword', loadById('pword')); loginUName=''; loginPassword='';">Login</button>
					<br/>
					<button ng-click="apicall('sessionAPI', 'loginByPin', 'pin', loadById('pword')); loginUName=''; loginPassword='';">Login by PIN</button>
					<br/>
				</div>
				
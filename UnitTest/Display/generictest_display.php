<div id='TEST' ng-hide="loggedIN">
	<p>GENERIC TEST DISPLAY</p>
	<input type='text' id='apiCall' name='apiCall' ng-model="apiInput"></input>

	<div id='listMethods'>
		<button ng-click="apicall(loadById('apiCall'), 'argumentList'); listMethodsDisplay('true'); $scope.testAPICall = [];">
		List Methods
		</button>
	</div>

	<div id='listMethodsDisplay' ng-show="displayMethods">
		<select id='methodSelect' ng-model="methodSelect">
			<option ng-repeat="(methodName, methodDetails) in listMethodsReturned" value="{{ listMethodsReturned.methodName }}">
			{{ listMethodsReturned.methodName }}
			</option>
		</select>		
	<button  ng-click="apicall('unittestAPI', 'listMethodInputs', 'api', apiInput, 'action', methodSelect); listMethodsDisplay('false');">Use Action</button>	
	</div>

	<div id='ListMethodInputs' ng-hide="displayMethods">
		<form name='apiCallForm' id='apiCallForm' ng-submit="dynamicAPICall(apiInput, methodSelect)">
			<p ng-repeat="(argumentNames, argumentTypes) in ListMethodInputsReturned" ng-init='formListIndex=addFormList(argumentNames)'>
			{{ argumentNames }}
			<br/>
			<input type='text' name='{{ argumentNames }}' id='{{ argumentNames }}' value='{{ argumentTypes }}' ng></input>
			<br/>
			</p>
			
			<button name="submitAPICall" id="submitAPICall" >Make API Call</button>
		</form>
	</div>
	
	<div id='apiCallReturned' ng-hide="displayMethods">
	<p> {{ testAPICall }} </p>	
	</div>
	
</div>
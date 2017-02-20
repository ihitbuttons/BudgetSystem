				<center>
				<button ng-click="apicall('budgetAPI', 'listAccountIds')">Refresh Accounts</button><br/>
				<br/><br/>
				<select id='accountDetailsDD' ng-model="selectedItem">
					<option ng-repeat="listAccountIdsItem in listAccountIdsReturned" value="{{ listAccountIdsItem.accountPK }}">
					{{ listAccountIdsItem.accountName }}
					</option>
				</select>
				<br/><br/>
				<button ng-click="apicall('budgetAPI', 'accountDetails', 'accountPK', loadById('accountDetailsDD')); accountAdd='false';">Account Details</button>
				<br/><br/>
				</center>
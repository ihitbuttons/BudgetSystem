<?php
//Need a default 'start date' value
//need a default account value
//

	switch($transactionPK)
	{
		case (is_numeric($transactionPK)):
			$formName = "Modify Transaction";
			break;
			
		case "new":
			$formName = "Add New Transaction";
			break;
		
		case "reoccurring":
			$formName = "Add Reoccurring Transaction";
			break;		
	}
?>

<!-- TransactionForm_Display -->
				<div id='TransactionForm' ng-hide="loggedIN">
				<center>
				
				<p><?php echo $formName; ?>:</p>
<?php
	switch($transactionPK)
	{
		case (is_numeric($transactionPK)):
				$account = "selectFromAccountValue";
?>
				<form name='selectModifiedDate' id='selectModifiedDate'>
				
				<p>Transaction Date:</p>
				<input type='text' id='modifiedDate' name='modifiedDate' ng-model="modifiedDateValue"></input>
				<script language="JavaScript">
				var o_cal = new tcal 
				({
				'formname': 'selectModifiedDate',
				'controlname': 'modifiedDate'
				});
				o_cal.a_tpl.yearscroll = false;
				o_cal.a_tpl.weekstart = 7;
				</script>
				
				</form>
<?php
			break;
			
		case "new":
?>
				<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=TransactionForm&transactionPK=reoccurring&date=<?php echo $date; ?>&account=<?php echo $account; ?>'>Reoccuring Transactions</a>
				<form name='selectModifiedDate' id='selectModifiedDate'>
				
				<p>Transaction Date:</p>
				<input type='text' id='modifiedDate' name='modifiedDate' ng-model="modifiedDateValue"></input>
				<script language="JavaScript">
				var o_cal = new tcal 
				({
				'formname': 'selectModifiedDate',
				'controlname': 'modifiedDate'
				});
				o_cal.a_tpl.yearscroll = false;
				o_cal.a_tpl.weekstart = 7;
				</script>
				
				</form>
<?php
			break;
		
		case "reoccurring":

?>
				<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=TransactionForm&transactionPK=new&date=<?php echo $date; ?>&account=<?php echo $account; ?>'>Single Transaction</a>
				<form name='selectModifiedDate' id='selectModifiedDate'>
				
				<p>Transaction Date:</p>
				<input type='text' id='modifiedDate' name='modifiedDate' ng-model="modifiedDateValue"></input>
				<script language="JavaScript">
				var o_cal = new tcal 
				({
				'formname': 'selectModifiedDate',
				'controlname': 'modifiedDate'
				});
				o_cal.a_tpl.yearscroll = false;
				o_cal.a_tpl.weekstart = 7;
				</script>
				
				</form>
				
				<form name='selectEndDate' id='selectEndDate'>
				
				<p>End Date:</p>
				<input type='text' id='endDate' name='endDate' ng-model="endDateValue"></input>
				<script language="JavaScript">
				var o_cal = new tcal 
				({
				'formname': 'selectEndDate',
				'controlname': 'endDate'
				});
				o_cal.a_tpl.yearscroll = false;
				o_cal.a_tpl.weekstart = 7;
				</script>
				
				</form>
				<!-- This should have a default Value -->
				<p>Reoccurring type:</p>
				<select id='transactionTypePK' ng-model="selectTransactionTypePK">
					<option ng-repeat="listTransactionTypesItem in listTransactionTypesReturned"  value="{{ listTransactionTypesItem.transactionTypePK }}"  ng-selected="isSelected(listTransactionTypesItem.transactionTypePK, 2)">{{ listTransactionTypesItem.typeName }}</option>
				</select>
				<br/><br/>
				
				
<?php
			break;		
	}
?>

				
				<p>Ammount:</p>
				<input type='text' id='transactionAmount' name='transactionAmount' ng-model="amountValue"></input>
				<br/><br/>
				
				<!-- This should have a default Value -->
				<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=AccountForm'>From Account:</a><br/>
				<select id='fromAccountDD' ng-model="selectFromAccountValue">
					<option ng-repeat="listAccountIdsItem in listAccountIdsReturned | orderBy:'accountName'"  value="{{ listAccountIdsItem.accountPK }}" ng-selected="isSelected(listAccountIdsItem.accountPK, <?php echo $account; ?>)">{{ listAccountIdsItem.accountName }}</option>
				</select>
				<br/><br/>
				
				<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=AccountForm'>To Account:</a><br/>
				<select id='toAccountDD' ng-model="selectToAccountValue" ng-change="apicall('budgetAPI', 'listPaymentMethods', 'accountPK', selectToAccountValue)">
					<option ng-repeat="listAccountIdsItem in listAccountIdsReturned | orderBy:'accountName'" value="{{ listAccountIdsItem.accountPK }}" ng-selected="isSelected(listAccountIdsItem.accountPK, selectToAccountValue)">{{ listAccountIdsItem.accountName }}</option>
				</select>
				<br/><br/>
								
				<p>Payment Method:</p>				
				<select id='paymentDD' ng-model="selectPaymentMethodValue">
					<option ng-repeat="listPaymentMethodsReturnedItem in listPaymentMethodsReturned | orderBy:'methodName'" value="{{ listPaymentMethodsReturnedItem.paymentMethodPK }}" ng-selected="isSelected(listPaymentMethodsReturnedItem.paymentMethodPK, selectPaymentMethodValue)">{{ listPaymentMethodsReturnedItem.methodName }}</option>
				</select>
				<br/><br/>
				
				<p>Purchaser:</p>				
				<select id='purchaserDD' ng-model="selectPurchaserValue">
					<option ng-repeat="listPurchasersReturnedItem in listPurchasersReturned | orderBy:'purchaserName'" value="{{ listPurchasersReturnedItem.purchaserPK }}" ng-selected="isSelected(listPurchasersReturnedItem.purchaserPK, selectPurchaserValue)">{{ listPurchasersReturnedItem.purchaserName }}</option>
				</select>
				<br/><br/>
				
				<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=CategoryForm'>Category:</a><br/>
				<select id='categoryDD' ng-model="selectCategoryValue">
					<option ng-repeat="listCategoriesReturnedItem in listCategoriesReturned | orderBy:'categoryName'" value="{{ listCategoriesReturnedItem.categoryPK }}" ng-selected="isSelected(listCategoriesReturnedItem.categoryPK, selectCategoryValue)">{{ listCategoriesReturnedItem.categoryName }}</option>
				</select>
				<br/><br/>
								
				<p>Posted:</p>
				<input type='checkbox' id='posted' ng-model="postedValue" ng-true-value="1" ng-false-value="0">
				<br/><br/>
				
				<input type='hidden' id='postedDate' name='postedDate' ng-model="postedDateValue"></input>
<?php
	if(is_numeric($transactionPK))
	{
?>
				<p>Active:</p>
				<input type='checkbox' id='active' ng-model="activeValue" ng-true-value="1" ng-false-value="0">
				<br/><br/>	
<?php
	}
?>				
				<p>Description:</p>
				<textarea id='descriptionTransaction' rows='4' cols='100' ng-model="descriptionValue"></textarea>
				<br/><br/>
<?php
	switch($transactionPK)
	{
		case (is_numeric($transactionPK)):
?>
<button ng-click="apicall('budgetAPI', 'modifyTransaction', 'transactionPK', <?php echo $transactionPK; ?>, 'amount', amountValue, 'purchaserPK', selectPurchaserValue, 'paymentMethodPK', selectPaymentMethodValue, 'modifiedDate', loadById('modifiedDate'), 'fromAccountPK', selectFromAccountValue, 'toAccountPK', selectToAccountValue, 'active', activeValue, 'posted', postedValue, 'postedDate', postedDateValue, 'categoryPK' , selectCategoryValue, 'description', descriptionValue)">Commit Changes</button>
<?php
			break;
			
		case "new":
?>
<button ng-click="apicall('budgetAPI', 'addTransaction', 'transactionTypePK', 1, 'amount', amountValue, 'purchaserPK', selectPurchaserValue, 'paymentMethodPK', selectPaymentMethodValue, 'originalDate', loadById('modifiedDate'), 'fromAccountPK', selectFromAccountValue, 'toAccountPK', selectToAccountValue, 'active', activeValue, 'posted', postedValue, 'postedDate', postedDateValue, 'categoryPK' , selectCategoryValue, 'description', descriptionValue)">Commit Changes</button>
<?php
			break;
		
		case "reoccurring":
?>
<button ng-click="apicall('budgetAPI', 'addReoccurringTransaciton', 'startDate', loadById('modifiedDate'), 'endDate', loadById('endDate'), 'transactionTypePK', selectTransactionTypePK, 'amount', amountValue, 'purchaserPK', selectPurchaserValue, 'paymentMethodPK', selectPaymentMethodValue, 'fromAccountPK', selectFromAccountValue, 'toAccountPK', selectToAccountValue, 'active', activeValue, 'posted', postedValue, 'postedDate', postedDateValue, 'categoryPK' , selectCategoryValue, 'description', descriptionValue)">Commit Changes</button>
<?php
			break;		
	}
?>
				<br/><br/>

				</center>
				</div>

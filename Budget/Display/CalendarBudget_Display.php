<!-- CalendarBudget_Display -->
<center>
	<table width="100%" border=1>
		<tbody ng-repeat='(weekNumber, dayNumberArray) in nDaysPatternReturned'>
			<tr> 
				<td ng-repeat='(dayNumber, dayPropertyArray) in dayNumberArray' width='14%' ng-class='{lightblue:(getDateCurrentReturned == dayPropertyArray[0]["numericDay"]), blue:(getDateCurrentReturned != dayPropertyArray[0]["numericDay"])}'>
					<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=TransactionForm&transactionPK=new&date={{ dayPropertyArray[0]["numericDay"] }}&account=<?php echo $account; ?>' >{{ dayPropertyArray[0]["numericDay"] }}
						<br/>
						{{ dayPropertyArray[0]["weekDate"] }}
						<br/>
					</a>
				</td>
			</tr>
			<tr>
				<td ng-repeat='(dayNumber, dayPropertyArray) in dayNumberArray' ng-class='{lightblue:(getDateCurrentReturned == dayPropertyArray[0]["numericDay"]), grey:(getDateCurrentReturned != dayPropertyArray[0]["numericDay"])}'>
					<p class='{{ dayPropertyArray[0]["balanceNegative"] }}'>
						Balance: {{ dayPropertyArray[0]["dailyBalance"] | currency}}
					</p>
				</td>
			</tr>
			<tr>
				<td ng-repeat='(dayNumber, dayPropertyArray) in dayNumberArray' ng-class='{lightblue:(getDateCurrentReturned == dayPropertyArray[0]["numericDay"])}'>
					<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=TransactionForm&transactionPK={{ propetyValue["transactionPK"] }}' ng-repeat='(propertyNumber, propetyValue) in dayPropertyArray' ng-show="propertyNumber" class='{{ propetyValue["posted"] }}' title='{{ propetyValue["category"] }}'>
						<p class='{{ propetyValue["posted"] }}'>{{ propetyValue["description"] }}
							<br/>
							{{ propetyValue["toAccount"] }} : {{ propetyValue["toAmount"] | currency }}
						</p>
					</a>
				</td>
			</tr>			
		</tbody>					
	</table>
</center>	

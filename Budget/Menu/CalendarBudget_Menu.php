<!-- CalendarBudget_Menu -->
						<form name='calendarDates' id='calendarDates' action='Budget.php' method="GET">
						<table border=0>
							<tr>
								<td class="bg">
									<p>
									Start Date:
									</p>
									<input type='text' id='start' name='start' value='<?php echo $startDate; ?>'>
									</input>
									<script language="JavaScript">
										var o_cal = new tcal 
										({
										'formname': 'calendarDates',
										'controlname': 'start'
										});
										o_cal.a_tpl.yearscroll = false;
										o_cal.a_tpl.weekstart = 7;
									</script>
								</td>
								<td class="bg">
									<p>
									End Date:
									</p>
									<input type='text' id='end' name='end' value='<?php echo $endDate; ?>'>
									</input>
									<script language="JavaScript">
										var o_cal = new tcal 
										({
										'formname': 'calendarDates',
										'controlname': 'end'
										});
										o_cal.a_tpl.yearscroll = false;
										o_cal.a_tpl.weekstart = 7;
									</script>								
								</td>
								<td class="bg">	
									<p>
										<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=AccountForm'>
										Account
										</a>
									</p>
									<select id='account' name='account' ng-model="selectFromAccount">
										<option ng-repeat="listAccountIdsItem in listAccountIdsReturned | orderBy:'accountName'"  value="{{ listAccountIdsItem.accountPK }}" ng-selected="isSelected(listAccountIdsItem.accountPK, <?php echo $account; ?>)">{{ listAccountIdsItem.accountName }}</option>
									</select>
								</td>
								<td class="bg">
									<p>
										<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=CategoryForm'>
										Category
										</a>
									</p>
									<select id='category' name='category' ng-model="selectFromCategory"> 
										<option value="0" ng-selected="isSelected(0, <?php echo $category; ?>)">
										All Categories
										</option>
										<option ng-repeat="listCategoriesItem in listCategoriesReturned | orderBy:'categoryName'" value="{{ listCategoriesItem.categoryPK }}" ng-selected="isSelected(listCategoriesItem.categoryPK, <?php echo $category; ?>)">
										{{ listCategoriesItem.categoryName }}
										</option>
									</select>																	
								</td>
							</tr>
							<tr>
								<td class="bg" colspan='2'>
									<p>
										Posted
									</p>
									<select id='posted' name='posted' ng-model="selectPosted"> 
										<option value="3" ng-selected="isSelected(3, <?php echo $posted; ?>)">
										All Transactions
										</option>
										<option value="2" ng-selected="isSelected(2, <?php echo $posted; ?>)">
										Posted Transactions
										</option>
										<option value="1" ng-selected="isSelected(1, <?php echo $posted; ?>)">
										Un-Posted Transactions
										</option>
									</select>
								</td>
								<td class="bg" colspan='2'>
									<p>
										Active
									</p>
									<select id='active' name='active' ng-model="selectActive"> 
										<option value="2" ng-selected="isSelected(2, <?php echo $active; ?>)">
										Active Transactions
										</option>
										<option value="1" ng-selected="isSelected(1, <?php echo $active; ?>)">
										Un-Active Transactions
										</option>
										<option value="3" ng-selected="isSelected(3, <?php echo $active; ?>)">
										All Transactions
										</option>
									</select>
								</td>

							</tr>
							<tr>
								<td class="bg" colspan='4'>
									<input type="submit" value='Apply Changes'>
									</input>
								</td>
							</tr>

						</table>
						</form>
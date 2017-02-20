<?php
$system = "budget";

require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'master.inc.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'pageLoaderINC.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'sanitizeINC.php');

$sanitizeIt = SanitizeClass::getInstance();	
$_GET = $sanitizeIt->sanitizeValues($_GET);
$_COOKIE = $sanitizeIt->sanitizeValues($_COOKIE);
$_POST = $sanitizeIt->sanitizeValues($_POST);

	//Check if a specific page exists, else load a default
	if (array_key_exists("page", $_GET))
	{
	$page = $_GET["page"];
	}
	else
	{
	$page = "CalendarBudget";
	}

$variableArray = PageLoaderClass::setVariables($page);

$defaultCategory = 1;
	
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Header' . DIRECTORY_SEPARATOR . 'header.php');
?>
				<div id='DisplayLayer' ng-hide="loggedIN">				
					<div id='options' ng-show="displayOptions">
						<center>
							<!-- Pages -->
							<table border=0>							
								<tr>
									<td class="bg">
									<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=CalendarBudget'>Calendar</a>
									</td>
									
									<!--
									<td class="bg">
									<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=LineItemBudget'>Line Item</a>
									</td>
									-->
									
									<td class="bg">
									<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=TransactionForm'>Transactions</a>
									</td>
									
									<td class="bg">
									<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=CategoryForm'>Categories</a>
									</td>
									
									<td class="bg">
									<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=AccountForm'>Accounts</a>
									</td>
								</tr>
							</table>
<!--
							<table border=0>							
								<tr>
									<td class="bg">
									<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=CalendarBudget<?php echo $argumentString; ?>'>All Transactions</a>
									</td>
									
									<td class="bg">
									<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=CalendarBudget<?php echo $argumentString; ?>&posted=1'>Posted Transactions</a>
									</td>
									
									<td class="bg">
									<a href='<?php echo HTTP; ?>//<?php echo VAR_SERVER_IS; ?>/Budget/Budget.php?page=CalendarBudget<?php echo $argumentString; ?>&category=<?php echo $defaultCategory; ?>'>Transactions by Category</a>
									</td>
								</tr>
							</table>
-->							
							<!-- END Pages -->
							
							<!-- Dynamic Menu Here -->
<?php 
require_once(PageLoaderClass::loadSystemPage($system, $page, 'menu'));
?>
							<!-- END Dynamic Menu -->
							
							<table border=0>
								<tr>
									<td class="bg">
									<button ng-click="apicall('sessionAPI', 'forceLogout')">
										Logout
									</button>
									</td>
								</tr>
							</table>
						</center>	
					</div>
					<!-- Dynamic Display Here -->
<?php 
require_once(PageLoaderClass::loadSystemPage($system, $page, 'display'));
?>
				<!-- END Dynamic Display -->
				</div>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'footer' . DIRECTORY_SEPARATOR . 'footer.php');	
?>
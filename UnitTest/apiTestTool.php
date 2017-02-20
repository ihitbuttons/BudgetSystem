<?php
$system = "UnitTest";

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
	$page = "genericTest";
	}

$variableArray = PageLoaderClass::setVariables($page);
	
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Header' . DIRECTORY_SEPARATOR . 'header.php');
?>

<?php 
require_once(PageLoaderClass::loadSystemPage($system, $page, 'menu'));
?>

<?php 
require_once(PageLoaderClass::loadSystemPage($system, $page, 'display'));
?>

<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'footer' . DIRECTORY_SEPARATOR . 'footer.php');	
?>
<?php
/* SERVER VARIABLES */

DEFINE('VAR_SERVER_IS', "");
DEFINE('HTTP', "https:");
DEFINE('COOKIE_NAME', '');

/* DATABASE */
DEFINE('DATABASE_HOST', "");
DEFINE('DATABASE_PORT', );

DEFINE('BUDGET_DATABASE', 'budget_system');
DEFINE('SESSION_DATABASE', 'budget_system');

DEFINE('DATABASE_USER_READONLY', '');
DEFINE('DATABASE_PASS_READONLY', '');

DEFINE('DATABASE_USER_MODIFY', '');
DEFINE('DATABASE_PASS_MODIFY', '');

DEFINE('DATABASE_USER_DELETE', '');
DEFINE('DATABASE_PASS_DELETE', '');
/* END DATABASE */

/* Session Storage */
DEFINE('SESSION_STORE', 'PHP');

/* Logging Directory Defines */
DEFINE('LOG_DIRECTORY', realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR );

/* Database Encryption */
DEFINE('DATABASE_ENCRYPTION', FALSE);
DEFINE('DATABASE_SITE_KEY', '');
DEFINE('CRYPTO_HEX_OFFSET', "");
DEFINE('CRYPTO_IV', "");
DEFINE('USER_CRYPTO_KEY', '');

/* Set the default API Version */	
DEFINE('APIVERSION', '1.0');

/* Cookie and Session Expirations */
DEFINE('SESSION_EXPIRATION_TIME', 15); //in minutes
DEFINE('COOKIE_EXPIRATION', 1); //in days

/* Front end Javascript Timeout */
DEFINE('JAVASCRIPT_HTTP_TIMEOUT', 600000000); //in miliseconds

/* API Rate Limit Defines */
DEFINE('API_RATE_LIMIT', 1000); //This is the number of API Calls, per user per minute

/* 
LOGGING VARIABLES
1 = Info logging Only *
2 = Warning Logging Only *
3 = Info / Warning Logging Only **
4 = Error Logging Only *
5 = Info / Error Logging Only **
6 = Warning / Error Logging Only **
7 = Info / Warning / Error Logging ***
*/ 

/* Master logging */
DEFINE('MASTER_LOGGING', 0);

/* Logging Method */
DEFINE('LOGGING_METHOD', 'FileLogging');

/* Core Classes */
DEFINE('DATABASECLASS_LOGGING', 0);
DEFINE('PAGELOADERCLASS_LOGGING', 0);
DEFINE('RESPONSECLASS_LOGGING', 0);
DEFINE('SANITIZECLASS_LOGGING', 0);

/* API Logging */
DEFINE('BUDGETCLASS_LOGGING', 0);
DEFINE('PROCEDURALDATES_LOGGING', 0);
DEFINE('RECIPIE_LOGGING', 0);
DEFINE('SESSIONCLASS_LOGGING', 0);
DEFINE('UNITTEST_LOGGING', 0);
DEFINE('USERCLASS_LOGGING', 7);
?>

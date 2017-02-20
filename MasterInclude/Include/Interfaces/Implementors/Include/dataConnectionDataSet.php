<?php
/*
Template:

$dataAbstraction[NUMBER] = array(
"service" => "SERVICE_INTERFACE_NAME",
"connection" => "CONNECTION_INFO",
"dataAdd" => array(
	"requires" => array("FIELD_NAME" => "FIELD_VALUE"),
	"ADDTIONAL_VALUES_BASED_ON_INTERFACE" => array()
	),
"dataFind" => array(
	"returns" => array("FIELD_NAME"),
	"requires" => array("FIELD_NAME" => "FIELD_VALUE"),
	"ADDTIONAL_VALUES_BASED_ON_INTERFACE" => array()
	),
"dataModify" => array(
	"requires" => array("FIELD_NAME" => "FIELD_VALUE"),
	
	),
"dataDelete" => array(
	"requires" => array("FIELD_NAME" => "FIELD_VALUE"),
	"ADDTIONAL_VALUES_BASED_ON_INTERFACE" => array()
	)
);
*/

//defines
define('BUDGET_DB', "budgetsystem1498");
define('USER_DB', "usersystem1498");

//Session Info from sessionID
$dataAbstraction[0] = array(
"service" => "RedisService",
"connection" => "session",
"dataAdd" => array(
	"requires" => array("sessionID", "session_token", "userID"),
	"cacheKey" => "SESSION_ID-SESSION_TOKEN-!!sessionID",
	"hashable" => false,
	"valueArray" => array(
		"session_token" => "!!session_token",
		"userID" => "!!userID"
		)
	),
"dataFind" => array(
	"returns" => array("session_token", "userID"),
	"requires" => array("sessionID"),
	"cacheKey" => "SESSION_ID-SESSION_TOKEN-!!sessionID",
	"hashable" => false
	),
"dataModify" => array(
	"requires" => array("sessionID", "session_token", "userID"),
	"cacheKey" => "SESSION_ID-SESSION_TOKEN-!!sessionID",
	"hashable" => false,
	"valueArray" => array(
		"session_token" => "!!session_token",
		"userID" => "!!userID"
		)
	),
"dataDelete" => array(
	"requires" => array("sessionID"),
	"cacheKey" => "SESSION_ID-SESSION_TOKEN-!!sessionID",
	"hashable" => false
	)
);

//Session Info from session_token
$dataAbstraction[1] = array(
"service" => "RedisService",
"connection" => "session",
"dataAdd" => array(
	"requires" => array("sessionID", "session_token", "userID"),
	"cacheKey" => "SESSION_TOKEN-SESSION_ID-!!session_token",
	"hashable" => false,
	"valueArray" => array(
		"sessionID" => "!!sessionID",
		"userID" => "!!userID"
		)
	),
"dataFind" => array(
	"returns" => array("sessionID", "userID"),
	"requires" => array("session_token"),
	"cacheKey" => "SESSION_TOKEN-SESSION_ID-!!session_token",
	"hashable" => false
	),
"dataModify" => array(
	"requires" => array("sessionID", "session_token", "userID"),
	"cacheKey" => "SESSION_TOKEN-SESSION_ID-!!session_token",
	"hashable" => false,
	"valueArray" => array(
		"sessionID" => "!!sessionID",
		"userID" => "!!userID"
		)
	),
"dataDelete" => array(
	"requires" => array("session_token"),
	"cacheKey" => "SESSION_TOKEN-SESSION_ID-!!session_token",
	"hashable" => false
	)
);

//Accounts from user_group {MySql}
$dataAbstraction[2] = array(
"service" => "MysqlService",
"connection" => "accounts",
"dataAdd" => array(
	"requires" => array("account_number", "account_type_pk", "account_name", "user_group_pk"),
	"query" => "INSERT INTO `" . BUDGET_DB . "`.`accounts` SET `account_number` = '!!account_number', `account_type_pk` = !!account_type_pk, `account_name` = '!!account_name', `user_group_pk` = !!user_group_pk"
	),
"dataFind" => array(
	"returns" => array(
		"account_name",
		"account_pk"
		),
	"requires" => array(
		"user_group_pk" 
		),
	"query" => "SELECT `account_name`, `account_pk` FROM `" . BUDGET_DB . "`.`accounts` WHERE `user_group_pk` = !!user_group_pk"
	),
"dataModify" => array(
	"requires" => array("account_number", "account_type_pk", "account_name", "user_group_pk"),
	"query" => "UPDATE `" . BUDGET_DB . "`.`accounts` SET `account_type_pk` = !!account_type_pk, `account_name` = '!!account_name', `user_group_pk` = !!user_group_pk WHERE `account_number` = '!!account_number'"
	),
"dataDelete" => array(
	"requires" => array(
		"account_number "
		),
	"query" => "DELETE FROM `" . BUDGET_DB . "`.`accounts` WHERE `account_number` = '!!account_number'" 
	)
);

//Accounts from user_group_pk {Redis - cache backed}
$dataAbstraction[3] = array(
"service" => "RedisService",
"connection" => "accounts",
"dataAdd" => array(
	"requires" => array("account_name", "account_type_pk", "user_group_pk"),
	"cacheKey" => "ACCOUNTS-USER_GROUP-!!user_group_pk",
	"hashable" => false,
	"valueArray" => array(
		"account_name" => "!!account_name",
		"account_type_pk" => "!!account_type_pk"
		)
	),
"dataFind" => array(
	"returns" => array("account_name", "account_pk"),
	"requires" => array("user_group_pk"),
	"cacheKey" => "ACCOUNTS-USER_GROUP-!!user_group_pk",
	"hashable" => false
	),
"dataModify" => array(
	"requires" => array("account_name", "account_type_pk", "user_group_pk"),
	"cacheKey" => "ACCOUNTS-USER_GROUP-!!user_group_pk",
	"hashable" => false,
	"valueArray" => array(
		"account_name" => "!!account_name",
		"account_type_pk" => "!!account_type_pk"
		)
	),
"dataDelete" => array(
	"requires" => array("user_group_pk"),
	"cacheKey" => "ACCOUNTS-USER_GROUP-!!user_group_pk",
	"hashable" => false
	)
);

$dataAbstraction[4] = array(
"service" => "MysqlService",
"connection" => "users",
"dataAdd" => array(
	"requires" => array("user_group_pk", "userID", "user_name", "created_date", "last_modified_date", "email_address", "password"),
	"query" => "INSERT INTO `" . USERS_DB . "`.`users_table` SET `user_pk` = '!!user_pk', `user_group_pk` = '!!user_grou', `userID` = '!!userID', `` = '!!',"
	),
"dataFind" => array(
	"returns" => array(
		"account_name",
		"account_pk"
		),
	"requires" => array(
		"user_group_pk" 
		),
	"query" => "SELECT `account_name`, `account_pk` FROM `" . USERS_DB . "`.`accounts` WHERE `user_group_pk` = !!user_group_pk"
	),
"dataModify" => array(
	"requires" => array("account_number", "account_type_pk", "account_name", "user_group_pk"),
	"query" => "UPDATE `" . USERS_DB . "`.`accounts` SET `account_type_pk` = !!account_type_pk, `account_name` = '!!account_name', `user_group_pk` = !!user_group_pk WHERE `account_number` = '!!account_number'"
	),
"dataDelete" => array(
	"requires" => array(
		"account_number "
		),
	"query" => "DELETE FROM `" . USERS_DB . "`.`accounts` WHERE `account_number` = '!!account_number'" 
	)
);
/*
$select_array = array(
	"table" => 'usertable', 
	"where" => 'WHERE', 
	"columns" => array(
	"username",
	"userid",
	"usergroup"),
	"returns" => array(
	"username",
	"userid",
	"usergroup"),
	"conditions" => array(
		array(
		"column" => "username",
		"operator" => "=",
		"value" => "$user",
		"concat" => ""),
		array(
		"column" => "password",
		"operator" => "=",
		"value" => "$password",
		"concat" => "AND"),
		
	),
	"endingQuery" => ""
	);


?>
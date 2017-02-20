<?php
interface SessionInterfaces
{
	public function startSession(); //this should generate an internal session identifier
	public function endSession($sessionID); //this should destroy a session based on the internal identifier
	public function forceEndSession();
	public function dumpValuesSession($sessionID); //this should dump the entire session array based on an internal identifier
	
	public function sessionIDByToken($token);
	public function sessionIDByCookie($cookie);
	public function addSessionIDByToken($sessionID, $token);  
	public function addSessionIDByCookie($sessionID, $cookie);
	//should be private functions for removing these values, that will be used by endSession();
	
    public function addValueToSession($sessionID, $key, $value);  
	public function removeValueFromSession($sessionID, $key);
	public function pullValueSession($sessionID, $key);
	
	public function addElementToSession($sessionID, $element, $key, $value);
	public function removeElementFromSession($sessionID, $element, $key);
	public function pullElementSession($sessionID, $element, $key);	
}
?>
<?php
interface LoggingInterfaces
{
    public function logData($loggingID, $classCalled, $methodCalled, $message, $type);

	public function clearLogs($clearArray);
}
?>
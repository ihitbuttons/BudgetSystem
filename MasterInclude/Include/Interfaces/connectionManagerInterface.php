<?php
interface ConnectionInterface
{
	//This should create, test, failover, shard, etc the connection
    public function createConnection($connectionInfo);
	
	//This should be
	public function closeConnection($connectionInfo);
}
?>
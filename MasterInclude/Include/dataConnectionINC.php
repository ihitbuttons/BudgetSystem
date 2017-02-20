<?php 
/*
Data Connection Class
*/

//include required interfaces
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR . 'dataConnectionInterface.php');

class DataConnection
{
	private $serviceArray;
	private $errorLogging;
	private $dataAbstraction = array();
	
	protected function __construct()
    {
	$this->errorLogging = LoggingClass::getInstance();
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	//get the data connection array
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR . 'MasterInclude' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'dataConnectionDataSet.php');
	$this->dataAbstraction = $dataAbstraction;
	}
	
	public static function getInstance()
    {
    static $instance = null;

        if (null === $instance) 
        {
        $instance = new static();
        }
		
	return $instance;
    }
	
	public function dataOperation($operationArray)
	{
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
	
	/*
		$operationArray = array(
	"operationID" => 2,
	"operation" => "dataFind",
	"requires" => array(
		"user_group" => "$groupID"
		)
	);
	*/
	$operationID = $operationArray["operationID"];
	$operation = $operationArray["operation"];
	$providedArray = $operationArray["requires"];
	
	$requirementsArray = $this->dataAbstraction["$operationID"]["$operation"]["requires"];
	$dataService = $this->dataAbstraction["$operationID"]["service"];	
	
	$fail = false;
		//make sure all required elements are present
		foreach($requirementsArray as $requirementKey => $requirementValue)
		{
		//requirementKey = 0, 1, 2, etc
		//requirementKey = "user_id", etc
			if (!array_key_exists("$requirementValue", $providedArray))
			{
			//we are missing a requirement!
			$fail = true;
			}
		}
		
		if ($fail === false)
		{
			//see if a class exists that can convert the input format to the requested output format
			if (class_exists("$dataService"))
			{	
				//see if the array of classes created has been created
				if(isset($this->serviceArray))
				{
					//see if a class that converts this input to the desired output has been created
					if(array_key_exists("$dataService", $this->serviceArray))
					{
					$responseHandler = $this->serviceArray["$dataService"];	
					}
					else
					{
					$this->serviceArray["$dataService"] = new $dataService;
					$responseHandler = $this->serviceArray["$dataService"];
					}			
				}
				else
				{
				$this->serviceArray["$dataService"] = new $dataService;
				$responseHandler = $this->serviceArray["$dataService"];
				}
			}
			else
			{
			//This is not a valid service				
			}	
		
		$operationProperties = $this->dataAbstraction["$operationID"]["$operation"];
		$operationConnection = $this->dataAbstraction["$operationID"]["connection"];
		$returned_response = $responseHandler->$operation($operationProperties, $providedArray, $operationConnection);
		
		return $returned_response;
		}
		else
		{
		//raise an error about missing a requirement				
		}
	}
	
	private function __clone()
    {
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
    }
    private function __wakeup()
    {
	$this->errorLogging->logInfo(__CLASS__ . "::" . __METHOD__, "Called.");
    }
}
?>
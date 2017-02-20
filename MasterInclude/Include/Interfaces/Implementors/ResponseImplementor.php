<?php
class ArrayJson implements ResponseInterface
{
    public function convertResponse($values)
    {
        if($jsonResponse = json_encode($values))
		{
		return $jsonResponse;
		}
		else
		{
		return "false";	
		}
	}
}

class StringArray implements ResponseInterface
{
    public function convertResponse($values)
    {
        if($jsonResponse = json_decode($values))
		{
		return $jsonResponse;
		}
		elseif($explodedResponse = explode(" ", $values))
		{
		return $explodedResponse;		
		}
		else
		{
		return "false";	
		}
	}
}

class StringJson implements ResponseInterface
{
    public function convertResponse($values)
    {
        if($jsonResponse = json_decode($values))
		{
		return $values;
		}
		elseif($jsonResponse = json_encode($values))
		{
		return $jsonResponse;		
		}
		else
		{
		return "false";	
		}
	}
}

class BooleanJson implements ResponseInterface
{
    public function convertResponse($values)
    {
        if($values)
		{
		$jsonResponse = json_encode(true);	
		}
		else
		{
		$jsonResponse = json_encode(false);	
		}
	
	return $jsonResponse;
	}
}

class ObjectJson implements ResponseInterface
{
    public function convertResponse($values)
    {
	return $values;
	}
}

class JsonArray implements ResponseInterface
{
	public function convertResponse($values)
    {
	//write some code here

	}
}

class JsonString implements ResponseInterface
{
	public function convertResponse($values)
    {	
	//write some code here
	}
}

class ArrayString implements ResponseInterface
{
	public function convertResponse($values)
    {
		if ($implodedResponse = implode(" ", $values))
		{
		return $implodedResponse;	
		}
		else
		{
		return "false";	
		}
	}
}
?>
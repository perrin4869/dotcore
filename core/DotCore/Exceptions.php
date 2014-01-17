<?php

class DotCoreException extends Exception
{
	public function __construct($message = NULL)
	{
		if($message == NULL)
		{
			$message = get_class($this);
		}
		parent::__construct($message);
	}
}

class IntParseException extends DotCoreException {}

?>

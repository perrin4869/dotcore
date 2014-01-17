<?php

class DotCoreAutoIncrementingKey extends DotCoreIntField
{
	public function __construct($field_name, DotCoreDAL $dal)
	{
		// AutoIncrementingKey is nullable - If null, it will create a new key, incrementing the value by one
		parent::__construct($field_name, $dal, TRUE);
	}
}

?>
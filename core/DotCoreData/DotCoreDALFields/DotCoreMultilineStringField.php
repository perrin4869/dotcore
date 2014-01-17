<?php

/**
 * Class used to represent the a plain string field that supports multiline values
 * @author perrin
 *
 */
class DotCoreMultilineStringField extends DotCorePlainStringField
{
	public function __construct(
			$field_name,
			DotCoreDAL $dal,
			$is_nullable = TRUE)
	{
		parent::__construct($field_name, $dal, $is_nullable);
	}
}

?>
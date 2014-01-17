<?php

/**
 * Class used to represent a string supporting HTML in the database field
 * @author perrin
 *
 */
class DotCoreHTMLStringField extends DotCoreStringField
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
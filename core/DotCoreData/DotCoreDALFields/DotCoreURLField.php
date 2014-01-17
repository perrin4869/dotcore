<?php

class InvalidURLException extends DotCoreException {}

/**
 * Class used to represent a url string database field
 * @author perrin
 *
 */
class DotCoreURLField extends DotCorePlainStringField
{
	public function __construct(
		$field_name,
		DotCoreDAL $dal,
		$is_nullable = TRUE)
	{
		parent::__construct($field_name, $dal, $is_nullable);
	}
	
	public function Validate(DotCoreDataRecord $record, &$new_val)
	{
		$result = parent::Validate($record, $new_val);

		if(!$this->IsEmpty($new_val) && !is_url($new_val))
		{
			throw new InvalidURLException();
		}

		return $result;
	}
	
}

?>
<?php

class InvalidWeekdayException extends DotCoreException {}

/**
 * Class used to represent any weekday database field
 * @author perrin
 *
 */
class DotCoreWeekdayField extends DotCoreIntField
{
	public function __construct(
		$field_name,
		DotCoreDAL $dal,
		$is_nullable = TRUE)
	{
		parent::__construct($field_name, $dal, $is_nullable);
	}

	public function Validate(DotCoreDataRecord $record, &$val)
	{
		$result = parent::Validate($record, $val);

		// Check the validity of the weekday
		if(!$this->IsEmpty($val) && ($val < 0 || $val > 6))
		{
			throw new InvalidWeekdayException();
		}

		return $result;
	}
}

?>
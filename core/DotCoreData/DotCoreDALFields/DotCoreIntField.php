<?php

/**
 * Class used to represent any numerical database field
 * @author perrin
 *
 */
class DotCoreIntField extends DotCoreDALField
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

		if(!$this->IsEmpty($new_val) && !is_numeric($new_val))
		{
			throw new IntParseException();
		}

		return $result;
	}

	public function CleanValue($value)
	{
		$value = parent::CleanValue($value);
		if(!$this->IsEmpty($value) && is_numeric($value))
		{
			$value = intval($value);
		}
		return $value;
	}

	public function IsEmpty($val)
	{
		return (empty($val) && $val !== 0 && $val !== '0');
	}
	
	public function GetValuePreparedForQuery($val)
	{
		if($this->IsEmpty($val))
		{
			return 'NULL';
		}
		return $val;
	}
}

?>
<?php

/**
 * Class used to represent any boolean database field
 * @author perrin
 *
 */
class DotCoreBooleanField extends DotCoreDALField
{
	public function __construct(
		$fieldName,
		DotCoreDAL $dal,
		$isNullable = FALSE)
	{
		parent::__construct($fieldName, $dal, $isNullable);
	}

	public function IsEmpty($val)
	{
		return $val === NULL;
	}
	
	public function Validate(DotCoreDataRecord $record, &$val)
	{
		return parent::Validate($record, $val);
	}

	public function CleanValue($val)
	{
		$val = parent::CleanValue($val);
		return $val == TRUE;
	}
	
	public function GetValuePreparedForQuery($val)
	{
		if(self::IsEmpty($val))
		{
				return "NULL";
		}

		if($val == TRUE)
		{
				return "1";
		}
		else
		{
				return "0";
		}
	}
}

?>

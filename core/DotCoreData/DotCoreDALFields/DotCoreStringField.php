<?php

/**
 * Class used to represent the base of any string database field
 * @author perrin
 *
 */
abstract class DotCoreStringField extends DotCoreDALField
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
		// Assume that the value doesn't have slashes in it
		return parent::Validate($record, $val);
	}

	/**
	 * Cleans the given argument from any slashes or spacing it may have been given so it can be stored in a record
	 * Called when storing a value from DB
	 * @return string
	 */
	public function CleanValue($val)
	{
		$val = parent::CleanValue($val);
		return stripslashes($val);
	}

	public function IsEmpty($val)
	{
		return strlen($val) == 0;
	}

	public function Equals($val1, $val2)
	{
		return (strcasecmp($val1, $val2) == 0);
	}

	/**
	 * Gets $val prepared to be inserted into a where clause.
	 * @param string $val - Should be cleaned before calling this function (no slashes and trimmed)
	 * @return string
	 */
	public function GetValuePreparedForQuery($val)
	{
		if(self::IsEmpty($val))
		{
			return 'NULL';
		}

		return '\'' . DotCoreDAL::EscapeString($val) . '\''; // Return the item quoted
	}
}

?>
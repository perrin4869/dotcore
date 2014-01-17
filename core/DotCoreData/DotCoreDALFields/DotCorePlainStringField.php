<?php

/**
 * Class used to represent the a plain one line database field
 * @author perrin
 *
 */
class DotCorePlainStringField extends DotCoreStringField
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
		$result = parent::Validate($record, $val);

		// Plain strings do not allow HTML
		// The value inside the record is always stored HTML encoded, and it's decoded only when storing it in the DB
		$val = htmlspecialchars($val); // Special chars will not convert hebrew characters too

		return $result;
	}

	/**
	 * Cleans the given argument from any slashes or spacing it may have been given so it can be stored in a record
	 * Called when storing a value from DB
	 * @return string
	 */
	public function CleanValue($val)
	{
		$val = parent::CleanValue($val);
		
		// Plain strings do not allow HTML
		return htmlspecialchars($val); // Special chars will not convert hebrew characters too
	}

	/**
	 * Gets $val prepared to be inserted into a where clause.
	 * @param string $val - Should be cleaned before calling this function (no slashes and trimmed)
	 * @return string
	 */
	public function GetValuePreparedForQuery($val)
	{
		$val = parent::GetValuePreparedForQuery($val);
		return htmlspecialchars_decode($val);
	}
}

?>
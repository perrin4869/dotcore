<?php

/**
 * Class used to represent any password database field
 * @author perrin
 *
 */
class DotCorePasswordField extends DotCorePlainStringField
{
	public function __construct(
		$field_name,
		DotCoreDAL $dal,
		$is_nullable = TRUE)
	{
		// Password is NEVER unique
		parent::__construct($field_name, $dal, $is_nullable);
	}
	
	public function Validate(DotCoreDataRecord $record, &$val)
	{
		parent::Validate($record, $val);
		$val = $this->Encrypt($val);
		return TRUE;
	}

	public function Encrypt($val)
	{
		return md5(strtolower($val));
	}
}

?>
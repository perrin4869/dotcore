<?php

class InvalidRecursiveFieldException extends DotCoreException {}
class ChildIsOwnParentRecursiveException extends DotCoreException {}

/**
 * Class used to represent recursive fields, i.e., fields that reference themselves like tree
 * @author perrin
 *
 */
class DotCoreRecursiveIntField extends DotCoreIntField
{
	public function __construct(
		$field_name,
		DotCoreDAL $dal,
		$constrain_field_name,
		$is_nullable = TRUE)
	{
		parent::__construct($field_name, $dal, $is_nullable);

		$this->constrain_field_name = $constrain_field_name;
	}

	private $constrain_field_name;

	public function GetConstrainFieldName()
	{
		return $this->constrain_field_name;
	}

	public function Validate(DotCoreDataRecord $record, &$new_val)
	{
		// If new_val is considered empty, make it NULL so NULL will be inserted into DB
		// Make it NULL now so that the NULL will be recorgnized throughout the validation
		if(empty($new_val))
		{
			$new_val = NULL;
		}
		
		$result = parent::Validate($record, $new_val);

		// If it's nullable, it can be empty, and then there's nothing to check
		if(!$this->IsEmpty($new_val))
		{
			$constrainField = $this->GetDAL()->GetField($this->GetConstrainFieldName());
			$restraint = new DotCoreDALRestraint();
			$restraint->AddRestraint(
				new DotCoreFieldRestraint($constrainField, $new_val, DotCoreFieldRestraint::OPERATION_EQUALS));

			// Make sure we're adding an existing page
			$count = $this->GetDAL()->Restraints($restraint)->GetCount();
			if($count < 1)
			{
				throw new InvalidRecursiveFieldException();
			}

			// We know that in recursive fields, there's only
			if($record->GetField($this->GetConstrainFieldName()) == $new_val)
			{
				// We can't have this value be equal to his parent's. A child can't be its own parent. It's a paradox!
				// We also know that if they're equal, they're not NULL, because $val != NULL
				throw new ChildIsOwnParentRecursiveException();
			}
		}

		return $result;
	}
}

?>
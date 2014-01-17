<?php

/**
 * Description of DotCoreLinkRestraint
 *
 * @author perrin
 */
class DotCoreLinkRestraint extends DotCoreDALRestraintUnit {

	public function  __construct(
		IDotCoreDALSelectableEntity $linking_field,
		IDotCoreDALSelectableEntity $linked_field,
		$operation = self::OPERATION_EQUALS) {
		
		$this->linking_field = $linking_field;
		$this->linked_field = $linked_field;
		$this->operation = $operation;
	}

	const OPERATION_EQUALS = '=';
	const OPERATION_NOT_EQUALS = '!=';
	const OPERATION_GREATER_THAN = '>';
	const OPERATION_GREATER_OR_EQUAL = '>=';
	const OPERATION_LESS_THAN = '<';
	const OPERATION_LESS_OR_EQUAL = '<=';

	/**
	 * Holds the definition of the field in the linking DAL
	 *
	 * @var IDotCoreDALSelectableEntity
	 */
	private $linking_field = NULL;

	/**
	 * The the definition of the field in the linked DAL
	 * 
	 * @var IDotCoreDALSelectableEntity
	 */
	private $linked_field = NULL;

	/**
	 * The operation to be made between the fields being liked
	 *
	 * @var string
	 */
	private $operation = NULL;

	/*
	 *
	 * Public accessors:
	 *
	 */

	/**
	 * Gets the foreign field in the link
	 * @return IDotCoreDALField
	 */
	public function GetLinkingField()
	{
		return $this->linking_field;
	}

	/**
	 * Sets the foreign field
	 *
	 * @param IDotCoreDALField $linking_field
	 * @return DotCoreLinkRestraint
	 */
	public function SetLinkingField(IDotCoreDALField $linking_field)
	{
		$this->linking_field = $linking_field;
		return $this;
	}

	/**
	 * Gets the primary field in the link
	 * @return IDotCoreDALField
	 */
	public function GetLinkedField()
	{
		return $this->linked_field;
	}

	/**
	 * Sets the primary field
	 *
	 * @param IDotCoreDALField $val
	 * @return DotCoreLinkRestraint
	 */
	public function SetLinkedField(IDotCoreDALField $field)
	{
		$this->linked_field = $field;
		return $this;
	}

	/**
	 * Returns the operation to which the field is restrained to the value of this restraint by
	 * @return string
	 */
	public function GetOperation()
	{
		return $this->operation;
	}

	/**
	 * Sets the operation to which the field is restrained to the value of this restraint by
	 *
	 * @param string $operation
	 * @return DotCoreLinkRestraint
	 */
	public function SetOperation($operation)
	{
		$this->operation = $operation;
		return $this;
	}

	/**
	 * Gets the SQL statement resulting from this DotCoreLinkRestraint
	 * @return string
	 */
	public function GetStatement() {
		// The reason we use GetSQLNameWithTablePrefix instead of GetSQLName
		// is because MySQL doesn't recognize field aliases on ON clauses
		return $this->linking_field->GetSQLNameWithTablePrefix() . $this->operation . $this->linked_field->GetSQLNameWithTablePrefix();
	}

}
?>

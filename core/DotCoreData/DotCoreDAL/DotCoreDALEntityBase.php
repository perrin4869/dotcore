<?php

/**
 * DotCoreDALEntityBase - Provides basic functionality for entities
 *
 * @author perrin
 */
abstract class DotCoreDALEntityBase extends DotCoreObject implements IDotCoreDALSelectableEntity {

	public function  __construct() {
		// Nothing fancy here
	}

	/**
	 * If set this is the name that will be used for queries
	 * @var string
	 */
	private $alias = NULL;

	public function getAlias() {
		return $this->alias;
	}

	public function setAlias($alias) {
		$this->alias = $alias;
	}

	/*
	 *
	 * Overridable methods
	 *
	 */

	public function CleanValue($val)
	{
		return stripslashes($val);
	}

	public function SetValue(DotCoreDataRecord $record, $value)
	{
		return $record->SetEntityValue($this->GetName(), $value);
	}

	public function SetValueFromDAL(DotCoreDataRecord $record, $value)
	{
		return $record->SetEntityValue($this->GetName(), $value);
	}

	public function GetValue(DotCoreDataRecord $record)
	{
		return $record->GetEntityValue($this->GetName());
	}

	public function IsEmpty($val) {
		$val = trim($val);
		return empty($val); // Nothing fancy here
	}

	public function GetValuePreparedForQuery($val) {
		return '\''.DotCoreDAL::EscapeString($val).'\'';
	}

	public function GetSelectStatementSQL()
	{
		$result = $this->GetSQLNameWithTablePrefix();
		$alias = $this->getAlias();
		if($alias != NULL) {
			$result .= ' AS '.$alias;
		}
		return $result;
	}

	public function GetSQLName()
	{
		$alias = $this->getAlias();
		if($alias != NULL) {
			return $alias;
		}
		else {
			return $this->GetName();
		}
	}

}
?>

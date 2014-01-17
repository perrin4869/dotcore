<?php

/**
 * IDotCoreDALSelectableEntity - Defines the interface which all items that can be selected as a result of a DAL query must implement
 */
interface IDotCoreDALSelectableEntity
{

	/**
	 * Gets the DAL to which the entity is associated
	 *
	 * @return DotCoreDAL
	 */
	public function GetDAL();

	/**
	 * Gets the name of the entity
	 *
	 * @return string
	 */
	public function GetName();

	/**
	 * Gets the SQL name of the container of this entity (most of the time, the name of the DAL or the Link containing it)
	 *
	 * @return string
	 */
	public function GetSQLContainerName();

	/**
	 * Gets the SQL name of the entity, with the name of the table as a prefix
	 *
	 * @return string
	 */
	public function GetSQLNameWithTablePrefix();

	/**
	 * Gets the name of this selectable entity for use inside a query (for example, inside ORDER BY statements, etc)
	 *
	 * @return string
	 */
	public function GetSQLName();

	/**
	 * Gets the SQL needed to embed this entity in a select statement
	 * 
	 * @return string
	 */
	public function GetSelectStatementSQL();

	/**
	 * Cleans the value gotten from the DAL for insertion into a record
	 * @param mixed $val
	 */
	public function CleanValue($val);

	/**
	 *
	 * @param mixed $val
	 */
	public function IsEmpty($val);

	/**
	 * Returns $val prepared for query
	 * @param $val
	 * @return mixed
	 */
	public function GetValuePreparedForQuery($val);

	/**
	 * Method called in order to store $value inside $record, validating them first
	 * @param DotCoreDataRecord $record
	 * @param mixed $value
	 * @return bool TRUE if successfully set, FALSE otherwise
	 */
	public function SetValue(DotCoreDataRecord $record, $value);

	/**
	 * Method called in order to store $value inside $record. Assume $value and $record are valid, as they come from DAL
	 * @param DotCoreDataRecord $record
	 * @param mixed $value
	 */
	public function SetValueFromDAL(DotCoreDataRecord $record, $value);

	/**
	 * Method called in order to get the value of the entity inside $record
	 * @param DotCoreDataRecord $record 
	 */
	public function GetValue(DotCoreDataRecord $record);

}

?>

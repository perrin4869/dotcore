<?php

/**
 * IDotCoreDALField provides basic interface that selectable entities need to implement in order to be fields
 *
 * @author perrin
 */
interface IDotCoreDALField extends IDotCoreDALSelectableEntity {

	/*
	 * Getters:
	 */

	/**
	 * Gets the name of the field in the database
	 * @return string
	 */
	public function GetFieldName();

	/**
	 * Returns true if the field is nullable, false otherwise
	 * @return boolean
	 */
	public function IsNullable();

	/**
	 * If $new_val is valid as defined by this DAL field, this function cleans it so it can be inserted into DB and returns nothing;
	 * If not valid, a DotCoreException is thrown
	 * @param DotCoreDataRecord $record - The record being validated
	 * @param mixed $new_val
	 *
	 * @return TRUE if the value should be set, FALSE to stop the value from being set
	 */
	public function Validate(DotCoreDataRecord $record, &$new_val);

	/**
	 * Returns true if $val1=$val2
	 * @param $val1
	 * @param $val2
	 * @return boolean
	 */
	public function Equals($val1, $val2);

	/**
	 * Method used to check whether or not this field's value has been loaded into $record
	 * @param DotCoreDataRecord $record
	 * @return TRUE if the field is loaded, FALSE otherwise
	 */
	public function IsFieldLoaded(DotCoreDataRecord $record);

}
?>

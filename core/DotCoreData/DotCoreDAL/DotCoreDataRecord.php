<?php

/**
 * DotCoreDataRecord is the base class of all the Records returned by the DALs
 *
 * @author perrin
 */
abstract class DotCoreDataRecord extends DotCoreObject {

	/**
	 * Constructor of DotCoreDataRecord, may be called ONLY from its corresponding DAL
	 * Friendly to DotCoreDAL
	 * @param DotCoreDAL $dal
	 */
	public function  __construct(DotCoreDAL $dal) {
		$this->dal = $dal;
	}

	public function  __toString() {
		return $this->GetUniqueCode();
	}

	public function PrintValues() {
		return print_r($this->values, true);
	}

	public function GetUniqueCode() {
		$result = '';
		$primary_fields = $this->dal->GetPrimaryFields();
		foreach($primary_fields as $field) {
			if(!empty($result)) {
				$result .= '_';
			}
			$result .= $this->GetField($field->GetFieldName());
		}
		return $result;
	}

	/**
	 * Holds the DAL that created this record
	 * @var DotCoreDAL
	 */
	private $dal;

	/**
	 * Holds the values of all the fields loaded into this record
	 * @var array of mixed
	 */
	private $values = array();

	/**
	 * Holds the values of entities that are not fields, like SQL functions results
	 * @var array
	 */
	private $entities = array();

	/**
	 * Holds the original values in the record, as they were in the original data source, before the user changed them
	 * @var array of mixed
	 */
	private $original_values = array();

	/**
	 * Holds the values gotten from the links stored in the DAL
	 * @var array of mixed
	 */
	private $links_values = array();

	/**
	 * Holds true if this record still does not belong to the backend data source
	 * @var boolean
	 */
	private $empty = TRUE;

	/**
	 * Holds a value defining whether or not this record is valid
	 * @var boolean
	 */
	private $is_valid = TRUE;

	/**
	 * An array used to store arbitrary values as needed
	 * @var array
	 */
	private $stored_values = array();

	/**
	 * Function used to get a reference to the DAL used to access records of this type
	 *
	 * @return DotCoreDAL
	 */
	public function GetDAL()
	{
		return $this->dal;
	}

	/**
	 * Returns the object that carries all the values of this record, to be used ONLY by the DAL
	 * Friendly to DotCoreDAL
	 *
	 * SHOULDN'T BE CALLED BY THE CLIENT
	 * @return array
	 */
	public function &GetRecordValuesHolder()
	{
		return $this->values;
	}

	/**
	 * Returns the object that carries all the values of the links of this record, to be used ONLY by the DAL
	 * Friendly to DotCoreDAL
	 *
	 * SHOULDN'T BE CALLED BY THE CLIENT
	 * @return array
	 */
	public function &GetRecordLinkValuesHolder()
	{
		return $this->links_values;
	}

	/**
	 * Gets the value of $field_name in this record. If not loaded, loads it on the run
	 * @param string $field_name
	 * @return mixed
	 */
	public function GetField($field_name)
	{
		$field = $this->GetDAL()->GetField($field_name);
		return $field->GetValue($this);
	}

	/**
	 * Loads the field $field_name with the value from Database
	 * @param string $field_name
	 */
	public function LoadField($field_name)
	{
		$this->dal->LoadField($field_name, $this);
	}

	public function GetLoadedFields()
	{
		$result = array();
		$keys = array_keys($this->values);
		foreach($keys as $key)
		{
				array_push($result, $this->GetDAL()->GetField($key));
		}
		return $result;
	}

	public function HasFieldLoaded($field_name) {
		return key_exists($field_name, $this->values);
	}

	public function GetLinkValue($link_name)
	{
		if(!isset($this->links_values[$link_name]) && !$this->IsEmpty())
		{
			DotCoreDAL::GetRelationship($link_name)->LoadLinkValue($this);
		}
		return isset($this->links_values[$link_name]) ? $this->links_values[$link_name] : NULL;
	}

	/**
	 * Sets the value of a link
	 * @param DotCoreDataRecord $record
	 */
	public function SetLinkValue($link_name, DotCoreDataRecord $record)
	{
		$relationship = DotCoreDAL::GetRelationship($link_name);
		if($relationship)
		{
			$this->GetDAL()->SetLinkValue($relationship, $this, $record);
			$this->RecordChanged();
		}
	}

	public function GetLoadedRelationships() {
		return array_keys($this->links_values);
	}

	public function HasLinkValueLoaded($link_name) {
		return array_key_exists($link_name, $this->links_values);
	}

	/**
	 * Gets an array of the fields that have to be updated in the database
	 * @return array of string
	 */
	public function GetEditedFields()
	{
		return array_keys($this->original_values);
	}

	public function ResetOldFields()
	{
		unset($this->original_values);
		$this->original_values = array();
	}

	/**
	 * Sets the value of $field_name in this record to $value, or throws exception if $value is invalid
	 * @param string $field_name
	 * @param mixed $value
	 *
	 * @throws InvalidFieldException if the requested field is invalid
	 */
	public function SetField($field_name, $value)
	{
		$dal = $this->GetDAL();
		$field = $dal->GetField($field_name);
		$old_value = $this->GetField($field_name);

		if($field->Validate($this, $value))
		{
			// If this record is empty, we try setting the value in order to validate it, because we don't know if it's
			// valid or not
			// In case it's not empty, set only if it differs from the old value, because we know the value is valid
			// if it equals the old value (the old value is set, so we know it's valid, because in order to be set it must
			// be validated)
			if($this->IsEmpty() || !$field->Equals($value, $old_value))
			{
				// Record the old value for future reference, if needed
				if(!$this->FieldChanged($field_name))
				{
					// Record ONLY THE VERY FIRST CHANGE TO THE VALUE, as it is the only relevant one
					// Subsequent changes, if any, are not original, as they have been originated by subsequent
					// changes by the user
					$this->original_values[$field_name] = $old_value;
				}

				$this->ChangeValue($field_name, $value);

				// The record was changed, so it's not valid anymore - needs another validation
				$this->RecordChanged();
			}
		}
	}

	public function SetFieldFromDAL($field_name, $value)
	{
		// Because we're setting the field from the DAL, it means that the value is restored to its most recent state
		// So there's no meaning in its original value
		unset($this->original_values[$field_name]);
		$this->ChangeValue($field_name, $value);
	}

	public function ChangeValue($field_name, $value)
	{
		$this->values[$field_name] = $value;
	}
	
	public function IsFieldSet($field_name)
	{
		return key_exists($field_name, $this->values);
	}

	/**
	 * Saves the value of an entity result
	 * @param string $entity_name
	 * @param mixed $value
	 */
	public function SetEntityValue($entity_name, $value) {
		$this->entities[$entity_name] = $value;
	}

	/**
	 * Gets the value of an entity stored in this record
	 * @param string $entity_name
	 * @return mixed
	 */
	public function GetEntityValue($entity_name) {
		return $this->entities[$entity_name];
	}

	/**
	 * Used to check whether the record has changed since it was pulled from the database / updated to the database
	 * @return boolean
	 */
	public function HasChanged() {
		return $this->IsValid();
	}

	/**
	 * Marks the record as having changed, should be called by DAL only
	 */
	public function RecordChanged() {
		// The record was changed, so it's not valid anymore - needs another validation
		$this->is_valid = FALSE;
	}

	/**
	 * Returns true if the field with name $field_name has changed and wasn't saved yet, false otherwise
	 * @param string $field_name
	 * @return boolean
	 */
	public function FieldChanged($field_name) {
		return key_exists($field_name, $this->original_values);
	}

	/**
	 * Gets the original value of the field by $field_name as it came from the underlying database, or NULL otherwise
	 * @param string $field_name
	 * @return string
	 */
	public function GetOriginalValue($field_name) {
		if(key_exists($field_name, $this->original_values)) {
			return $this->original_values[$field_name];
		}
		else {
			return $this->GetField($field_name);
		}
	}

	/**
	 * Returns the fields that were changed to their original values
	 */
	public function RestoreOriginalValues() {
		foreach($this->original_values as $key=>$value)
		{
			$this->values[$key] = $value;
		}
		$this->ResetOldFields(); // All the fields were recovered, so we're resetting to the previous state
	}

	/**
	 * Returns true if this record has not been populated with data from a DAL,
	 * or if it hasn't been inserted into any data source with a DAL
	 * @return boolean
	 */
	public function IsEmpty()
	{
		return $this->empty;
	}

	/**
	 * Sets this record as non-empty - meaning it has either been populated with data from a DAL or has been inserted with a DAL
	 * Should be called only from its respective DAL - Frieldly to DotCoreDAL
	 *
	 * @return DotCoreDataRecord
	 */
	public function SetSaved()
	{
		$this->empty = FALSE;
		$this->is_valid = TRUE; // When set filled it is valid automatically
		$this->ResetOldFields();
		return $this;
	}

	/**
	 * Checks whether or not this record is valid
	 * @return boolean
	 */
	public function IsValid() {
		return $this->is_valid;
	}

	/**
	 * Sets this record as valid if $bool is TRUE, or to FALSE otherwise
	 * Should be called only from its respective DAL - Frieldly to DotCoreDAL
	 *
	 * @param boolean $bool
	 * @return DotCoreDataRecord
	 */
	public function SetValid($bool = TRUE) {
		$this->is_valid = $bool;
		return $this;
	}

	/**
	 * Stores a value in this record for future referral
	 * @param string $key
	 * @param mixed $val
	 */
	public function StoreValue($key, $val) {
		$this->stored_values[$key] = $val;
		return $this;
	}

	/**
	 * Retrieves a stored value in the record
	 * @param string $key
	 */
	public function RetrieveValue($key) {
		return $this->stored_values[$key];
	}

	/**
	 * Frees the value stored in $key
	 * @param string $key
	 */
	public function DeleteStoredValue($key) {
		$this->stored_values[$key] = NULL;
		unset($this->stored_values[$key]);
	}

	/**
	 * Checks whether the record has a value stored in $key
	 * @param string $key
	 */
	public function HasStoredValue($key) {
		return key_exists($key, $this->stored_values);
	}

}
?>

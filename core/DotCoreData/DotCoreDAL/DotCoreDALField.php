<?php

/**
 * Exception thrown whenever a required field is tried to be set to empty
 * @author perrin
 *
 */
class EmptyRequiredFieldException extends DotCoreException { }

/**
 * DotCoreDALField is the base for the definition of DAL fields
 *
 * @author perrin
 */
abstract class DotCoreDALField extends DotCoreObject implements IDotCoreDALField {
    
    /**
     * Constructor for a generic DotCoreDALField
     * @param string $field_name The name of the field in the database
     * @param DotCoreDAL $dal A reference to the DAL that holds this field
     * @param boolean $is_nullable Optional, true if the field is nullable, false otherwise
     */
    public function __construct(
        $field_name,
        DotCoreDAL $dal,
        $is_nullable = TRUE)
    {
        $this->field_name = $field_name;
        $this->dal = $dal;
        $this->is_nullable = $is_nullable;
    }

    /**
     * Holds the DAL
     * @var DotCoreDAL
     */
    private $dal;
    private $field_name;
    private $is_nullable;

    /**
     * Returns the name of the DB Field
     * @return string
     */
    public function __toString()
    {
        return $this->GetFieldName();
    }

    /*
     *
     * Accessors:
     *
     */

    /*
     * Getters:
     */

    /**
     * Gets the name of the field in the database
     * @return string
     */
    public function GetFieldName()
    {
        return $this->field_name;
    }

    /**
     * Returns true if the field is nullable, false otherwise
     * @return boolean
     */
    public function IsNullable()
    {
        return $this->is_nullable;
    }

    /**
     * If $new_val is valid as defined by this DAL field, this function cleans it so it can be inserted into DB and returns nothing;
     * If not valid, a DotCoreException is thrown
     * @param DotCoreDataRecord $record - The record being validated
     * @param mixed $new_val
     *
     * @return TRUE if the value should be set, FALSE to stop the value from being set
     */
    public function Validate(DotCoreDataRecord $record, &$new_val)
    {
        if(is_string($new_val))
        {
            $new_val = trim($new_val);
        }
        
        if(!$this->IsNullable() && $this->IsEmpty($new_val))
        {
            throw new EmptyRequiredFieldException();
        }

        return TRUE;
    }

    /**
     * Returns true if $val1=$val2
     * @param $val1
     * @param $val2
     * @return boolean
     */
    public function Equals($val1, $val2)
    {
        return $val1 == $val2;
    }


    /*
     *
     * IDotCoreSelectableEntity Implementation:
     *
     */
     
    /**
     * Gets the reference to the table this field belongs to
     * @return DotCoreDAL
     */
    public function GetDAL()
    {
        return $this->dal;
    }

    /**
     * Gets the name of this selectable entity for use inside a query (for example, inside WHERE statements, etc)
     *
     * @return string
     */
    public function GetName()
    {
        return $this->field_name;
    }

    public function GetSQLContainerName()
    {
        return $this->dal->GetName();
    }

    public function GetSQLNameWithTablePrefix()
    {
        return $this->GetSQLContainerName() . '.' . $this->GetName();
    }

    public function GetSQLName()
    {
        return $this->GetSQLContainerName() . '_' . $this->GetName();
    }

    public function GetSelectStatementSQL()
    {
        return $this->GetSQLNameWithTablePrefix() . ' AS ' . $this->GetSQLName();
    }

    /**
     * Performs any operations needed on the value given so it can be given to a record successfully
     * @param $val
     */
    public function CleanValue($val)
    {
        return $val;
    }

    public function SetValue(DotCoreDataRecord $record, $value) {
        return $record->SetField($this->GetName(), $value);
    }

    public function SetValueFromDAL(DotCoreDataRecord $record, $value) {
        $value = $this->CleanValue($value);
        $record->SetFieldFromDAL($this->GetFieldName(), $value);
    }

    public function GetValue(DotCoreDataRecord $record) {
        $field_name = $this->GetFieldName();
        if(!$record->IsFieldSet($field_name))
        {
            if($record->IsEmpty())
            {
                // There's no value in the database yet, so it's currently NULL
                return NULL;
            }
            else
            {
                // Load it dynamically
                // Maybe in the future we can add a strict mode, in which this throws an exception
                // This is an expensive function
                $this->GetDAL()->LoadField($field_name, $record);
            }
        }

        $values_holder = &$record->GetRecordValuesHolder();
        return $values_holder[$field_name];
    }

    public function IsFieldLoaded(DotCoreDataRecord $record) {
        return $record->HasFieldLoaded($this->GetFieldName());
    }

}
?>

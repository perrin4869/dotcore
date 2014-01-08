<?php

/**
 * Description of DotCoreEntityRestraint
 *
 * @author perrin
 */
class DotCoreEntityRestraint extends DotCoreDALRestraintUnit {

    public function  __construct(IDotCoreDALSelectableEntity $entity, $value, $operation = self::OPERATION_EQUALS) {
        $this->entity = $entity;
        $this->value = $value;
        $this->operation = $operation;
    }

    const OPERATION_EQUALS = '=';
    const OPERATION_NOT_EQUALS = '!=';
    const OPERATION_GREATER_THAN = '>';
    const OPERATION_GREATER_OR_EQUAL = '>=';
    const OPERATION_LESS_THAN = '<';
    const OPERATION_LESS_OR_EQUAL = '<=';
    const OPERATION_LIKE = 'LIKE';
    const OPERATION_NOT_LIKE = 'NOT LIKE';

    /**
     * Holds the definition of the field to be restrained
     *
     * @var IDotCoreDALSelectableEntity
     */
    private $entity = NULL;

    /**
     * The value to which the field is restrained
     * 
     * @var mixed
     */
    private $value = NULL;

    /**
     * The operation to be made between the field and the value
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
     * Gets the field restrainted by this restraint
     * @return IDotCoreDALSelectableEntity
     */
    public function GetField()
    {
        return $this->entity;
    }

    /**
     * Sets the field that will be restrained
     *
     * @param IDotCoreDALSelectableEntity $field
     * @return DotCoreEntityRestraint
     */
    public function SetField(IDotCoreDALField $field)
    {
        $this->entity = $field;
        return $this;
    }

    /**
     * Gets the value to which the field of this DotCoreEntityRestraint is restrained
     * @return mixed
     */
    public function GetValue()
    {
        return $this->value;
    }

    /**
     * Sets the value to which the field of this DotCoreEntityRestraint is restrained
     *
     * @param mixed $val
     * @return DotCoreEntityRestraint
     */
    public function SetValue($val)
    {
        $this->value = $val;
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
     * @return DotCoreEntityRestraint
     */
    public function SetOperation($operation)
    {
        $this->operation = $operation;
        return $this;
    }

    /**
     * Gets the SQL statement resulting from this DotCoreEntityRestraint
     * @return string
     */
    public function GetStatement() {
        // The reason we use GetFieldNameWithTablePrefix instead of GetFieldNameForQuery
        // is because MySQL doesn't recognize field aliases on WHERE clauses

        /*
         * Standard SQL doesn't allow you to refer to a column alias in a WHERE clause.
         * This restriction is imposed because when the WHERE code is executed, the column value may not yet be determined.
         */
        $result = $this->entity->GetSQLNameWithTablePrefix();
        if($this->entity->IsEmpty($this->value))
        {
            $not = ($this->operation == self::OPERATION_NOT_EQUALS) ? ' NOT' : '';
            return $result . ' IS' . $not . ' NULL';
        }
        else
        {
            return $result . ' ' . $this->operation . ' ' . $this->entity->GetValuePreparedForQuery($this->value);
        }
    }

}
?>

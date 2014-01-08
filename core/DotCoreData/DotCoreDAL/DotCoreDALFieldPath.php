<?php

/**
 * DotCoreDALFieldPath - Defines a path throught a hierarchy of linked DALS to an DotCoreDALField interface
 *
 * @author perrin
 */
class DotCoreDALFieldPath extends DotCoreDALEntityPath implements IDotCoreDALLinkedField {

    /**
     * Constructor of DotCoreDALEntityPath
     *
     * @param DotCoreDALField $field The actual field to be used
     * @param DotCoreDALPath $path The path to the entity
     */
    public function  __construct(DotCoreDALField $field , DotCoreDALPath $path) {
        parent::__construct($field, $path);
    }

    /**
     *
     * @return DotCoreDALField
     */
    public function getField()
    {
        return $this->getEntity();
    }

    /*
     *
     * IDotCoreDALField implementation:
     *
     */

    public function GetFieldName()
    {
        return $this->getField()->GetFieldName();
    }

    public function IsNullable()
    {
        return $this->getField()->IsNullable();
    }

    public function GetValuePreparedForQuery($val)
    {
        return $this->getField()->GetValuePreparedForQuery($val);
    }

    public function IsEmpty($val)
    {
        return $this->getField()->IsEmpty($val);
    }

    public function Validate(DotCoreDataRecord $record, &$new_val)
    {
        return $this->getField()->Validate($record, $new_val);
    }

    public function Equals($val1, $val2)
    {
        return $this->getField()->Equals($val1, $val2);
    }

    public function IsFieldLoaded(DotCoreDataRecord $record) {
        return $this->getField()->IsFieldLoaded($this->GetLinkedRecord($record));
    }

}
?>

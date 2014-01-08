<?php

/**
 * DotCoreDALEntityPath - Defines a path throught a hierarchy of linked DALS to an IDotCoreDALSelectableEntity interface
 *
 * @author perrin
 */
class DotCoreDALEntityPath extends DotCoreObject implements IDotCoreDALLinkedEntity {

    /**
     * Constructor of DotCoreDALEntityPath
     *
     * @param IDotCoreDALSelectableEntity $entity The actual entity to be used
     * @param DotCoreDALPath $path The path to the entity
     */
    public function  __construct(IDotCoreDALSelectableEntity $entity , DotCoreDALPath $path) {
        $this->entity = $entity;
        $this->path = $path;
    }

    public function  __toString() {
        return $this->entity->GetSQLName();
    }

    /**
     * Stores the path to the entity
     * @var DotCoreDALPath
     */
    private $path = NULL;
    /**
     * Stores the entity pointed to by the path
     * @var IDotCoreDALSelectableEntity
     */
    private $entity = NULL;

    /*
     *
     * Accessors:
     *
     */

    /**
     *
     * @return DotCoreDALPath
     */
    public function getPath() {
        return $this->path;
    }

    /**
     *
     * @param DotCoreDALPath $path
     * @return DotCoreDALEntityPath
     */
    public function setPath(DotCoreDALPath $path) {
        $this->path = $path;
        return $this;
    }

    /**
     *
     * @return IDotCoreDALSelectableEntity
     */
    public function getEntity() {
        return $this->entity;
    }

    /**
     *
     * @param IDotCoreSelectableEntity $entity
     * @return DotCoreDALEntityPath
     */
    public function setEntity(IDotCoreSelectableEntity $entity){
        $this->entity = $entity;
        return $this;
    }

    /**
     * Compares $value to this entity, and returns true if both $value and $this are equal
     * $value can be either a DotCoreDALEntityPath or an IDotCoreDALSelectableEntity
     * @param mixed $value
     */
    public function Equals($value)
    {
        if($value instanceof DotCoreDALEntityPath)
        {
            return $value->__toString() == $this->__toString();
        }
        elseif($value instanceof IDotCoreDALSelectableEntity)
        {
            return $value->GetSQLName() == $this->__toString();
        }
        return FALSE;
    }

    /*
     *
     * Methods:
     *
     */

    /**
     * Gets the name of the link, if any, to the entity, or NULL if no link leads to the entity (i.e., the entity resides in the main DAL)
     * @return string
     */
    public function GetSQLLinkName()
    {
        if($this->getPath()->count() > 0)
        { 
            return join('_', $this->getPath()->toArray());
        }
        return NULL;
    }

    /*
     *
     * IDotCoreSelectableEntity implementation
     *
     */

    /**
     * Gets the DAL to which the entity is associated
     *
     * @return DotCoreDAL
     */
    public function GetDAL()
    {
        return $this->entity->GetDAL();
    }

    /**
     * Gets the name of this selectable entity for use inside a query (for example, inside WHERE statements, etc)
     *
     * @return string
     */
    public function GetName()
    {
        return $this->entity->GetName();
    }

    public function GetSQLContainerName()
    {
        // If this entity is not linked, just get the regular container name
        if(!$this->path->is_empty())
        {
            return $this->path->GetPathSQL();
        }
        else
        {
            return $this->GetDAL()->GetName();
        }
    }

    public function GetSQLNameWithTablePrefix()
    {
        return $this->GetSQLContainerName() . '.' . $this->GetName();
    }

    public function GetSQLName()
    {
        return $this->GetSQLContainerName() . '_' . $this->entity->GetName();
    }

    public function GetSelectStatementSQL()
    {
        return $this->GetSQLNameWithTablePrefix() . ' AS ' . $this->GetSQLName();
    }

    public function CleanValue($val)
    {
        return $this->entity->CleanValue($val);
    }

    public function IsEmpty($val) {
        return $this->entity->IsEmpty($val);
    }

    public function SetValue(DotCoreDataRecord $record, $value)
    {
        // return $this->entity->SetValue($this->GetLinkedRecord($record), $value);
        return $this->entity->SetValue($record, $value);
    }

    public function SetValueFromDAL(DotCoreDataRecord $record, $value)
    {
        // return $this->entity->SetValueFromDAL($this->GetLinkedRecord($record), $value);
        return $this->entity->SetValueFromDAL($record, $value);
    }

    public function GetValue(DotCoreDataRecord $record)
    {
        // return $this->entity->GetValue($this->GetLinkedRecord($record));
        return $this->entity->GetValue($record);
    }

    public function GetValuePreparedForQuery($val)
    {
        return $this->entity->GetValuePreparedForQuery($val);
    }

    public function GetLinkedRecord(DotCoreDataRecord $root_record)
    {
        $count_links = $this->path->count();
        for($i = 0; $i < $count_links; $i++)
        {
            $root_record = $root_record->GetLinkValue($this->path[$i]);
        }
        return $root_record;
    }

}

?>
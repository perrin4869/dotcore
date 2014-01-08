<?php

/**
 * DotCoreMin - Describes an entity used to get minimum values of entities inside DALs
 *
 * @author perrin
 */
class DotCoreMin extends DotCoreDALEntityBase {

    /**
     *
     * @param IDotCoreSelectableField $entity - The the entity whose minimum value is being queried
     * @param mixed $component
     */
    public function  __construct(IDotCoreDALSelectableEntity $entity) {
        $this->entity = $entity;
    }

    /**
     *
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
     * @return IDotCoreDALSelectableField
     */
    public function getEntity() {
        return $this->entity;
    }

    /**
     *
     * @param IDotCoreDALSelectableField $entity
     */
    public function setEntity(IDotCoreDALSelectableField $entity = NULL) {
        $this->entity = $entity;
    }


    /*
     * IDotCoreDALSelectableEntity implementation:
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
        return $this->GetSQLNameWithTablePrefix();
    }

    public function GetSQLContainerName()
    {
        return $this->entity->GetSQLContainerName();
    }

    public function GetSQLNameWithTablePrefix()
    {
        return 'MIN('.$this->entity->GetSQLNameWithTablePrefix().')';
    }

}
?>
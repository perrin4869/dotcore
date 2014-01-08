<?php

/**
 * DotCoreDateComponentEntity - Describes a component of a date for selection
 *
 * @author perrin
 */
class DotCoreDateComponentEntity extends DotCoreDALEntityBase {

    /**
     *
     * @param IDotCoreSelectableField $date_field
     * @param mixed $component
     */
    public function  __construct(IDotCoreDALSelectableEntity $date_field, $component) {
        $this->date_field = $date_field;
        $this->date_component = $component;
    }

    const DATE_COMPONENT_DAY = 'DAY';
    const DATE_COMPONENT_MONTH = 'MONTH';
    const DATE_COMPONENT_YEAR = 'YEAR';

    /**
     * Holds the date field whose component we want
     * @var IDotCoreSelectableField
     */
    private $date_field;

    /**
     * Holds the component of the date to extract
     * @var string
     */
    private $date_component;

    /*
     *
     * Accessors:
     *
     */

    /**
     *
     * @return IDotCoreDALSelectableField
     */
    public function getDateField() {
        return $this->date_field;
    }

    /**
     *
     * @param IDotCoreDALSelectableField $date_field
     */
    public function setDateField(IDotCoreDALSelectableField $date_field) {
        $this->date_field = $date_field;
    }

    public function getDateComponent() {
        return $this->date_component;
    }

    public function setDateComponent($date_component) {
        $this->date_component = $date_component;
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
        return $this->date_field->GetDAL();
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
        // If this entity is not linked, just get the regular container name
        return $this->date_field->GetSQLContainerName();
    }

    public function GetSQLNameWithTablePrefix()
    {
        return $this->date_component.'('.$this->date_field->GetSQLNameWithTablePrefix().')';
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

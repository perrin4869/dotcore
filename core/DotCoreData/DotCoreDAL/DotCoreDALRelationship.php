<?php

/**
 * DotCoreDALRelationship - Defines the base for all relationships between 2 Data Access Layer Classes
 *
 * @author perrin
 */
abstract class DotCoreDALRelationship extends DotCoreObject {

    /**
     * Constructor for DotCoreDALRelationship
     * @param string $link_name
     * @param DotCoreDAL $first_dal
     * @param DotCoreDAL $second_dal
     */
    public function  __construct($link_name, DotCoreDAL $first_dal, DotCoreDAL $second_dal) {
        $this->link_name = $link_name;
        $this->first_dal = $first_dal;
        $this->second_dal = $second_dal;
    }

    /**
     * Holds the DAL doing the linking
     * @var DotCoreDAL
     */
    private $first_dal = NULL;

    /**
     * Holds the liked DAL
     * @var DotCoreDAL
     */
    private $second_dal = NULL;

    /**
     * Holds the name of this link
     * @var string
     */
    private $link_name = NULL;

    /*
     *
     * Accessors:
     *
     */

    /**
     * Gets the first DAL in the relationship
     * @return DotCoreDAL
     */
    public function GetFirstDAL() {
        return $this->first_dal;
    }

    /**
     * Gets the second DAL in the relationship
     * @return DotCoreDAL
     */
    public function GetSecondDAL()
    {
        return $this->second_dal;
    }

    /**
     *
     * @param DotCoreDAL $dal
     * @return DotCoreDAL
     */
    public function GetOppositeDAL(DotCoreDAL $dal) {
        return $dal == $this->GetSecondDAL() ? $this->GetFirstDAL() : $this->GetSecondDAL();
    }

    /**
     * Gets the name of this link
     * @return string
     */
    public function GetRelationshipName() {
        return $this->link_name;
    }

    /**
     * Sets the name of the link to $name
     * @param string $name
     * @return DotCoreDALRelationship
     */
    public function SetRelationshipName($name) {
        $this->link_name = $name;
        return $this;
    }

    /**
     * This function is used to set the value of a link result into its respective object
     * @param DotCoreDataRecord $record
     * @param DotCoreDataRecord $value
     */
    public function SetLinkValue(DotCoreDataRecord $record, DotCoreDataRecord $value = NULL)
    {
        // Simple way to store a result, may be overriden
        $links_holder = &$record->GetRecordLinkValuesHolder();
        $link_name = $this->GetRelationshipName();

        // Store an empty record instead of NULL, for consistency
        if($value == NULL) {
            $value = $this->GetLinkedDAL()->GetNewRecord();
        }
        
        $links_holder[$link_name] = $value;
    }

    /**
     * Makes the relationship permanent throughout the rest of the application
     * 
     */
    public function MakePermanent() {}

    /**
     * Gets the statement that needs to be embedded in the join part of the SQL statement
     */
    abstract public function GetJoinStatement(
            DotCoreDAL $linking_dal,
            $linking_type,
            DotCoreDALPath $path = NULL,
            $custom_restraints = NULL);

    /**
     * Method used to save the changes made to the link
     */
    abstract public function Save(DotCoreDataRecord $record);

    /**
     * Checks whether the link value on $record is valid
     * @return boolean
     */
    public function Validate(DotCoreDataRecord $record) {
        return TRUE;
    }

    /**
     * Loads the linked value in record
     * @param DotCoreDataRecord $record
     * @param array of IDotCoreDALSelectableEntity $fields
     * @return DotCoreDataRecord
     */
    abstract public function LoadLinkValue(DotCoreDataRecord $record, $entities = NULL);

    /**
     * Unloads the link record pointed to by $record
     * @param DotCoreDataRecord $record
     * @return DotCoreDALRelationship
     */
    public function UnloadLinkValue(DotCoreDataRecord $record) {
        $links_holder = &$record->GetRecordLinkValuesHolder();
        unset($links_holder[$this->GetRelationshipName()]);
        return $this;
    }

}
?>
<?php

/**
 * Description of DotCoreDALLink
 *
 * @author perrin
 */
class DotCoreDALLink {

    /**
     * Constructor of DotCoreDALLink
     * If not $linking_dal is passed, one will automatically be added once the link is added to a tree
     * If the link is used for other purposes, a linking_dal must be provided manually
     * @param DotCoreDALRelationship $relationship
     * @param DotCoreDAL $linking_dal
     * @param string $link_type
     * @param mixed $additional_restraints
     * @param boolean $store_link_results
     */
    public function  __construct(
        DotCoreDALRelationship $relationship,
        DotCoreDAL $linking_dal = NULL,
        $link_type = self::LINK_TYPE_LEFT,
        $additional_restraints = NULL,
        $store_link_results = TRUE)
    {
        $this->relationship = $relationship;
        $this->linking_dal = $linking_dal;
        $this->link_type = $link_type;
        $this->additional_restraints = $additional_restraints;
        $this->store_link_results = $store_link_results;
    }

    const LINK_TYPE_INNER = 'INNER';
    const LINK_TYPE_LEFT = 'LEFT';
    const LINK_TYPE_RIGHT = 'RIGHT';

    /**
     * Holds the relationship by which the two dals are linked
     * @var DotCoreDALRelationship
     */
    private $relationship = NULL;

    /**
     * Holds the linking DAL of the link
     * @var DotCoreDAL
     */
    private $linking_dal = NULL;

    /**
     * Holds the way in which links are deployed
     * @var string
     */
    private $link_type = NULL;

    /**
     * Holds additional restraints over the link
     * @var mixed 
     */
    private $additional_restraints = NULL;

    /**
     * Defines whether or not the results of a query containing this link should be stored with the results or not
     * @var boolean
     */
    private $store_link_results = NULL;

    /*
     *
     * Accessors:
     *
     */

    /**
     *
     * @return DotCoreDALRelationship
     */
    public function GetRelationship() {
        return $this->relationship;
    }

    /**
     *
     * @param DotCoreDALRelationship $relationship
     */
    public function SetRelationship(DotCoreDALRelationship $relationship) {
        $this->relationship = $relationship;
    }

    /**
     *
     * @return DotCoreDAL
     */
    public function GetLinkingDAL() {
        return $this->linking_dal;
    }

    /**
     *
     * @param DotCoreDAL $linking_dal 
     */
    public function SetLinkingDAL(DotCoreDAL $linking_dal) {
        $this->linking_dal = $linking_dal;
    }

    public function IsLinkingDALSet() {
        return $this->linking_dal instanceof DotCoreDAL;
    }

    /**
     * @return string
     */
    public function GetLinkType() {
        if($this->link_type == NULL) {
            return self::LINK_TYPE_LEFT;
        }
        return $this->link_type;
    }

    /**
     *
     * @param string $link_type
     */
    public function SetLinkType($link_type) {
        $this->link_type = $link_type;
    }

    /**
     * @return mixed
     */
    public function GetAdditionalRestraints() {
        return $this->additional_restraints;
    }

    /**
     *
     * @param mixed $additional_restraints
     */
    public function SetAdditionalRestratints($additional_restraints) {
        $this->additional_restraints = $additional_restraints;
    }

    /**
     *
     * @return boolean
     */
    public function StoreLinkResults() {
        return $this->store_link_results;
    }

    /**
     *
     * @param boolean $bool
     */
    public function SetStoreLinkResults($bool) {
        $this->store_link_results = $bool;
    }

    /*
     *
     * Proxy methods:
     *
     */

    public function GetLinkName() {
        return $this->GetRelationship()->GetRelationshipName();
    }

    public function GetOppositeDAL() {
        return $this->GetRelationship()->GetOppositeDAL($this->GetLinkingDAL());
    }

    /*
     *
     * Methods
     *
     */

    public function GetStatement(DotCoreDALPath $path = NULL) {
        if(!$this->IsLinkingDALSet()) {
            throw new Exception('Can\'t get join statement from link because the linking DAL was not set.');
        }
        return $this->relationship->GetJoinStatement($this->GetLinkingDAL(), $this->GetLinkType(), $path, $this->GetAdditionalRestraints());
    }

}

?>
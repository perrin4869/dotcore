<?php

/**
 * DotCoreLinkBLL - Contains the business logic behind the links
 *
 * @author perrin
 */
class DotCoreLinkBLL extends DotCoreBLL {

    /*
     *
     * Fields accessors:
     *
     */

    /**
     * Gets the auto incrementing ID of this DAL
     * @return DotCoreAutoIncrementingKey
     */
    public function getFieldLinkID()
    {
        return $this->GetDAL()->GetField(DotCoreLinkDAL::LINK_ID);
    }

    /**
     * Gets the field that defines the title of the link
     * @return DotCorePlainStringField
     */
    public function getFieldTitle()
    {
        return $this->GetDAL()->GetField(DotCoreLinkDAL::LINK_TITLE);
    }

    /**
     * Gets the field that defines the url of the link
     * @return DotCoreURLField
     */
    public function getFieldUrl()
    {
        return $this->GetDAL()->GetField(DotCoreLinkDAL::LINK_URL);
    }

    /**
     * Gets the field that defines the description of the link
     * @return DotCoreHTMLStringField
     */
    public function getFieldDescription()
    {
        return $this->GetDAL()->GetField(DotCoreLinkDAL::LINK_DESCRIPTION);
    }

    /**
     * Gets the field that defines the logo of the link
     * @return DotCoreImageField
     */
    public function getFieldLogo()
    {
        return $this->GetDAL()->GetField(DotCoreLinkDAL::LINK_LOGO);
    }

    /**
     * Gets the field that defines the language of the link
     * @return DotCoreIntField
     */
    public function getFieldLanguageID()
    {
        return $this->GetDAL()->GetField(DotCoreLinkDAL::LINK_LANGUAGE_ID);
    }

    /**
     * Gets the field that defines the order of the link
     * @return DotCoreIntField
     */
    public function getFieldOrder()
    {
        return $this->GetDAL()->GetField(DotCoreLinkDAL::LINK_ORDER);
    }

    /*
     *
     * Abstract Methods Implementation:
     *
     */

    /**
     *
     * @return DotCoreLinkDAL
     */
    public static function GetDAL() {
        return self::GetDALHelper('DotCoreLinkDAL');
    }

    /*
     *
     * Busines Logic Methods:
     *
     */

    /**
     *
     * @param int $id
     * @return DotCoreLinkBLL
     */
    public function ByLinkID($id) {
        
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldLinkID(), $id));

        $this->Restraints($restraints);
        return $this;
    }

    /**
     *
     * @param int $lang_id
     * @return DotCoreLinkBLL
     */
    public function ByLanguageID($lang_id) {
        
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldLanguageID(), $lang_id));

        return $this->Restraints($restraints);
        return $this;
    }

    public function OrderedByLanguageAndOrder()
    {
        $links_order = new DotCoreDALSelectionOrder();
        $links_order
            ->AddOrderUnit(
                new DotCoreFieldSelectionOrder(
                    $this->getFieldLanguageID(),
                    DotCoreFieldSelectionOrder::DIRECTION_ASC))
            ->AddOrderUnit(
                new DotCoreFieldSelectionOrder(
                    $this->getFieldOrder(),
                    DotCoreFieldSelectionOrder::DIRECTION_ASC));
            
        $this->Order($links_order);
        return $this;
    }

    public function Ordered() {
        $links_order = new DotCoreDALSelectionOrder();
        $links_order
            ->AddOrderUnit(
                new DotCoreFieldSelectionOrder(
                    $this->getFieldOrder(),
                    DotCoreFieldSelectionOrder::DIRECTION_ASC));

        $this->Order($links_order);
        return $this;
    }

}
?>

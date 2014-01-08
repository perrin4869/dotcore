<?php

/**
 * DotCoreContentBLL - Contains the business logic behind the contents of the website
 *
 * @author perrin
 */
class DotCoreContentBLL extends DotCoreBLL {

    /*
     *
     * Fields accessors:
     *
     */

    /**
     * Gets the field that defines to which content in the template of
     * this content's page this content belongs
     * @return DotCoreIntField
     */
    public function getFieldTemplateContentID()
    {
        return $this->GetDAL()->GetField(DotCoreContentDAL::CONTENT_TEMPLATE_CONTENT_ID);
    }

    /**
     * Gets the field that defines to which page this content belongs
     * @return DotCoreIntField
     */
    public function getFieldContentPageID()
    {
        return $this->GetDAL()->GetField(DotCoreContentDAL::CONTENT_PAGE_ID);
    }

    /**
     * Gets the field that defines the text of the content
     * @return DotCoreStringField
     */
    public function getFieldText()
    {
        return $this->GetDAL()->GetField(DotCoreContentDAL::CONTENT_TEXT);
    }

    /**
     *
     * @return DotCoreDALFulltext
     */
    public function getContentFulltext() {
        return $this->GetDAL()->GetFulltext(DotCoreContentDAL::CONTENT_FULLTEXT);
    }

    /*
     *
     * Abstract Methods Implementation:
     *
     */

    /**
     *
     * @return DotCoreContentDAL
     */
    public static function GetDAL() {
        return self::GetDALHelper('DotCoreContentDAL');
    }
    
    /*
     * 
     * Link Methods
     * 
     */

    /**
     * Links the pages DAL
     *
     * @return DotCoreOneToManyRelationship
     */
    public function LinkPages() {
        $link = DotCoreDAL::GetRelationship(DotCoreContentDAL::PAGE_CONTENTS_LINK);
        $this->AddLink($link);
        return $link;
    }

    /**
     * Gets the page of this content
     *
     * @param DotCoreContentRecord $content
     * @return DotCorePageRecord
     */
    public static function GetPage(DotCoreContentRecord $content) {
        return $content->GetLinkValue(DotCoreContentDAL::PAGE_CONTENTS_LINK);
    }

    /*
     *
     * Busines Logic Methods:
     *
     */

    /**
     *
     * @param int $id
     * @return DotCorePageBLL
     */
    public function ByPageID($id)
    {
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldContentPageID(), $id));

        return $this->Restraints($restraints);
    }

}
?>

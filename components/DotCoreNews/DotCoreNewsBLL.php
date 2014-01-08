<?php

/**
 * DotCoreNewsBLL - Contains the business logic behind the news
 *
 * @author perrin
 */
class DotCoreNewsBLL extends DotCoreBLL {

    /*
     *
     * Abstract Methods Implementation:
     *
     */

    /**
     *
     * @return DotCoreNewsDAL
     */
    public static function GetDAL() {
        return self::GetDALHelper('DotCoreNewsDAL');
    }

    /*
     *
     * Fields accessors:
     *
     */

    /**
     * Gets the auto incrementing ID of this DAL
     * @return DotCoreAutoIncrementingKey
     */
    public function getFieldNewsID()
    {
        return $this->GetDAL()->GetField(DotCoreNewsDAL::NEWS_ID);
    }

    /**
     * Gets the field that defines the title of the news
     * @return DotCoreStringField
     */
    public function getFieldTitle()
    {
        return $this->GetDAL()->GetField(DotCoreNewsDAL::NEWS_TITLE);
    }

    /**
     * Gets the field that defines the short content of the news
     * @return DotCoreStringField
     */
    public function getFieldShortContent()
    {
        return $this->GetDAL()->GetField(DotCoreNewsDAL::NEWS_SHORT_CONTENT);
    }

    /**
     * Gets the field that defines the content of the news
     * @return DotCoreStringField
     */
    public function getFieldContent()
    {
        return $this->GetDAL()->GetField(DotCoreNewsDAL::NEWS_CONTENT);
    }

    /**
     * Gets the field that defines the date in which the news was written
     * @return DotCoreTimestampField
     */
    public function getFieldDate()
    {
        return $this->GetDAL()->GetField(DotCoreNewsDAL::NEWS_DATE);
    }

    /**
     * Gets the field which defines the language of the news
     * @return DotCoreIntField
     */
    public function getFieldLanguageID()
    {
        return $this->GetDAL()->GetField(DotCoreNewsDAL::NEWS_LANGUAGE_ID);
    }

    /*
     *
     * Busines Logic Methods:
     *
     */

    /**
     *
     * @param int $id
     * @return DotCoreNewsBLL
     */
    public function ByNewsID($id) {
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldNewsID(), $id));

        return $this->Restraints($restraints);
    }

    /**
     *
     * @param int $lang_id
     * @return DotCoreNewsBLL
     */
    public function ByNewsLanguageID($lang_id) {
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldLanguageID(), $lang_id));

        return $this->Restraints($restraints);
    }

}
?>

<?php

/**
 * DotCoreLanguageBLL - Contains the business logic of the languages
 *
 * @author perrin
 */
class DotCoreLanguageBLL extends DotCoreBLL {

    /*
     *
     * Properties:
     *
     */

    private static $is_multilanguage = NULL;

    /*
     *
     * Fields accessors
     *
     */

    /**
     * Gets the field that defines the autoincrementing ID of languages
     * @return DotCoreAutoIncrementingKey
     */
    public function getFieldLanguageID()
    {
        return $this->GetDAL()->GetField(DotCoreLanguageDAL::LANGUAGE_ID);
    }

    /**
     * Gets the field that contains the ISO 639-1 code of the language (i.e., the first 2 letters)
     * @return DotCoreStringField
     */
    public function getFieldLanguageCode()
    {
        return $this->GetDAL()->GetField(DotCoreLanguageDAL::LANGUAGE_CODE);
    }

    /**
     * Gets the field that contains the direction (rtl or ltr) of the language
     * @return DotCoreBooleanField
     */
    public function getFieldDirection()
    {
        return $this->GetDAL()->GetField(DotCoreLanguageDAL::LANGUAGE_DIRECTION);
    }

    /**
     * Gets the field that contains the direction default page that gets loaded when a certain language is requested
     * @return DotCoreIntField
     */
    public function getFieldDefaultPage()
    {
        return $this->GetDAL()->GetField(DotCoreLanguageDAL::LANGUAGE_DEFAULT_PAGE_ID);
    }

    /*
     *
     * Abstract Methods Implementation:
     *
     */

    /**
     *
     * @return DotCoreLanguageDAL
     */
    public static function GetDAL() {
        return self::GetDALHelper('DotCoreLanguageDAL');
    }

    /**
     * Cache the array of languages
     * @var array
     */
    private static $languages_array = NULL;

    /**
     * Cache the ID dictionary
     * @var array
     */
    private static $languages_id_dictionary = NULL;

    /**
     * Cache the code dictionary
     * @var array
     */
    private static $languages_code_dictionary = NULL;

    /**
     * Gets a language record by the ID given
     * @param int $id
     * @return DotCoreLanguageBLL
     */
    public function ByLanguageID($id)
    {
        $restraint = new DotCoreDALRestraint();
        $restraint->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldLanguageID(), $id));
        // The fields needed can be specified before calling this function
        return $records = $this->Restraints($restraint);
    }

    /**
     * Gets a language record by the code given
     * @param string $code
     * @return DotCoreLanguageBLL
     */
    public function ByLanguageCode($code)
    {
        $restraint = new DotCoreDALRestraint();
        $restraint->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldLanguageCode(), $code));
        // The fields needed can be specified before calling this function
        return $this->Restraints($restraint);
    }

    public static function GetAllLanguages() {
        if(self::$languages_array === NULL) {
            $languages_bll = new DotCoreLanguageBLL();
            $dal = $languages_bll->GetDAL();
            self::$languages_array = $languages_bll
                ->Fields($dal->GetFieldsDefinitions())
                ->Select();
        }
        return self::$languages_array;
    }

    public static function GetLanguagesIDDictionary()
    {
        // If it's not cached yet
        if(self::$languages_id_dictionary == NULL)
        {
            $languages_bll = new DotCoreLanguageBLL();
            $dal = $languages_bll->GetDAL();
            self::$languages_id_dictionary = $languages_bll
                ->Fields($dal->GetFieldsDefinitions())
                ->SelectDictionary($languages_bll->getFieldLanguageID());
        }
        return self::$languages_id_dictionary;
    }

    public function GetLanguagesCodeDictionary()
    {
        // If it's not cached yet
        if(self::$languages_code_dictionary == NULL)
        {
            $languages_bll = new DotCoreLanguageBLL();
            $dal = $languages_bll->GetDAL();
            self::$languages_code_dictionary = $languages_bll
                ->Fields($dal->GetFieldsDefinitions())
                ->SelectDictionary($languages_bll->getFieldLanguageCode());
        }
        return self::$languages_code_dictionary;
    }

    /**
     * Checks whether there is more than one language installed
     *
     * @return boolean
     */
    public static function IsMultilanguage() {
        if(self::$is_multilanguage === NULL) {
            $lang_bll = new DotCoreLanguageBLL();
            self::$is_multilanguage = $lang_bll->GetCount() > 1;
        }
        return self::$is_multilanguage;
    }

}
?>

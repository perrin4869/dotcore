<?php

/**
 * DotCoreRoleMultilangBLL - Contains the business logic of the roles multilanguage properties
 *
 * @author perrin
 */
class DotCoreRoleMultilangBLL extends DotCoreBLL {

    /*
     *
     * Abstract Methods Implementation:
     *
     */

    public static function GetDAL() {
        return self::GetDALHelper('DotCoreRoleMultilangDAL');
    }

    /*
     *
     * Fields accessors
     *
     */

    /**
     * Gets the field that defines the role of this multilanguage object
     * @return DotCoreIntField
     */
    public function getFieldRoleID()
    {
        return $this->GetDAL()->GetField(DotCoreRoleMultilangDAL::ROLE_ID);
    }

    /**
     * Gets the field that defines the language of this multilanguage object
     * @return DotCoreIntField
     */
    public function getFieldLanguageID(){
        return $this->GetDAL()->GetField(DotCoreRoleMultilangDAL::LANGUAGE_ID);
    }

    /**
     * Gets the field that defines the name of the role
     * @return DotCoreStringField
     */
    public function getFieldRoleName()
    {
        return $this->GetDAL()->GetField(DotCoreRoleMultilangDAL::ROLE_NAME);
    }

}
?>

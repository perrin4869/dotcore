<?php

class PermissionDeniedException extends Exception
{
    private $required_role;

    public function __construct($required_role, $message = '')
    {
        if(empty($message))
        {
            $message = $required_role . ' role is required to do this action.';
        }
        parent::__construct($message);
        $this->required_role = $required_role;
    }

    public function getRequiredRole()
    {
        return $this->required_role;
    }
}

/**
 * DotCoreRoleBLL - Contains the business logic of the roles
 *
 * @author perrin
 */
class DotCoreRoleBLL extends DotCoreBLL {

    /*
     *
     * Abstract Methods Implementation:
     *
     */

    /**
     *
     * @return DotCoreRoleDAL
     */
    public static function GetDAL() {
        return self::GetDALHelper('DotCoreRoleDAL');
    }

    /*
     *
     * Fields accessors
     *
     */

    /**
     * Gets the field that defines the autoincrementing ID of roles
     * @return DotCoreAutoIncrementingKey
     */
    public function getFieldRoleID()
    {
        return $this->GetDAL()->GetField(DotCoreRoleDAL::ROLES_ID);
    }

    /**
     * Gets the field that defines the name of the roles
     * @return DotCoreStringField
     */
    public function getFieldDesc()
    {
        return $this->GetDAL()->GetField(DotCoreRoleDAL::ROLES_DESC);
    }
    
    /*
     *
     * Link Methods:
     *
     */

    public static function GetRolesMultilangRelationship() {
        return DotCoreDAL::GetRelationship(DotCoreRoleMultilangDAL::ROLE_MULTILANG_LINK);
    }

    /**
     * Links the roles multilanguage DAL
     *
     * @return DotCoreOneToManyRelationship
     */
    public function LinkRolesMultilang() {
        $link = self::GetRolesMultilangRelationship();
        $this->AddLink($link);
        return $link;
    }

    /**
     * Gets the multilanguage properties of roles
     * @return array
     */
    public static function GetRolesMultilanguageProperties(DotCoreRoleRecord $role) {
        return $role->GetLinkValue(DotCoreRoleMultilangDAL::ROLE_MULTILANG_LINK);
    }

    /**
     * Cache the ID dictionary
     * @var array
     */
    private static $roles_id_dictionary = NULL;

    /**
     *
     * @param string $desc
     * @return DotCoreRoleBLL
     */
    public function ByRoleDescription($desc)
    {
        $restraint = new DotCoreDALRestraint();
        $restraint->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldDesc(), $desc));

        return $this->Restraints($restraint);
    }

    public function GetRoleCountByName($name)
    {
        $dal = $this->GetDAL();
        
        $restraint = new DotCoreDALRestraint();
        $restraint->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldName(), $name, DotCoreFieldRestraint::OPERATION_EQUALS));
        
        return $dal
            ->Restraints($restraint)
            ->GetCount();
    }

    public function GetRolesIDDictionary()
    {
        // If it's not cached yet
        if(self::$roles_id_dictionary == NULL)
        {
            self::$roles_id_dictionary = $this->SelectDictionary($this->getFieldRoleID());
        }
        return self::$roles_id_dictionary;
    }

}
?>

<?php

/**
 * Description of DotCoreAdminBLL
 *
 * @author perrin
 */
class DotCoreAdminBLL extends DotCoreBLL {

    /*
     *
     * Properties:
     *
     */

    const COOKIE_ADMIN_USERNAME = 'admin_username_cookie';
    const COOKIE_ADMIN_PASSWORD = 'admin_password_cookie';

    /**
     * Stores the currently active admin
     * @var DotCoreAdminRecord
     */
    public static $current_admin = NULL;

    /**
     * Stores an Admin BLL for internal use
     * @var DotCoreAdminBLL
     */
    private static $admin_bll = NULL;

    /*
     *
     * Abstract implementations:
     *
     */

    /**
     *
     * @return DotCoreAdminDAL
     */
    public static function GetDAL() {
        return self::GetDALHelper('DotCoreAdminDAL');
    }

     /*
     *
     * Fields accessors
     *
     */

    /**
     * Gets the field that defines the user from which this admin is built
     * @return DotCoreIntField
     */
    public function getFieldAdminID()
    {
        return $this->GetDAL()->GetField(DotCoreAdminDAL::ADMIN_ID);
    }

    /**
     * Gets the field that defines whether the admin is advanced or not
     * @return DotCoreBooleanField
     */
    public function getFieldIsAdvanced()
    {
        return $this->GetDAL()->GetField(DotCoreAdminDAL::ADMIN_ADVANCED);
    }

    /*
     *
     * Links:
     *
     */

    public static function GetAdminUserRelationship() {
        return DotCoreDAL::GetRelationship(DotCoreAdminDAL::USER_ADMIN_LINK);
    }

    public static function GetAdminRolesRelationship() {
        return DotCoreDAL::GetRelationship(DotCoreRoleDAL::ADMIN_ROLES_LINK);
    }

    /**
     * Links a Users DAL to this admins DAL, and returns the link
     *
     * @return DotCoreOneToOneRelationship
     */
    public function LinkUsers() {
        $link = self::GetAdminUserRelationship();
        $this->AddLink($link);
        return $link;
    }

    /**
     * Links a Roles DAL to this admins DAL, and returns the link
     *
     * @return DotCoreManyToManyRelationship
     */
    public function LinkRoles() {
        $link = self::GetAdminRolesRelationship();
        $this->AddLink($link);
        return $link;
    }

    /*
     *
     * Links accessors
     *
     */

    /**
     * Gets the user info of this admin
     * @return DotCoreUserRecord
     */
    public static function GetUser(DotCoreAdminRecord $admin){
        return $admin->GetLinkValue(DotCoreAdminDAL::USER_ADMIN_LINK);
    }

    public static function SetUser(DotCoreAdminRecord $admin, DotCoreUserRecord $user) {
        $admin->SetLinkValue(DotCoreAdminDAL::USER_ADMIN_LINK, $user);
    }

    /**
     * Gets the roles of this admin
     * @return array
     */
    public static function GetRoles(DotCoreAdminRecord $admin){
        return $admin->GetLinkValue(DotCoreRoleDAL::ADMIN_ROLES_LINK);
    }

    public static function ExchangeRoles(DotCoreAdminRecord $admin, $roles){
        $roles_link = self::GetAdminRolesRelationship();
        $roles_link->SetLinkValue($admin, $roles);
    }

    /*
     *
     * Business Logic Methods:
     *
     */

    /**
     * Gets the static Admin BLL
     * @return DotCoreAdminBLL
     */
    public static function GetAdminBLL() {
        if(self::$admin_bll == NULL) {
            self::$admin_bll = new DotCoreAdminBLL();
            self::$admin_bll->LinkUsers();
            self::$admin_bll->LinkRoles();
        }
        else {
            self::$admin_bll->LinkUsers();
            self::$admin_bll->LinkRoles();
        }
        return self::$admin_bll;
    }

    /**
     * Returns the admin by the ID $id
     * @param int $id
     * @return DotCoreAdminRecord
     */
    public function ByAdminID($id) {
        $restraint = new DotCoreDALRestraint();
        $restraint->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldAdminID(), $id));

        return $this->Restraints($restraint);
    }

    public static function IsInRole(DotCoreAdminRecord $admin, $role_name) {
        $admins_roles = self::GetRoles($admin);
        if($admins_roles) {
            foreach($admins_roles as $role) {
                if($role->getRoleDesc() == $role_name) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    /**
     * Used to check whether the username and password pair given are valid
     * On being valid, it returns the user associated with the login, else it returns NULL
     * @param string $username
     * @param string $password
     * @return DotCoreUserRecord
     */
    public static function IsCorrectLogin($username, $password)
    {
        $admin = NULL;
        $user_bll = new DotCoreUserBLL();
        $user_dal = $user_bll->GetDAL();
        $user = $user_bll
            ->Fields($user_dal->GetFieldsDefinitions())
            ->ByUsernameAndPassword($username, $password)
            ->SelectFirstOrNull();
        if($user)
        {
            $admin = self::GetGlobalAdminByUser($user);
        }
        return $admin;
    }

    public static function Login(DotCoreAdminRecord $admin) {
        $user = self::GetUser($admin);
        $username = $user->getUsername();
        $expiration = time() + 60 * 60 * 24 * 365;
        setcookie(self::COOKIE_ADMIN_USERNAME, $username, $expiration, '/');
        // I need the global cookie to be recognized right away, or the rest of the class may behave oddly!
        $_COOKIE[self::COOKIE_ADMIN_USERNAME] = $username;

        $password_cookie = md5($username . DotCoreConfig::$SECRET_STRING);
        setcookie(self::COOKIE_ADMIN_PASSWORD, $password_cookie, $expiration, '/');
        $_COOKIE[self::COOKIE_ADMIN_PASSWORD] = $password_cookie;

        $user->setUserLastLogin(time());
        $user_bll = new DotCoreUserBLL();
        $user_bll->Save($user);

        self::$current_admin = $admin;
    }

    public static function Logoff() {
        setcookie(self::COOKIE_ADMIN_USERNAME, '', time()-3600, '/');
        setcookie(self::COOKIE_ADMIN_PASSWORD, '', time()-3600, '/');
    }

    /**
     * Gets the current admin
     * @return DotCoreAdminRecord
     */
    public static function &GetCurrent() {
        if(self::$current_admin == NULL) {
            $username = $_COOKIE[self::COOKIE_ADMIN_USERNAME];
            // No need to clean, it's just checked against this password, no problem with the DB
            $password = $_COOKIE[self::COOKIE_ADMIN_PASSWORD];

            if(md5($username . DotCoreConfig::$SECRET_STRING) == $password)
            {
                // If the user does not exist, there's nothing to do!
                $user_bll = new DotCoreUserBLL();
                $user_dal = $user_bll->GetDAL();
                $user = $user_bll
                    ->Fields($user_dal->GetFieldsDefinitions())
                    ->ByUsername($username)
                    ->SelectFirstOrNull();
                if($user != NULL)
                {
                    self::$current_admin = self::GetGlobalAdminByUser($user);
                }
            }

            if(self::$current_admin == NULL)
            {
                $admin_bll = self::GetAdminBLL();
                self::$current_admin = $admin_bll->GetNewRecord(); // No user currently - give him a new user
            }
        }

        return self::$current_admin;
    }

    protected static function GetGlobalAdminByUser(DotCoreUserRecord $user) {
        $admin_bll = self::GetAdminBLL();
        $roles_path = DotCoreRoleDAL::ADMIN_ROLES_LINK.'.';

        $admin = $admin_bll
            ->Fields(
                array(
                    $admin_bll->getFieldIsAdvanced(),
                    $roles_path.DotCoreRoleDAL::ROLES_DESC
                )
             )
            ->ByAdminID($user->getUserID())
            ->SelectFirstOrNull();
        

        if($admin != NULL)
        {
            $admin_bll->SetUser($admin, $user);
        }
        
        // Finalize the selection, as this BLL is not deleted, so we need to do this manually
        // Finalize the selection only AFTER finishing using the BLL
        $admin_bll->FinalizeSelection();

        return $admin;
    }

}
?>

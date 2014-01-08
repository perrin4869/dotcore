<?php

class InvalidOldPasswordException extends DotCoreException {}
class PasswordsNotMatchingException extends DotCoreException {}

/**
 * DotCoreUserBLL - Contains the business logic of the users
 *
 * @author perrin
 */
class DotCoreUserBLL extends DotCoreBLL {

    /**
     *
     * @return DotCoreUserDAL
     */
    public static function GetDAL()
    {
        return self::GetDALHelper('DotCoreUserDAL');
    }

    const COOKIE_USERNAME = 'dotcore_username';
    const COOKIE_PASSWORD = 'dotcore_password';
    
    /**
     * Holds the user curretly browsing the webpage
     * @var DotCoreUserRecord
     */
    private static $current_user;

    /**
     * Holds a static user BLL for internal uses
     * @var DotCoreUserBLL
     */
    private static $user_bll = NULL;

    /*
     *
     * Fields accessors
     *
     */

    /**
     * Gets the field that defines the autoincrementing ID of users
     * @return DotCoreAutoIncrementingKey
     */
    public function getFieldUserID()
    {
        return $this->GetDAL()->GetField(DotCoreUserDAL::USER_ID);
    }

    /**
     * Gets the field that defines the username of users
     * @return DotCoreStringField
     */
    public function getFieldUserName()
    {
        return $this->GetDAL()->GetField(DotCoreUserDAL::USER_NAME);
    }

    /**
     * Gets the field that defines the password of users
     * @return DotCorePasswordField
     */
    public function getFieldPassword()
    {
        return $this->GetDAL()->GetField(DotCoreUserDAL::USER_PASSWORD);
    }

    /**
     * Gets the field that defines the email of users
     * @return DotCoreEmailField
     */
    public function getFieldEmail()
    {
        return $this->GetDAL()->GetField(DotCoreUserDAL::USER_EMAIL);
    }

    /**
     * Gets the field that defines the first name of users
     * @return DotCoreStringField
     */
    public function getFieldFirstName()
    {
        return $this->GetDAL()->GetField(DotCoreUserDAL::USER_FIRST_NAME);
    }

    /**
     * Gets the field that defines the last name of users
     * @return DotCoreStringField
     */
    public function getFieldLastName()
    {
        return $this->GetDAL()->GetField(DotCoreUserDAL::USER_LAST_NAME);
    }

    /**
     * Gets the field that defines the phone of users
     * @return DotCoreStringField
     */
    public function getFieldPhone()
    {
        return $this->GetDAL()->GetField(DotCoreUserDAL::USER_PHONE);
    }

    /**
     * Gets the field that defines the last login timestamp of users
     * @return DotCoreTimestampField
     */
    public function getFieldLastLogin()
    {
        return $this->GetDAL()->GetField(DotCoreUserDAL::USER_LAST_LOGIN);
    }

    /**
     * Gets the field that defines the date the user was created as a timestamp
     * @return DotCoreTimestampField
     */
    public function getFieldDateCreated()
    {
        return $this->GetDAL()->GetField(DotCoreUserDAL::USER_DATE_CREATED);
    }

    /*
     *
     * DotCoreUser Methods
     *
     */

    /**
     * Gets the user bll used internally by this BLL
     * @return DotCoreUserBLL
     */
    private static function GetUserBLL() {
        if(self::$user_bll == NULL)
        {
            self::$user_bll = new DotCoreUserBLL();
        }
        else
        {
            self::$user_bll->FinalizeSelection();
        }
        return self::$user_bll;
    }

    /**
     * Returns the user that matches the username and the (unencrypted), or NULL if there's no match
     * @param string $username
     * @param string $password unencrypted
     * @return DotCoreUserBLL
     */
    public function ByUsernameAndPassword($username, $password)
    {
        // The default operation is equals
        $restraint = new DotCoreDALRestraint();
        $restraint
        ->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldUserName(), $username)
        )
        ->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldPassword(), $this->getFieldPassword()->Encrypt($password))
        );
        
        return $this->Restraints($restraint);
    }

    /**
     * Returns the user that matches the username given in $username
     * @param string $username
     * @return DotCoreUserBLL
     */
    public function ByUsername($username)
    {
        $restraint = new DotCoreDALRestraint();
        $restraint
            ->AddRestraint(
                new DotCoreFieldRestraint($this->getFieldUserName(), $username)
            );

        return  $this->Restraints($restraint);
    }

    public function ByUserID($id) {
        $restraint = new DotCoreDALRestraint();
        $restraint
        ->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldUserID(), $id)
        );

        return $this->Restraints($restraint);
    }

    // Login functions

    /**
     * Gets the record of the user currently browsing the webpage
     * @return DotCoreUserRecord
     */
    public static function GetCurrent()
    {
        if(self::$current_user === NULL)
        {
            $username = $_COOKIE[self::COOKIE_USERNAME];
            // No need to clean, it's just checked against this password, no problem with the DB
            $password = $_COOKIE[self::COOKIE_PASSWORD];

            $user_bll = self::GetUserBLL();
            $dal = $user_bll->GetDAL();
            if(md5($username . DotCoreConfig::$SECRET_STRING) == $password)
            {
                // If the user does not exist, there's nothing to do!
                self::$current_user = $user_bll
                    ->Fields($dal->GetFieldsDefinitions())
                    ->ByUsername($username)
                    ->SelectFirstOrNull();
            }

            if(self::$current_user == NULL)
            {
                self::$current_user = $user_bll->GetNewRecord(); // No user currently - give him a new user
            }
            else
            {
                // Don't forget to update the last login time
                self::$current_user->setUserLastLogin(time());
                $user_bll->Save(self::$current_user);
            }
        }
        return self::$current_user;
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
        $user_bll = self::GetUserBLL();
        $user = $user_bll->ByUsernameAndPassword($username, $password)->SelectFirstOrNull();
        return $user;
    }

    /**
     * Tries to login based on the username and password given.
     * @return void
     */
    public static function Login(DotCoreUserRecord $user)
    {
        $username = $user->getUsername();
        $expiration = time() + 60 * 60 * 24 * 365;
        setcookie(self::COOKIE_USERNAME, $username, $expiration, '/');
        // I need the global cookie to be recognized right away, or the rest of the class may behave oddly!
        $_COOKIE[self::COOKIE_USERNAME] = $username;

        $passwordCookie = md5($username . DotCoreConfig::$SECRET_STRING);
        setcookie(self::COOKIE_PASSWORD, $passwordCookie, $expiration, '/');
        $_COOKIE[self::COOKIE_PASSWORD] = $passwordCookie;

        $user->setUserLastLogin(time());
        $user_bll = self::GetUserBLL();
        $user_bll->Save($user);

        self::$current_user = $user;
    }

    public static function Logoff()
    {
        // Remove any cookies inserted
        setcookie(self::COOKIE_USERNAME, '', time()-3600, '/');
        setcookie(self::COOKIE_PASSWORD, '', time()-3600, '/');
    }

}
?>

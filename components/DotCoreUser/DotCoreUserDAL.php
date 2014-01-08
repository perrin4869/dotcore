<?php

/**
 * DotCoreUserDAL - MySQL DAL for the users
 *
 * @author perrin
 */
class DotCoreUserDAL extends DotCoreDAL {

    public function  __construct()
    {
        parent::__construct(self::USER_TABLE);

        $username_field = new DotCorePlainStringField(self::USER_NAME, $this, FALSE);

        $this->AddField(new DotCoreAutoIncrementingKey(self::USER_ID, $this));
        $this->AddField($username_field);
        $this->AddField(new DotCorePasswordField(self::USER_PASSWORD, $this, FALSE));
        $this->AddField(new DotCoreEmailField(self::USER_EMAIL, $this, TRUE));
        $this->AddField(new DotCorePlainStringField(self::USER_FIRST_NAME, $this, TRUE));
        $this->AddField(new DotCorePlainStringField(self::USER_LAST_NAME, $this, TRUE));
        $this->AddField(new DotCorePlainStringField(self::USER_PHONE, $this, TRUE));
        $this->AddField(new DotCoreTimestampField(self::USER_LAST_LOGIN, $this, TRUE));
        $this->AddField(new DotCoreTimestampField(self::USER_DATE_CREATED, $this, FALSE));

        $this->AddUniqueKey(self::USER_UNIQUE_USERNAME, $username_field);

        $this->SetPrimaryField(self::USER_ID);
    }

    /**
     *
     * @return DotCoreUserDAL
     */
    public static function GetInstance()
    {
        return parent::GetDALInstance(__CLASS__);
    }

    const USER_TABLE = 'dotcore_users';

    const USER_ID = 'user_id';
    const USER_NAME  = 'username';
    const USER_PASSWORD = 'password';
    const USER_EMAIL = 'email';
    const USER_FIRST_NAME = 'first_name';
    const USER_LAST_NAME = 'last_name';
    const USER_PHONE = 'phone';
    const USER_LAST_LOGIN = 'last_login';
    const USER_DATE_CREATED = 'date_created';

    const USER_UNIQUE_USERNAME = 'username';

    /**
     * Returns a record of DotCoreUserDAL
     * @return DotCoreUserRecord
     */
    public function GetRecord()
    {
        return new DotCoreUserRecord($this);
    }

    public function GetNewRecord()
    {
        $new_user = parent::GetNewRecord();
        $new_user->setUserDateCreated(time());
        $new_user->setUserLastLogin(NULL);
        $new_user->setUserFirstName(NULL);
        $new_user->setUserLastName(NULL);
        $new_user->setUserPhone(NULL);
        return $new_user;
    }

}
?>

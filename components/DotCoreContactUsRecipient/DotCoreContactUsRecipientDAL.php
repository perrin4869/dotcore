<?php

/**
 * DotCoreContactUsRecipientDAL - Implements the data access logic for the contact us recipients of this website
 *
 * @author perrin
 */
class DotCoreContactUsRecipientDAL extends DotCoreDAL {

    public function  __construct()
    {
        parent::__construct(self::CONTACT_US_RECIPIENT_TABLE);

        $email_field = new DotCoreEmailField(self::CONTACT_US_RECIPIENT_EMAIL, $this, FALSE);
        $language_field = new DotCoreIntField(self::CONTACT_US_RECIPIENT_LANGUAGE_ID, $this, FALSE);

        $this->AddField(new DotCoreAutoIncrementingKey(self::CONTACT_US_RECIPIENT_ID, $this));
        $this->AddField(new DotCorePlainStringField(self::CONTACT_US_RECIPIENT_NAME, $this, FALSE));
        $this->AddField($email_field);
        $this->AddField($language_field);

        $this->AddUniqueKey(
            self::CONTACT_US_UNIQUE_EMAIL_KEY,
            array($email_field,$language_field));

        $this->SetPrimaryField(self::CONTACT_US_RECIPIENT_ID);
    }

    /**
     *
     * @return DotCoreContactUsRecipientDAL
     */
    public static function GetInstance()
    {
        return parent::GetDALInstance(__CLASS__);
    }

    const CONTACT_US_RECIPIENT_TABLE = 'dotcore_contact_us_recipient';

    const CONTACT_US_RECIPIENT_ID = 'contact_us_recipient_id';
    const CONTACT_US_RECIPIENT_NAME = 'contact_us_recipient_name';
    const CONTACT_US_RECIPIENT_EMAIL = 'contact_us_recipient_email';
    const CONTACT_US_RECIPIENT_LANGUAGE_ID = 'contact_us_recipient_language_id';

    const LANGUAGE_LINK = 'language_contact_us_recipient_link';

    const CONTACT_US_UNIQUE_EMAIL_KEY = 'contact_us_recipient_unique_email_key';

    /**
     * Returns a record of DotCoreContactUsRecipientRecord
     * @return DotCoreContactUsRecipientRecord
     */
    public function GetRecord()
    {
        return new DotCoreContactUsRecipientRecord($this);
    }

}

DotCoreDAL::AddRelationship(
        new DotCoreOneToManyRelationship(
            DotCoreContactUsRecipientDAL::LANGUAGE_LINK,
            DotCoreLanguageDAL::GetInstance()->GetField(DotCoreLanguageDAL::LANGUAGE_ID),
            DotCoreContactUsRecipientDAL::GetInstance()->GetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_LANGUAGE_ID)
        )
    );

?>
<?php

/**
 * DotCoreContactMemberDAL - Implements the data access logic for the contact members of this website
 *
 * @author perrin
 */
class DotCoreContactMemberDAL extends DotCoreDAL {

    public function  __construct()
    {
        parent::__construct(self::CONTACT_MEMBER_TABLE);

        $email_field = new DotCoreEmailField(self::CONTACT_MEMBER_EMAIL, $this, FALSE);

        $this->AddField(new DotCoreAutoIncrementingKey(self::CONTACT_MEMBER_ID, $this));
        $this->AddField($email_field);
        $this->AddField(new DotCoreTimestampField(self::CONTACT_MEMBER_DATE_ADDED, $this, FALSE));
        $this->AddField(new DotCoreIntField(self::CONTACT_MEMBER_LANGUAGE_ID, $this, FALSE));

        $this->AddUniqueKey(self::CONTACT_MEMBER_UNIQUE_EMAIL, $email_field);

        $this->SetPrimaryField(self::CONTACT_MEMBER_ID);
    }

    /**
     *
     * @return DotCoreContactMemberDAL
     */
    public static function GetInstance()
    {
        return parent::GetDALInstance(__CLASS__);
    }

    const CONTACT_MEMBER_TABLE = 'dotcore_contact_list';

    const CONTACT_MEMBER_ID = 'contact_id';
    const CONTACT_MEMBER_EMAIL = 'email';
    const CONTACT_MEMBER_DATE_ADDED = 'date_added';
    const CONTACT_MEMBER_LANGUAGE_ID = 'language_id';

    const LANGUAGE_LINK = 'language_contact_member_link';

    const CONTACT_MEMBER_UNIQUE_EMAIL = 'email';

    /**
     * Returns a record of DotCoreContactMemberRecord
     * @return DotCoreContactMemberRecord
     */
    public function GetRecord()
    {
        return new DotCoreContactMemberRecord($this);
    }

    public function GetNewRecord() {
        $new_record = parent::GetNewRecord();
        $new_record->setContactMemberDateAdded(time());
        return $new_record;
    }

}

DotCoreDAL::AddRelationship(
        new DotCoreOneToManyRelationship(
            DotCoreContactMemberDAL::LANGUAGE_LINK,
            DotCoreLanguageDAL::GetInstance()->GetField(DotCoreLanguageDAL::LANGUAGE_ID),
            DotCoreContactMemberDAL::GetInstance()->GetField(DotCoreContactMemberDAL::CONTACT_MEMBER_LANGUAGE_ID)
        )
    );

?>
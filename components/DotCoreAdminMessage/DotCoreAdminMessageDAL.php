<?php

/**
 * DotCoreAdminMessageDAL - Defines simple public messages left by admins
 *
 * @author perrin
 */
class DotCoreAdminMessageDAL extends DotCoreDAL {

    public function  __construct()
    {
        parent::__construct(self::ADMIN_MESSAGE_TABLE);

        $this->AddField(new DotCoreAutoIncrementingKey(self::ADMIN_MESSAGE_ID, $this));
        $this->AddField(new DotCoreMultilineStringField(self::ADMIN_MESSAGE_TEXT, $this, FALSE));
        $this->AddField(new DotCoreIntField(self::ADMIN_MESSAGE_ADMIN_ID, $this, FALSE));
        $this->AddField(new DotCoreDateTimeField(self::ADMIN_MESSAGE_DATETIME, $this, FALSE));

        $this->SetPrimaryField(self::ADMIN_MESSAGE_ID);
    }

    /**
     *
     * @return DotCoreAdminMessageDAL
     */
    public static function GetInstance()
    {
        return parent::GetDALInstance(__CLASS__);
    }

    const ADMIN_MESSAGE_TABLE = 'dotcore_admin_messages';

    const ADMIN_MESSAGE_ID = 'admin_message_id';
    const ADMIN_MESSAGE_TEXT = 'admin_message_text';
    const ADMIN_MESSAGE_DATETIME = 'admin_message_datetime';
    const ADMIN_MESSAGE_ADMIN_ID = 'admin_message_admin_id';

    const ADMIN_LINK = 'admin_admin_message_link';

    /**
     * Returns a record of DotCoreAdminMessageRecord
     * @return DotCoreAdminMessageRecord
     */
    public function GetRecord()
    {
        return new DotCoreAdminMessageRecord($this);
    }

    
    public function GetNewRecord() {
        $new_record = new DotCoreAdminMessageRecord($this);
        $new_record->setAdminMessageDateTime(date(DotCoreConfig::$DATETIME_MYSQL_FORMAT));
        return $new_record;
    }

}

DotCoreDAL::AddRelationship(
        new DotCoreOneToManyRelationship(
            DotCoreAdminMessageDAL::ADMIN_LINK,
            DotCoreAdminDAL::GetInstance()->GetField(DotCoreAdminDAL::ADMIN_ID),
            DotCoreAdminMessageDAL::GetInstance()->GetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_ADMIN_ID)
        )
    );

?>
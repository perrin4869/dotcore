<?php

/**
 * DotCoreEventDAL - Implements the data access logic for the events of this website
 *
 * @author perrin
 */
class DotCoreEventDAL extends DotCoreDAL {

    public function  __construct()
    {
        parent::__construct(self::EVENTS_TABLE);

        $field_title = new DotCorePlainStringField(self::EVENTS_TITLE, $this, FALSE);
        $field_description = new DotCoreMultilineStringField(self::EVENTS_DESCRIPTION, $this, FALSE);
        $field_details = new DotCoreHTMLStringField(self::EVENTS_DETAILS, $this, TRUE);

        $this->AddField(new DotCoreAutoIncrementingKey(self::EVENTS_ID, $this));
        $this->AddField($field_title);
        $this->AddField($field_description);
        $this->AddField($field_details);
        $this->AddField(new DotCoreDateField(self::EVENTS_DATE, $this, FALSE));
        $this->AddField(new DotCoreIntField(self::EVENTS_LANGUAGE_ID, $this, FALSE));

        $this->SetPrimaryField(self::EVENTS_ID);
        $fulltext = new DotCoreDALFulltext(self::EVENTS_FULLTEXT, $this);
        $fulltext
            ->AddField($field_title)
            ->AddField($field_description)
            ->AddField($field_details);
        $this->AddFulltext($fulltext);
    }

    /**
     *
     * @return DotCoreEventDAL
     */
    public static function GetInstance()
    {
        return parent::GetDALInstance(__CLASS__);
    }

    const EVENTS_TABLE = "dotcore_events";

    const EVENTS_ID = 'event_id';
    const EVENTS_TITLE = 'event_title';
    const EVENTS_DESCRIPTION = 'event_description';
    const EVENTS_DETAILS = 'event_details';
    const EVENTS_DATE = 'event_date';
    const EVENTS_LANGUAGE_ID = 'event_language_id';

    const EVENTS_FULLTEXT = 'events_fulltext';

    /**
     * Returns a record of DotCoreEventRecord
     * @return DotCoreEventRecord
     */
    public function GetRecord()
    {
        return new DotCoreEventRecord($this);
    }

}
?>

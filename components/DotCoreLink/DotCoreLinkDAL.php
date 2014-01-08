<?php

/**
 * DotCoreLinkDAL - Implements the data access logic for the links of this website
 *
 * @author perrin
 */
class DotCoreLinkDAL extends DotCoreDAL {

    public function  __construct()
    {
        parent::__construct(self::LINKS_TABLE);

        $this->AddField(new DotCoreAutoIncrementingKey(self::LINK_ID, $this));
        $this->AddField(new DotCorePlainStringField(self::LINK_TITLE, $this, FALSE));
        $this->AddField(new DotCoreURLField(self::LINK_URL, $this, FALSE));
        $this->AddField(new DotCoreHTMLStringField(self::LINK_DESCRIPTION, $this, TRUE));
        $this->AddField(new DotCoreImageField(self::LINK_LOGO, $this, '/images/link_logos/', FALSE, TRUE));
        $this->AddField(new DotCoreIntField(self::LINK_LANGUAGE_ID, $this, FALSE));
        $this->AddField(new DotCoreOrderField(self::LINK_ORDER, $this, FALSE));

        $this->SetPrimaryField(self::LINK_ID);
    }

    /**
     *
     * @return DotCoreNewsDAL
     */
    public static function GetInstance()
    {
        return parent::GetDALInstance(__CLASS__);
    }

    const LINKS_TABLE = 'dotcore_links';

    const LINK_ID = 'link_id';
    const LINK_TITLE = 'link_title';
    const LINK_URL = 'link_url';
    const LINK_DESCRIPTION = 'link_description';
    const LINK_LOGO = 'link_logo';
    const LINK_LANGUAGE_ID = 'link_language_id';
    const LINK_ORDER = 'link_order';

    const LANGUAGE_LINK = 'language_link_link';

    /**
     * Returns a record of DotCoreLinkRecord
     * @return DotCoreLinkRecord
     */
    public function GetRecord()
    {
        return new DotCoreLinkRecord($this);
    }
    
}

DotCoreDAL::AddRelationship(
        new DotCoreOneToManyRelationship(
            DotCoreLinkDAL::LANGUAGE_LINK,
            DotCoreLanguageDAL::GetInstance()->GetField(DotCoreLanguageDAL::LANGUAGE_ID),
            DotCoreLinkDAL::GetInstance()->GetField(DotCoreLinkDAL::LINK_LANGUAGE_ID)
        )
    );

?>
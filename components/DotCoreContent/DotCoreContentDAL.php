<?php

/**
 * DotCoreContentDAL - Implements the data access logic for the contents of this website
 *
 * @author perrin
 */
class DotCoreContentDAL extends DotCoreDAL {

    public function  __construct()
    {
        parent::__construct(self::CONTENT_TABLE);

        $content_text = new DotCoreHTMLStringField(self::CONTENT_TEXT, $this, TRUE);

        $this->AddField(new DotCoreAutoIncrementingKey(self::CONTENT_TEMPLATE_CONTENT_ID, $this));
        $this->AddField(new DotCoreIntField(self::CONTENT_PAGE_ID, $this, FALSE));
	$this->AddField($content_text);

	$this->SetPrimaryField(self::CONTENT_TEMPLATE_CONTENT_ID);
        $this->SetPrimaryField(self::CONTENT_PAGE_ID);

        $fulltext = new DotCoreDALFulltext(self::CONTENT_FULLTEXT, $this);
        $fulltext->AddField($content_text);
        $this->AddFulltext($fulltext);
    }

    /**
     *
     * @return DotCoreContentDAL
     */
    public static function GetInstance()
    {
        return parent::GetDALInstance(__CLASS__);
    }

    const CONTENT_TABLE = 'dotcore_contents';

    const CONTENT_TEMPLATE_CONTENT_ID = 'content_id';
    const CONTENT_PAGE_ID = 'page_id';
    const CONTENT_TEXT = 'content_text';

    const CONTENT_FULLTEXT = 'content_fulltext';
    
    const PAGE_CONTENTS_LINK = 'page_content_link';

    /**
     * Returns a record of DotCoreContentRecord
     * @return DotCoreContentRecord
     */
    public function GetRecord()
    {
        return new DotCoreContentRecord($this);
    }

}

// Add relationships
    
DotCoreDAL::AddRelationship(
    new DotCoreOneToManyRelationship(
        DotCoreContentDAL::PAGE_CONTENTS_LINK,
        DotCorePageDAL::GetInstance()->GetField(DotCorePageDAL::PAGE_ID),
        DotCoreContentDAL::GetInstance()->GetField(DotCoreContentDAL::CONTENT_PAGE_ID)
        )
    );

?>
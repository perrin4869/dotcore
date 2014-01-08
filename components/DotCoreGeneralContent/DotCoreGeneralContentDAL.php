<?php

/**
 * DotCoreGeneralContentDAL - Implements the data access logic for the general contents of this website
 *
 * @author perrin
 */
class DotCoreGeneralContentDAL extends DotCoreDAL {

    public function  __construct()
    {
        parent::__construct(self::GENERAL_CONTENTS_TABLE);
        
        $this->AddField(new DotCoreAutoIncrementingKey(self::GENERAL_CONTENTS_ID, $this));
        $this->AddField(new DotCorePlainStringField(self::GENERAL_CONTENTS_NAME, $this, FALSE));
        $this->AddField(new DotCorePlainStringField(self::GENERAL_CONTENTS_DESCRIPTION, $this, FALSE));
        $this->AddField(new DotCoreIntField(self::GENERAL_CONTENTS_CONTENT_TYPE, $this, FALSE));
        $this->AddField(new DotCoreIntField(self::GENERAL_CONTENTS_ORDER, $this, FALSE));

        $this->SetPrimaryField(self::GENERAL_CONTENTS_ID);
    }

    /**
     *
     * @return DotCoreGeneralContentDAL
     */
    public static function GetInstance()
    {
        return parent::GetDALInstance(__CLASS__);
    }

    const GENERAL_CONTENTS_TABLE = 'dotcore_general_contents';

    const GENERAL_CONTENTS_ID = 'general_content_id';
    const GENERAL_CONTENTS_NAME = 'general_content_name';
    const GENERAL_CONTENTS_DESCRIPTION = 'general_content_description';
    const GENERAL_CONTENTS_CONTENT_TYPE = 'general_content_type';
    const GENERAL_CONTENTS_ORDER = 'general_content_order';

    const GENERAL_CONTENTS_MULTILANG_CONTENTS_LINK = 'general_contents_multilang_contents_link';

    const CONTENT_TYPE_RICH = 1;
    const CONTENT_TYPE_MULTILINE = 2;
    const CONTENT_TYPE_ONE_LINE = 3;

    /**
     * Returns a record of DotCoreGeneralContentRecord
     * @return DotCoreGeneralContentRecord
     */
    public function GetRecord()
    {
        return new DotCoreGeneralContentRecord($this);
    }

}

DotCoreDAL::AddRelationship(
    new DotCoreOneToManyRelationship(
        DotCoreGeneralContentDAL::GENERAL_CONTENTS_MULTILANG_CONTENTS_LINK,
        DotCoreGeneralContentDAL::GetInstance()->GetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_ID),
        DotCoreGeneralContentMultilangContentDAL::GetInstance()->GetField(DotCoreGeneralContentMultilangContentDAL::GENERAL_CONTENTS_MULTILANG_CONTENT_ID)
    )
);

?>
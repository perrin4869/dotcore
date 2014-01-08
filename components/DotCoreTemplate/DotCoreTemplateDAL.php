<?php

/**
 * DotCoreTemplateDAL - Implements the data access logic for the templates of this website
 *
 * @author perrin
 */
class DotCoreTemplateDAL extends DotCoreDAL {

    public function  __construct()
    {
        parent::__construct(self::TEMPLATE_TABLE);

        $folder_field = new DotCorePlainStringField(self::TEMPLATE_FOLDER, $this, FALSE);

        $this->AddField(new DotCoreAutoIncrementingKey(self::TEMPLATE_ID, $this));
        $this->AddField($folder_field);
        $this->AddField(new DotCorePlainStringField(self::TEMPLATE_NAME, $this, FALSE));

        $this->SetPrimaryField(self::TEMPLATE_ID);
        $this->AddUniqueKey(self::TEMPLATE_FOLDER_UNIQUE, array($folder_field));
        
    }

    /**
     *
     * @return DotCoreNewsDAL
     */
    public static function GetInstance()
    {
        return parent::GetDALInstance(__CLASS__);
    }

    const TEMPLATE_TABLE = "dotcore_templates";

    const TEMPLATE_ID = "template_id";
    const TEMPLATE_FOLDER = "template_folder";
    const TEMPLATE_NAME = "template_name";

    const TEMPLATE_FOLDER_UNIQUE = 'template_folder_unique';


    /**
     * Returns a record of DotCoreTemplateRecord
     * @return DotCoreTemplateRecord
     */
    public function GetRecord()
    {
        return new DotCoreTemplateRecord($this);
    }

}
?>

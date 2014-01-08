<?php

/**
 * DotCoreFeatureDAL - Implements the data access logic for the embeddable features of this website
 *
 * @author perrin
 */
class DotCoreFeatureDAL extends DotCoreDAL {

    public function  __construct()
    {
        parent::__construct(self::FEATURES_TABLE);

        $field_feature_name = new DotCorePlainStringField(self::FEATURE_NAME, $this, FALSE);
        $field_feature_class = new DotCorePlainStringField(self::FEATURE_CLASS, $this, FALSE);
        
        $this->AddField(new DotCoreAutoIncrementingKey(self::FEATURE_ID, $this));
        $this->AddField($field_feature_name);
        $this->AddField($field_feature_class);
        $this->AddField(new DotCorePlainStringField(self::FEATURE_SERVER_PATH, $this, TRUE));
        $this->AddField(new DotCoreURLField(self::FEATURE_DOMAIN_PATH, $this, TRUE));
        $this->AddField($field_feature_class);

        $this->AddUniqueKey(self::FEATURE_NAME_UNIQUE_KEY, $field_feature_name);
        $this->AddUniqueKey(self::FEATURE_CLASS_UNIQUE_KEY, $field_feature_class);

        $this->SetPrimaryField(self::FEATURE_ID);
    }

    /**
     *
     * @return DotCoreFeatureDAL
     */
    public static function GetInstance()
    {
        return parent::GetDALInstance(__CLASS__);
    }

    const FEATURES_TABLE = 'dotcore_features';

    const FEATURE_ID = 'feature_id';
    const FEATURE_NAME = 'feature_name';
    const FEATURE_CLASS = 'feature_class';
    const FEATURE_SERVER_PATH = 'feature_server_path';
    const FEATURE_DOMAIN_PATH = 'feature_domain_path';

    const FEATURE_NAME_UNIQUE_KEY = 'feature_name_unique_key';
    const FEATURE_CLASS_UNIQUE_KEY = 'feature_class_unique_key';
    
    /**
     * Returns a record of DotCoreFeatureRecord
     * @return DotCoreFeatureRecord
     */
    public function GetRecord()
    {
        return new DotCoreFeatureRecord($this);
    }

}

?>
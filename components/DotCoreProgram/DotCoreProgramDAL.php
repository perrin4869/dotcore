<?php

/**
 * DotCoreProgramDAL - Implements the data access logic for the programs in the admin panel
 *
 * @author perrin
 */
class DotCoreProgramDAL extends DotCoreDAL {

    public function  __construct()
    {
        parent::__construct(self::PROGRAMS_TABLE);

        $field_program_name = new DotCorePlainStringField(self::PROGRAM_NAME, $this, FALSE);
        $field_program_class = new DotCorePlainStringField(self::PROGRAM_CLASS, $this, FALSE);
        
        $this->AddField(new DotCoreAutoIncrementingKey(self::PROGRAM_ID, $this));
        $this->AddField($field_program_name);
        $this->AddField($field_program_class);
        $this->AddField(new DotCorePlainStringField(self::PROGRAM_SERVER_PATH, $this, TRUE));
        $this->AddField(new DotCoreURLField(self::PROGRAM_DOMAIN_PATH, $this, TRUE));
        $this->AddField($field_program_class);

        $this->AddUniqueKey(self::PROGRAM_NAME_UNIQUE_KEY, $field_program_name);
        $this->AddUniqueKey(self::PROGRAM_CLASS_UNIQUE_KEY, $field_program_class);

        $this->SetPrimaryField(self::PROGRAM_ID);
    }

    /**
     *
     * @return DotCoreProgramDAL
     */
    public static function GetInstance()
    {
        return parent::GetDALInstance(__CLASS__);
    }

    const PROGRAMS_TABLE = 'dotcore_programs';

    const PROGRAM_ID = 'program_id';
    const PROGRAM_NAME = 'program_name';
    const PROGRAM_CLASS = 'program_class';
    const PROGRAM_SERVER_PATH = 'program_server_path';
    const PROGRAM_DOMAIN_PATH = 'program_domain_path';

    const PROGRAM_NAME_UNIQUE_KEY = 'feature_program_unique_key';
    const PROGRAM_CLASS_UNIQUE_KEY = 'feature_program_unique_key';

    /**
     * Returns a record of DotCoreProgramRecord
     * @return DotCoreProgramRecord
     */
    public function GetRecord()
    {
        return new DotCoreProgramRecord($this);
    }

}
?>

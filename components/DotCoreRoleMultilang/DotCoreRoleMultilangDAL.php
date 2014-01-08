<?php

/**
 * DotCoreRoleMultilangDAL - MySQL DAL for the roles
 *
 * @author perrin
 */
class DotCoreRoleMultilangDAL extends DotCoreDAL {

    public function  __construct()
    {
        parent::__construct(self::ROLES_MULTILANG_TABLE);

        $this->AddField(new DotCoreIntField(self::ROLE_ID, $this, FALSE));
        $this->AddField(new DotCoreIntField(self::LANGUAGE_ID, $this, FALSE));
        $this->AddField(new DotCorePlainStringField(self::ROLE_NAME, $this, FALSE));

        $this->SetPrimaryField(self::ROLE_ID);
        $this->SetPrimaryField(self::LANGUAGE_ID);
    }

    /**
     *
     * @return DotCoreRoleMultilangDAL
     */
    public static function GetInstance()
    {
        return parent::GetDALInstance(__CLASS__);
    }

    const ROLES_MULTILANG_TABLE = 'dotcore_roles_multilang';

    const ROLE_ID = 'roles_multilang_role_id';
    const LANGUAGE_ID = 'roles_multilang_language_id';
    const ROLE_NAME = 'role_name';
    
    const ROLE_MULTILANG_LINK = 'roles_multilang_link';
    const LANGUAGE_LINK = 'language_roles_multilang_link';

    /**
     * Returns a record of DotCoreRoleMultilangRecord
     * @return DotCoreRoleMultilangRecord
     */
    public function GetRecord()
    {
        return new DotCoreRoleMultilangRecord($this);
    }

}

DotCoreDAL::AddRelationship(
    new DotCoreOneToManyRelationship(
            DotCoreRoleMultilangDAL::ROLE_MULTILANG_LINK,
            DotCoreRoleDAL::GetInstance()->GetField(DotCoreRoleDAL::ROLES_ID),
            DotCoreRoleMultilangDAL::GetInstance()->GetField(DotCoreRoleMultilangDAL::ROLE_ID)
        )
    );

DotCoreDAL::AddRelationship(
        new DotCoreOneToManyRelationship(
            DotCoreRoleMultilangDAL::LANGUAGE_LINK,
            DotCoreLanguageDAL::GetInstance()->GetField(DotCoreLanguageDAL::LANGUAGE_ID),
            DotCoreRoleMultilangDAL::GetInstance()->GetField(DotCoreRoleMultilangDAL::LANGUAGE_ID)
        )
    );

?>
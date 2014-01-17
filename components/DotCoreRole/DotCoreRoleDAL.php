<?php

/**
 * DotCoreRoleDAL - MySQL DAL for the roles
 *
 * @author perrin
 */
class DotCoreRoleDAL extends DotCoreDAL {

	public function  __construct()
	{
		parent::__construct(self::ROLES_TABLE);

		$desc_field = new DotCorePlainStringField(self::ROLES_DESC, $this, FALSE);

		$this->AddField(new DotCoreAutoIncrementingKey(self::ROLES_ID, $this));
		$this->AddField($desc_field);

		$this->AddUniqueKey(self::ROLES_UNIQUE_DESC, $desc_field);

		$this->SetPrimaryField(self::ROLES_ID);
	}

	/**
	 *
	 * @return DotCoreRoleDAL
	 */
	public static function GetInstance()
	{
		return parent::GetDALInstance(__CLASS__);
	}

	const ROLES_TABLE = 'dotcore_roles';

	const ROLES_ID = 'role_id';
	const ROLES_DESC = 'role_desc';

	const ROLES_UNIQUE_DESC = 'role_desc';

	const ADMIN_ROLES_LINK = 'admin_roles_link';

	/**
	 * Returns a record of DotCoreRoleRecord
	 * @return DotCoreRoleRecord
	 */
	public function GetRecord()
	{
		return new DotCoreRoleRecord($this);
	}

}

// Add relationships

DotCoreDAL::AddRelationship(
		new DotCoreManyToManyRelationship(
			DotCoreRoleDAL::ADMIN_ROLES_LINK,
			DotCoreAdminDAL::GetInstance()->GetField(DotCoreAdminDAL::ADMIN_ID),
			DotCoreAdminRolesDAL::GetInstance()->GetField(DotCoreAdminRolesDAL::ADMIN_ROLES_ADMIN_ID),
			DotCoreAdminRolesDAL::GetInstance()->GetField(DotCoreAdminRolesDAL::ADMIN_ROLES_ROLE_ID),
			DotCoreRoleDAL::GetInstance()->GetField(DotCoreRoleDAL::ROLES_ID)
		)
	);

?>
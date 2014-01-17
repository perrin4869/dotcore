<?php

/**
 * DotCoreAdminRolesDAL - MySQL DAL for the many to many admin roles
 *
 * @author perrin
 */
class DotCoreAdminRolesDAL extends DotCoreDAL {

	public function  __construct()
	{
		parent::__construct(self::ADMIN_ROLES_TABLE);

		$this->AddField(new DotCoreIntField(self::ADMIN_ROLES_ROLE_ID, $this, FALSE));
		$this->AddField(new DotCoreIntField(self::ADMIN_ROLES_ADMIN_ID, $this, FALSE));

		$this->SetPrimaryField(self::ADMIN_ROLES_ROLE_ID);
		$this->SetPrimaryField(self::ADMIN_ROLES_ADMIN_ID);
	}

	/**
	 *
	 * @return DotCoreAdminRolesDAL
	 */
	public static function GetInstance()
	{
		return parent::GetDALInstance(__CLASS__);
	}

	const ADMIN_ROLES_TABLE = "dotcore_admins_roles";

	const ADMIN_ROLES_ROLE_ID = "admins_roles_role_id";
	const ADMIN_ROLES_ADMIN_ID = "admins_roles_admin_id";

	/**
	 * Returns a record of DotCoreAdminRolesRecord
	 * @return DotCoreAdminRolesRecord
	 */
	public function GetRecord()
	{
		return new DotCoreAdminRolesRecord($this);
	}

}
?>

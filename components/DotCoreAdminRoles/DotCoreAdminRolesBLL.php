<?php

/**
 * DotCoreAdminRolesBLL - Contains the business logic of the admin's roles
 *
 * @author perrin
 */
class DotCoreAdminRolesBLL extends DotCoreBLL {

	/*
	 *
	 * Abstract Methods Implementation:
	 *
	 */

	/**
	 *
	 * @return DotCoreAdminRolesDAL
	 */
	public static function GetDAL() {
		return self::GetDALHelper('DotCoreAdminRolesDAL');
	}

	/*
	 *
	 * Fields accessors
	 *
	 */

	/**
	 * Gets the field that defines the admin's role
	 * @return DotCoreIntField
	 */
	public function getFieldRoleID()
	{
		return $this->GetDAL()->GetField(DotCoreAdminRolesDAL::ADMIN_ROLES_ROLE_ID);
	}

	/**
	 * Gets the field that defines the admin
	 * @return DotCoreIntField
	 */
	public function getFieldAdminID()
	{
		return $this->GetDAL()->GetField(DotCoreAdminRolesDAL::ADMIN_ROLES_ADMIN_ID);
	}

}
?>

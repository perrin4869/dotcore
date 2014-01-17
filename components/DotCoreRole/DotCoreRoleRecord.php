<?php

/**
 * DotCoreRoleRecord - Defines a single record of a role obtained by DotCoreRoleDAL
 *
 * @author perrin
 */
class DotCoreRoleRecord extends DotCoreDataRecord {

	/**
	 * Constructor for Role record
	 *
	 * @param DotCoreDAL $dal
	 */
	public function  __construct(DotCoreDAL $dal) {
		parent::__construct($dal);
	}

	public function  __toString() {
		return $this->getRoleDesc();
	}

	/*
	 *
	 * Accessors:
	 *
	 */

	/*
	 * Getters:
	 */

	public function getRoleID() {
		return $this->GetField(DotCoreRoleDAL::ROLES_ID);
	}

	public function getRoleDesc() {
		return $this->GetField(DotCoreRoleDAL::ROLES_DESC);
	}

	/*
	 * Setters:
	 */

	private function setRoleID($val) {
		$this->SetField(DotCoreRoleDAL::ROLES_ID, $val);
	}

	public function setRoleDesc($val) {
		$this->SetField(DotCoreRoleDAL::ROLES_DESC, $val);
	}

}
?>

<?php

/**
 * DotCoreRoleMultilangRecord - Defines a record for a single language of roles obtained by DotCoreRoleMultilangDAL
 *
 * @author perrin
 */
class DotCoreRoleMultilangRecord extends DotCoreDataRecord {

	/**
	 * Constructor for DotCoreRoleMultilang record
	 *
	 * @param DotCoreDAL $dal
	 */
	public function  __construct(DotCoreDAL $dal) {
		parent::__construct($dal);
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
		return $this->GetField(DotCoreRoleMultilangDAL::ROLE_ID);
	}

	public function getRoleName() {
		return $this->GetField(DotCoreRoleMultilangDAL::ROLE_NAME);
	}

	public function getLanguageID() {
		return $this->GetField(DotCoreRoleMultilangDAL::LANGUAGE_ID);
	}

	/*
	* Setters:
	*/

	private function setRoleID($val) {
		$this->SetField(DotCoreRoleMultilangDAL::ROLE_ID, $val);
	}

	private function setRoleName($val) {
		$this->SetField(DotCoreRoleMultilangDAL::ROLE_NAME, $val);
	}

	public function setLanguageID($val) {
		$this->SetField(DotCoreRoleMultilangDAL::LANGUAGE_ID, $val);
	}

}
?>

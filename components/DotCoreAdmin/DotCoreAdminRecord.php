<?php

/**
 * DotCoreAdminRecord - Defines a single record of an admin obtained by DotCoreAdminDAL
 *
 * @author perrin
 */
class DotCoreAdminRecord extends DotCoreDataRecord {

	/**
	 * Constructor for Admin records
	 *
	 * @param DotCoreDAL $dal
	 */
	public function  __construct(DotCoreDAL $dal) {
		parent::__construct($dal);
	}

	public function  __toString() {
		return $this->getAdminID();
	}

	/*
	 *
	 * Accessors:
	 *
	 */

	/*
	 * Getters:
	 */

	public function getAdminID() {
		return $this->GetField(DotCoreAdminDAL::ADMIN_ID);
	}

	public function isAdvanced() {
		return $this->GetField(DotCoreAdminDAL::ADMIN_ADVANCED);
	}

	/*
	* Setters:
	*/

	public function setAdminID($val) {
		$this->SetField(DotCoreAdminDAL::ADMIN_ID, $val);
	}

	public function setIsAdvanced($val) {
		$this->SetField(DotCoreAdminDAL::ADMIN_ADVANCED, $val);
	}

}
?>

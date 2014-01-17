<?php

/**
 * DotCoreUserRecord - Defines a single record of a user obtained by DotCoreUserDAL
 *
 * @author perrin
 */
class DotCoreUserRecord extends DotCoreDataRecord {

	/**
	 * Constructor for User record
	 *
	 * @param DotCoreDAL $dal
	 */
	public function  __construct(DotCoreDAL $dal) {
		parent::__construct($dal);
	}

	/*
	 *
	 * Variables
	 *
	 */

	/*
	 *
	 * Accessors:
	 *
	 */

	/*
	 * Getters:
	 */

	public function getUserID() {
		return $this->GetField(DotCoreUserDAL::USER_ID);
	}

	public function getUserName() {
		return $this->GetField(DotCoreUserDAL::USER_NAME);
	}

	public function getUserPassword() {
		return $this->GetField(DotCoreUserDAL::USER_PASSWORD);
	}

	public function getUserEmail() {
		return $this->GetField(DotCoreUserDAL::USER_EMAIL);
	}

	public function getUserFirstName() {
		return $this->GetField(DotCoreUserDAL::USER_FIRST_NAME);
	}

	public function getUserLastName() {
		return $this->GetField(DotCoreUserDAL::USER_LAST_NAME);
	}

	public function getUserPhone() {
		return $this->GetField(DotCoreUserDAL::USER_PHONE);
	}

	public function getUserLastLogin() {
		return $this->GetField(DotCoreUserDAL::USER_LAST_LOGIN);
	}

	public function getUserDateCreated() {
		return $this->GetField(DotCoreUserDAL::USER_DATE_CREATED);
	}

	/*
	 * Setters:
	 */

	private function setUserID($val) {
		$this->SetField(DotCoreUserDAL::USER_ID, $val);
	}

	public function setUserName($val) {
		$this->SetField(DotCoreUserDAL::USER_NAME, $val);
	}

	public function setUserPassword($val) {
		$this->SetField(DotCoreUserDAL::USER_PASSWORD, $val);
	}

	public function setUserEmail($val) {
		$this->SetField(DotCoreUserDAL::USER_EMAIL, $val);
	}

	public function setUserFirstName($val) {
		$this->SetField(DotCoreUserDAL::USER_FIRST_NAME, $val);
	}

	public function setUserLastName($val) {
		$this->SetField(DotCoreUserDAL::USER_LAST_NAME, $val);
	}

	public function setUserPhone($val) {
		$this->SetField(DotCoreUserDAL::USER_PHONE, $val);
	}

	public function setUserLastLogin($val) {
		$this->SetField(DotCoreUserDAL::USER_LAST_LOGIN, $val);
	}

	public function setUserDateCreated($val) {
		$this->SetField(DotCoreUserDAL::USER_DATE_CREATED, $val);
	}

}
?>

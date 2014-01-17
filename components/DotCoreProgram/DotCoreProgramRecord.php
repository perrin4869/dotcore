<?php

/**
 * DotCoreProgramRecord represents one record of programs from a DAL
 *
 * @author perrin
 */
class DotCoreProgramRecord extends DotCoreDataRecord {

	/**
	 * Constructor for Program records
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

	public function getProgramID() {
		return $this->GetField(DotCoreProgramDAL::PROGRAM_ID);
	}

	public function getProgramName() {
		return $this->GetField(DotCoreProgramDAL::PROGRAM_NAME);
	}

	public function getProgramClass() {
		return $this->GetField(DotCoreProgramDAL::PROGRAM_CLASS);
	}

	public function getProgramServerPath() {
		return $this->GetField(DotCoreProgramDAL::PROGRAM_SERVER_PATH);
	}

	public function getProgramDomainPath() {
		return $this->GetField(DotCoreProgramDAL::PROGRAM_DOMAIN_PATH);
	}

	/*
	 * Setters:
	 */


	private function setProgramID($val) {
		$this->SetField(DotCoreProgramDAL::PROGRAM_ID, $val);
	}

	public function setProgramName($name) {
		$this->SetField(DotCoreProgramDAL::PROGRAM_NAME, $name);
	}

	public function setProgramClass($class) {
		$this->SetField(DotCoreProgramDAL::PROGRAM_CLASS, $class);
	}

	public function setProgramServerPath($path) {
		$this->SetField(DotCoreProgramDAL::PROGRAM_SERVER_PATH, $path);
	}

	public function setProgramDomainPath($path) {
		$this->SetField(DotCoreProgramDAL::PROGRAM_DOMAIN_PATH, $path);
	}
	

}
?>

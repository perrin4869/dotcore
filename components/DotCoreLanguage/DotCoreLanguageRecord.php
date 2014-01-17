<?php

/**
 * DotCoreLanguageRecord - Defines a single record of a language obtained by DotCoreLanguageDAL
 *
 * @author perrin
 */
class DotCoreLanguageRecord extends DotCoreDataRecord {

	/**
	 * Constructor for Language record
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

	public function getLanguageID() {
		return $this->GetField(DotCoreLanguageDAL::LANGUAGE_ID);
	}

	public function getLanguageCode() {
		return $this->GetField(DotCoreLanguageDAL::LANGUAGE_CODE);
	}

	public function getLanguageDirection() {
		return $this->GetField(DotCoreLanguageDAL::LANGUAGE_DIRECTION);
	}

	public function getLanguageDefaultPageID() {
		return $this->GetField(DotCoreLanguageDAL::LANGUAGE_DEFAULT_PAGE_ID);
	}

	/*
	 * Setters:
	 */

	private function setLanguageID($val) {
		$this->SetField(DotCoreLanguageDAL::LANGUAGE_ID, $val);
	}

	public function setLanguageCode($val) {
		$this->SetField(DotCoreLanguageDAL::LANGUAGE_CODE, $val);
	}

	public function setLanguageDirection($val) {
		$this->SetField(DotCoreLanguageDAL::LANGUAGE_DIRECTION, $val);
	}

	public function setLanguageDefaultPageID($val) {
		$this->SetField(DotCoreLanguageDAL::LANGUAGE_DEFAULT_PAGE_ID, $val);
	}
	
}
?>

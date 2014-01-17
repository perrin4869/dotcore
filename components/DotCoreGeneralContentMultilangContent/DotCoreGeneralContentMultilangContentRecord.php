<?php

/**
 * DotCoreGeneralContentMultilangContentRecord holds the content of a general content of the website for a given language
 *
 * @author perrin
 */
class DotCoreGeneralContentMultilangContentRecord extends DotCoreDataRecord {

	/**
	 * Constructor for a multilingual record of General Content
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

	public function getGeneralContentID() {
		return $this->GetField(DotCoreGeneralContentMultilangContentDAL::GENERAL_CONTENTS_MULTILANG_CONTENT_ID);
	}

	public function getLanguageID() {
		return $this->GetField(DotCoreGeneralContentMultilangContentDAL::GENERAL_CONTENTS_MULTILANG_LANGUAGE_ID);
	}

	public function getText() {
		return $this->GetField(DotCoreGeneralContentMultilangContentDAL::GENERAL_CONTENTS_MULTILANG_TEXT);
	}

	/*
	 * Setters:
	 */

	public function setGeneralContentID($val) {
		$this->SetField(DotCoreGeneralContentMultilangContentDAL::GENERAL_CONTENTS_MULTILANG_CONTENT_ID, $val);
	}

	public function setLanguageID($languageID) {
		$this->SetField(DotCoreGeneralContentMultilangContentDAL::GENERAL_CONTENTS_MULTILANG_LANGUAGE_ID, $languageID);
	}

	public function setText($text) {
		$this->SetField(DotCoreGeneralContentMultilangContentDAL::GENERAL_CONTENTS_MULTILANG_TEXT, $text);
	}

}
?>

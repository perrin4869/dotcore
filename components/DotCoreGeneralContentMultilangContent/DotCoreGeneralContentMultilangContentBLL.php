<?php

/**
 * DotCoreGeneralContentMultilangContentBLL - Contains the business logic behind the multilingual components of general contents
 *
 * @author perrin
 */
class DotCoreGeneralContentMultilangContentBLL extends DotCoreBLL {

	/*
	 *
	 * Fields accessors:
	 *
	 */

	/**
	 * Gets the field that defines the general content
	 * @return DotCoreIntField
	 */
	public function getFieldGeneralContentsMultilangContentID()
	{
		return $this->GetDAL()->GetField(DotCoreGeneralContentMultilangContentDAL::GENERAL_CONTENTS_MULTILANG_CONTENT_ID);
	}

	/**
	 * Gets the field that defines the language
	 * @return DotCoreIntField
	 */
	public function getFieldGeneralContentsMultilangLanguageID()
	{
		return $this->GetDAL()->GetField(DotCoreGeneralContentMultilangContentDAL::GENERAL_CONTENTS_MULTILANG_LANGUAGE_ID);
	}

	/**
	 * Gets the field that defines the text of the general content for a given language
	 * @return DotCoreStringField
	 */
	public function getFieldGeneralContentMultilangText()
	{
		return $this->GetDAL()->GetField(DotCoreGeneralContentMultilangContentDAL::GENERAL_CONTENTS_MULTILANG_TEXT);
	}

	/*
	 *
	 * Abstract Methods Implementation:
	 *
	 */

	public static function GetDAL() {
		return self::GetDALHelper('DotCoreGeneralContentMultilangContentDAL');
	}

}
?>

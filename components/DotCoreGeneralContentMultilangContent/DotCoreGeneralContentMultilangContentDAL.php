<?php

/**
 * DotCoreGeneralContentDAL - Implements the data access logic for the multilingual properties of general contents
 *
 * @author perrin
 */
class DotCoreGeneralContentMultilangContentDAL extends DotCoreDAL {

	public function  __construct()
	{
		parent::__construct(self::GENERAL_CONTENTS_MULTILANG_CONTENT_TABLE);
		
		$this->AddField(new DotCoreIntField(self::GENERAL_CONTENTS_MULTILANG_CONTENT_ID, $this, FALSE));
		$this->AddField(new DotCoreIntField(self::GENERAL_CONTENTS_MULTILANG_LANGUAGE_ID, $this, FALSE));
		$this->AddField(new DotCoreHTMLStringField(self::GENERAL_CONTENTS_MULTILANG_TEXT, $this, TRUE));

		$this->SetPrimaryField(self::GENERAL_CONTENTS_MULTILANG_CONTENT_ID);
		$this->SetPrimaryField(self::GENERAL_CONTENTS_MULTILANG_LANGUAGE_ID);
	}

	/**
	 *
	 * @return DotCoreGeneralContentMultilangContentDAL
	 */
	public static function GetInstance()
	{
		return parent::GetDALInstance(__CLASS__);
	}

	const GENERAL_CONTENTS_MULTILANG_CONTENT_TABLE = 'dotcore_general_contents_multilang_contents';

	const GENERAL_CONTENTS_MULTILANG_CONTENT_ID = 'general_contents_multilang_content_id';
	const GENERAL_CONTENTS_MULTILANG_LANGUAGE_ID = 'general_contents_multilang_lang_id';
	const GENERAL_CONTENTS_MULTILANG_TEXT = 'general_content_text';

	const LANGUAGE_LINK = 'languages_general_content_multilang_link';

	/**
	 * Returns a record of DotCoreGeneralContentMultilangContentRecord
	 * @return DotCoreGeneralContentMultilangContentRecord
	 */
	public function GetRecord()
	{
		return new DotCoreGeneralContentMultilangContentRecord($this);
	}

}

DotCoreDAL::AddRelationship(
		new DotCoreOneToManyRelationship(
			DotCoreGeneralContentMultilangContentDAL::LANGUAGE_LINK,
			DotCoreLanguageDAL::GetInstance()->GetField(DotCoreLanguageDAL::LANGUAGE_ID),
			DotCoreGeneralContentMultilangContentDAL::GetInstance()->GetField(DotCoreGeneralContentMultilangContentDAL::GENERAL_CONTENTS_MULTILANG_LANGUAGE_ID)
		)
	);

?>
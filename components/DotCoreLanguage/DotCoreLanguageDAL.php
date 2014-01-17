<?php

/**
 * DotCoreLanguageDAL - MySQL DAL for the languages
 *
 * @author perrin
 */
class DotCoreLanguageDAL extends DotCoreDAL {

	public function  __construct()
	{
		parent::__construct(self::LANGUAGES_TABLE);

		$this->AddField(new DotCoreAutoIncrementingKey(self::LANGUAGE_ID, $this));
		$this->AddField(new DotCorePlainStringField(self::LANGUAGE_CODE, $this, FALSE));
		$this->AddField(new DotCoreBooleanField(self::LANGUAGE_DIRECTION, $this, FALSE));
		$this->AddField(new DotCoreIntField(self::LANGUAGE_DEFAULT_PAGE_ID, $this, TRUE));

		$this->AddUniqueKey(self::LANGUAGE_UNIQUE_CODE, $this->GetField(self::LANGUAGE_CODE));

		$this->SetPrimaryField(self::LANGUAGE_ID);
	}

	/**
	 *
	 * @return DotCoreLanguageDAL
	 */
	public static function GetInstance()
	{
		return parent::GetDALInstance(__CLASS__);
	}

	const LANGUAGES_TABLE = "dotcore_languages";

	const LANGUAGE_ID = "language_id";
	const LANGUAGE_CODE = "language_code";
	const LANGUAGE_DIRECTION = "language_direction";
	const LANGUAGE_DEFAULT_PAGE_ID = "default_page_id";

	const LANGUAGE_UNIQUE_CODE = 'language_code';

	const LANGUAGES_DIRECTION_LTR = 1;
	const LANGUAGES_DIRECTION_RTL = 0;

	const LANGUAGE_DEFAULT_PAGE_RELATIONSHIP = 'language_default_page_relationship';

	/**
	 * Returns a record of DotCoreLanguageRecord
	 * @return DotCoreLanguageRecord
	 */
	public function GetRecord()
	{
		return new DotCoreLanguageRecord($this);
	}

	/**
	 * Returns a record of DotCoreLanguageRecord with some initializations for insertion
	 * @return DotCoreLanguageRecord
	 */
	public function GetNewRecord()
	{
		$new_language = $this->GetRecord();
		$custom_direction = self::LANGUAGES_DIRECTION_LTR;
		$this->SetValueFromDAL(self::LANGUAGE_DIRECTION, $new_language, $custom_direction);
		return $new_language;
	}

}

DotCoreDAL::AddRelationship(
	new DotCoreOneToManyRelationship(
		DotCoreLanguageDAL::LANGUAGE_DEFAULT_PAGE_RELATIONSHIP,
		DotCoreLanguageDAL::GetInstance()->GetField(DotCoreLanguageDAL::LANGUAGE_DEFAULT_PAGE_ID),
		DotCorePageDAL::GetInstance()->GetField(DotCorePageDAL::PAGE_ID),
		DotCorePageDAL::GetInstance()
	)
);

?>
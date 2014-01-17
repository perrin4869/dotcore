<?php

/**
 * DotCoreNewsDAL - Implements the data access logic for the news of this website
 *
 * @author perrin
 */
class DotCoreNewsDAL extends DotCoreDAL {

	public function  __construct()
	{
		parent::__construct(self::NEWS_TABLE);

		$this->AddField(new DotCoreAutoIncrementingKey(self::NEWS_ID, $this));
		$this->AddField(new DotCorePlainStringField(self::NEWS_TITLE, $this, FALSE));
		$this->AddField(new DotCoreHTMLStringField(self::NEWS_CONTENT, $this, TRUE));
		$this->AddField(new DotCoreTimestampField(self::NEWS_DATE, $this, FALSE));
		$this->AddField(new DotCoreMultilineStringField(self::NEWS_SHORT_CONTENT, $this, FALSE));
		$this->AddField(new DotCoreIntField(self::NEWS_LANGUAGE_ID, $this, FALSE));

		$this->SetPrimaryField(self::NEWS_ID);
	}

	/**
	 *
	 * @return DotCoreNewsDAL
	 */
	public static function GetInstance()
	{
		return parent::GetDALInstance(__CLASS__);
	}

	const NEWS_TABLE = "dotcore_news";

	const NEWS_ID = "news_id";
	const NEWS_TITLE = "news_title";
	const NEWS_SHORT_CONTENT = "news_short_content";
	const NEWS_CONTENT = "news_content";
	const NEWS_DATE = "news_date";
	const NEWS_LANGUAGE_ID = "news_language_id";

	const LANGUAGE_LINK = 'language_news_link';

	/**
	 * Returns a record of DotCoreNewsRecord
	 * @return DotCoreNewsRecord
	 */
	public function GetRecord()
	{
		return new DotCoreNewsRecord($this);
	}

	/**
	 * Returns a record of DotCoreNewsRecord with some initializations for insertion
	 * @return DotCoreNewsRecord
	 */
	public function GetNewRecord()
	{
		$news_record = $this->GetRecord();
		$this->SetValueFromDAL(self::NEWS_DATE, $news_record, time());
		return $news_record;
	}	

}

DotCoreDAL::AddRelationship(
		new DotCoreOneToManyRelationship(
			DotCoreNewsDAL::LANGUAGE_LINK,
			DotCoreLanguageDAL::GetInstance()->GetField(DotCoreLanguageDAL::LANGUAGE_ID),
			DotCoreNewsDAL::GetInstance()->GetField(DotCoreNewsDAL::NEWS_LANGUAGE_ID)
		)
	);

?>
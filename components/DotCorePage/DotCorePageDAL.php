<?php

/**
 * DotCorePageDAL - Implements the data access logic for the pages of this website
 *
 * @author perrin
 */
class DotCorePageDAL extends DotCoreDAL {

	public function  __construct()
	{
		parent::__construct(self::PAGE_TABLE);

		$page_url_field = new DotCorePlainStringField(self::PAGE_URL, $this, FALSE);
		$page_language_id_field = new DotCoreIntField(self::PAGE_LANGUAGE, $this, FALSE);

		$this->AddField(new DotCoreAutoIncrementingKey(self::PAGE_ID, $this));
		$this->AddField(new DotCorePlainStringField(self::PAGE_NAME, $this, FALSE));
		$this->AddField($page_url_field);
		$this->AddField(new DotCoreHTMLStringField(self::PAGE_HEADER_CONTENT, $this, TRUE));
		$this->AddField(new DotCoreIntField(self::PAGE_ORDER, $this, FALSE)); // Can't make this unique, because then it would no be updatable
		$this->AddField(new DotCoreRecursiveIntField(self::PAGE_PARENT_ID, $this, self::PAGE_ID, TRUE));
		$this->AddField(new DotCoreBooleanField(self::PAGE_APPEARS_IN_NAV, $this, FALSE));
		$this->AddField($page_language_id_field);
		$this->AddField(new DotCorePlainStringField(self::PAGE_TITLE, $this, TRUE));

		// URLs are not allowed to be repeated for the same language
		$this->AddUniqueKey(self::PAGE_UNIQUE_URL, array($page_url_field, $page_language_id_field));

		$this->SetPrimaryField(self::PAGE_ID);
	}

	/**
	 *
	 * @return DotCorePageDAL
	 */
	public static function GetInstance()
	{
		return parent::GetDALInstance(__CLASS__);
	}

	const PAGE_TABLE = 'dotcore_pages';

	const PAGE_ID = 'page_id';
	const PAGE_NAME = 'page_name';
	const PAGE_URL = 'page_url';
	const PAGE_HEADER_CONTENT = 'page_header_content';
	const PAGE_ORDER = 'page_order';
	const PAGE_PARENT_ID = 'page_parent_id';
	const PAGE_APPEARS_IN_NAV = 'appears_in_nav';
	const PAGE_LANGUAGE = 'page_language_id';
	const PAGE_TITLE = 'title';

	const LANGUAGE_LINK = 'language_page_link';

	const PAGE_UNIQUE_URL = 'page_url';

	/**
	 * Returns a record of DotCorePageRecord
	 * @return DotCorePageRecord
	 */
	public function GetRecord()
	{
		return new DotCorePageRecord($this);
	}

	/**
	 * Returns a record of DotCorePageRecord with some initializations for insertion
	 * @return DotCorePageRecord
	 */
	public function GetNewRecord()
	{
		$new_page = parent::GetNewRecord();
		$this->SetValueFromDAL(self::PAGE_APPEARS_IN_NAV, $new_page, TRUE);
		$this->SetValueFromDAL(self::PAGE_HEADER_CONTENT, $new_page, NULL);
		$this->SetValueFromDAL(self::PAGE_TITLE, $new_page, NULL);
		$this->SetValueFromDAL(self::PAGE_PARENT_ID, $new_page, NULL);
		return $new_page;
	}

}

DotCoreDAL::AddRelationship(
	new DotCoreOneToManyRelationship(
		DotCorePageDAL::LANGUAGE_LINK,
		DotCoreLanguageDAL::GetInstance()->GetField(DotCoreLanguageDAL::LANGUAGE_ID),
		DotCorePageDAL::GetInstance()->GetField(DotCorePageDAL::PAGE_LANGUAGE)
	)
);


?>
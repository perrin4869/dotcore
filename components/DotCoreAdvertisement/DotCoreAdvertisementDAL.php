<?php

/**
 * DotCoreAdvertisementDAL - Implements the data access logic for the advertisements of this website
 *
 * @author perrin
 */
class DotCoreAdvertisementDAL extends DotCoreDAL {

	public function  __construct()
	{
		parent::__construct(self::ADVERTISEMENT_TABLE);

		$field_media_name = new DotCoreImageField(self::ADVERTISEMENT_MEDIA_NAME, $this, self::ADVERTISEMENT_MEDIA_FOLDER, FALSE, FALSE);

		$this->AddField(new DotCoreAutoIncrementingKey(self::ADVERTISEMENT_ID, $this));
		$this->AddField(new DotCorePlainStringField(self::ADVERTISEMENT_TEXT, $this, TRUE));
		$this->AddField($field_media_name);
		$this->AddField(new DotCoreBooleanField(self::ADVERTISEMENT_IS_ACTIVE, $this, FALSE));
		$this->AddField(new DotCoreURLField(self::ADVERTISEMENT_URL, $this, TRUE));

		$this->AddUniqueKey(self::ADVERTISEMENT_UNIQUE_MEDIA_NAME, $field_media_name);
		$field_media_name->AddAllowedType("swf");
		$this->SetPrimaryField(self::ADVERTISEMENT_ID);
	}

	/**
	 *
	 * @return DotCoreAdvertisementDAL
	 */
	public static function GetInstance()
	{
		return parent::GetDALInstance(__CLASS__);
	}

	const ADVERTISEMENT_TABLE = "dotcore_advertisement";

	const ADVERTISEMENT_ID = "advertisement_id";
	const ADVERTISEMENT_TEXT = "advertisement_text";
	const ADVERTISEMENT_MEDIA_NAME = "advertisement_media_name";
	const ADVERTISEMENT_IS_ACTIVE = "advertisement_is_active";
	const ADVERTISEMENT_URL = "advertisement_url";

	const ADVERTISEMENT_UNIQUE_MEDIA_NAME = 'advertisement_media_name';

	const ADVERTISEMENT_MEDIA_FOLDER = '/images/advertisements/';

	/**
	 * Returns a record of DotCoreAdvertisementRecord
	 * @return DotCoreAdvertisementRecord
	 */
	public function GetRecord()
	{
		return new DotCoreAdvertisementRecord($this);
	}

}
?>

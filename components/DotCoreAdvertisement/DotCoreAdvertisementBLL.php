<?php

/**
 * DotCoreAdvertisementBLL - Contains the business logic behind the ads
 *
 * @author perrin
 */
class DotCoreAdvertisementBLL extends DotCoreBLL {

	/*
	 *
	 * Abstract Methods Implementation:
	 *
	 */

	/**
	 *
	 * @return DotCoreAdvertisementDAL
	 */
	public static function GetDAL() {
		return self::GetDALHelper('DotCoreAdvertisementDAL');
	}

	/*
	 *
	 * Fields accessors:
	 *
	 */

	/**
	 * Gets the auto incrementing ID of this DAL
	 * @return DotCoreAutoIncrementingKey
	 */
	public function getFieldAdvertisementID()
	{
		return $this->GetDAL()->GetField(DotCoreAdvertisementDAL::ADVERTISEMENT_ID);
	}

	/**
	 * Gets the field that defines the text of the ad
	 * @return DotCoreStringField
	 */
	public function getFieldText()
	{
		return $this->GetDAL()->GetField(DotCoreAdvertisementDAL::ADVERTISEMENT_TEXT);
	}

	/**
	 * Gets the field that defines the media name of the ad
	 * @return DotCoreImageField
	 */
	public function getFieldMediaName()
	{
		return $this->GetDAL()->GetField(DotCoreAdvertisementDAL::ADVERTISEMENT_MEDIA_NAME);
	}

	/**
	 * Gets the field that defines if the ad is active
	 * @return DotCoreBooleanField
	 */
	public function getFieldIsActive()
	{
		return $this->GetDAL()->GetField(DotCoreAdvertisementDAL::ADVERTISEMENT_IS_ACTIVE);
	}

	/**
	 * Gets the field that defines the url to which this ad points
	 * @return DotCoreStringField
	 */
	public function getFieldUrl()
	{
		return $this->GetDAL()->GetField(DotCoreAdvertisementDAL::ADVERTISEMENT_URL);
	}

	/*
	 *
	 * Busines Logic Methods:
	 *
	 */

	/**
	 *
	 * @param int $id
	 * @return DotCoreAdvertisementBLL
	 */
	 public function ByAdvertisementID($id) {
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldAdvertisementID(), $id));

		return $this->Restraints($restraints);
	}

	/**
	 *
	 * @return DotCoreAdvertisementBLL
	 */
	 public function ByActiveAdvertisements() {
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldIsActive(), TRUE));

		return $this->Restraints($restraints);
	}
}
?>

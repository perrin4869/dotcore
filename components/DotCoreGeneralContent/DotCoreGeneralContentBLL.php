<?php

/**
 * DotCoreGeneralContentBLL - Contains the business logic behind the general contents of the website
 *
 * @author perrin
 */
class DotCoreGeneralContentBLL extends DotCoreBLL {

	/*
	 *
	 * Fields accessors:
	 *
	 */

	/**
	 * Gets the field that defines the unique ID of the general contents
	 * @return DotCoreAutoIncrementingKey
	 */
	public function getFieldGeneralContentsID()
	{
		return $this->GetDAL()->GetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_ID);
	}

	/**
	 * Gets the field that defines the identifiying name of general contents
	 * @return DotCoreStringField
	 */
	public function getFieldName()
	{
		return $this->GetDAL()->GetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_NAME);
	}

	/**
	 * Gets the field that defines the description of the content
	 * @return DotCoreStringField
	 */
	public function getFieldContentDescription()
	{
		return $this->GetDAL()->GetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_DESCRIPTION);
	}

	/**
	 * Gets the field that defines the type of the content
	 * @return DotCoreIntField
	 */
	public function getFieldContentType()
	{
		return $this->GetDAL()->GetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_CONTENT_TYPE);
	}

	/**
	 * Gets the field that defines the order of the general contents
	 * @return DotCoreIntField
	 */
	public function getFieldOrder()
	{
		return $this->GetDAL()->GetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_ORDER);
	}

	/*
	 *
	 * Abstract Methods Implementation:
	 *
	 */

	/**
	 *
	 * @return DotCoreGeneralContentDAL
	 */
	public static function GetDAL() {
		return self::GetDALHelper('DotCoreGeneralContentDAL');
	}

	/*
	 *
	 * Links:
	 *
	 */

	/**
	 * Adds a link to a multilingual general content DAL
	 *
	 * @return DotCoreOneToManyRelationship
	 */
	public function LinkGeneralContentsMultilangContent() {
		$relationship = DotCoreDAL::GetRelationship(DotCoreGeneralContentDAL::GENERAL_CONTENTS_MULTILANG_CONTENTS_LINK);
		$this->AddLink($relationship);
		return $relationship;
	}

	/*
	 *
	 * Link Accessors:
	 *
	 */

	public static function GetMultilangContents(DotCoreGeneralContentRecord $general_content) {
		return $general_content->GetLinkValue(DotCoreGeneralContentDAL::GENERAL_CONTENTS_MULTILANG_CONTENTS_LINK);
	}

	public static function AddMultilangContent(
		DotCoreGeneralContentRecord $general_content,
		DotCoreGeneralContentMultilangContentRecord $content) {
		$general_content->SetLinkValue(DotCoreGeneralContentDAL::GENERAL_CONTENTS_MULTILANG_CONTENTS_LINK, $content);
	}

	/*
	 *
	 * Order Methods
	 *
	 */

	/**
	 *
	 * @return DotCoreGeneralContentBLL
	 */
	public function Ordered($direction = DotCoreFieldSelectionOrder::DIRECTION_ASC)
	{
		$order = new DotCoreDALSelectionOrder();
		$order
			->AddOrderUnit(
				new DotCoreFieldSelectionOrder(
					$this->getFieldOrder(),
					$direction
				)
			);
			
		return $this->Order($order);
	}

}
?>

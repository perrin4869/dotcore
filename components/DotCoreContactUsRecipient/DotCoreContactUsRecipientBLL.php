<?php

/**
 * DotCoreContactUsRecipientBLL - Contains the business logic behind the contact us recipients
 *
 * @author perrin
 */
class DotCoreContactUsRecipientBLL extends DotCoreBLL {

	/*
	 *
	 * Fields accessors:
	 *
	 */

	/**
	 * Gets the auto incrementing ID of this DAL
	 * @return DotCoreAutoIncrementingKey
	 */
	public function getFieldContactUsRecipientID()
	{
		return $this->GetDAL()->GetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_ID);
	}

	/**
	 * Gets the field that defines the contact us recipient name
	 * @return DotCoreStringField
	 */
	public function getFieldName()
	{
		return $this->GetDAL()->GetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_NAME);
	}

	/**
	 * Gets the field that defines the contact us recipient email
	 * @return DotCoreEmailField
	 */
	public function getFieldEmail()
	{
		return $this->GetDAL()->GetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_EMAIL);
	}

	/**
	 *
	 * @return DotCoreIntField
	 */
	public function getFieldContactUsRecipientLanguageID()
	{
		return $this->GetDAL()->GetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_LANGUAGE_ID);
	}

	/*
	 *
	 * Abstract Methods Implementation:
	 *
	 */

	/**
	 *
	 * @return DotCoreContactUsRecipientDAL
	 */
	public static function GetDAL() {
		return self::GetDALHelper('DotCoreContactUsRecipientDAL');
	}

	/*
	 *
	 * Busines Logic Methods:
	 *
	 */

	/**
	 *
	 * @param int $id
	 * @return DotCoreContactUsRecipientBLL
	 */
	public function ByContactUsRecipientID($id) {
		
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldContactUsRecipientID(), $id));

		$this->Restraints($restraints);
		return $this;
		
	}

	/**
	 *
	 * @param int $id
	 * @return DotCoreContactUsRecipientBLL
	 */
	public function ByLanguageID($id) {

		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldContactUsRecipientLanguageID(), $id));

		$this->Restraints($restraints);
		return $this;

	}

	/**
	 *
	 * @return DotCoreContactUsRecipientBLL
	 */
	public function OrderedByLanguage() {
		$order = new DotCoreDALSelectionOrder();
		$order->AddOrderUnit(
			new DotCoreFieldSelectionOrder($this->getFieldContactUsRecipientLanguageID(), DotCoreFieldSelectionOrder::DIRECTION_ASC)
		);
		$this->Order($order);
		return $this;
	}

}
?>

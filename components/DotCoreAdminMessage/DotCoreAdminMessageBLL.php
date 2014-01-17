<?php

/**
 * DotCoreAdminMessageBLL - Contains the business logic behind the admin messages
 *
 * @author perrin
 */
class DotCoreAdminMessageBLL extends DotCoreBLL {


	/*
	 *
	 * Fields accessors:
	 *
	 */

	/**
	 * Gets the auto incrementing ID of this DAL
	 * @return DotCoreAutoIncrementingKey
	 */
	public function getFieldAdminMessageID()
	{
		return $this->GetDAL()->GetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_ID);
	}

	/**
	 * 
	 * @return DotCoreStringField
	 */
	public function getFieldText()
	{
		return $this->GetDAL()->GetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_TEXT);
	}

	/**
	 * 
	 * @return DotCoreIntField
	 */
	public function getFieldAdminID()
	{
		return $this->GetDAL()->GetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_ADMIN_ID);
	}

	/**
	 *
	 * @return DotCoreDateTimeField
	 */
	public function getFieldDateTime()
	{
		return $this->GetDAL()->GetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_DATETIME);
	}

	/*
	 *
	 * Abstract Methods Implementation:
	 *
	 */

	/**
	 *
	 * @return DotCoreAdminMessageDAL
	 */
	public static function GetDAL() {
		return self::GetDALHelper('DotCoreAdminMessageDAL');
	}
	
	/*
	 * 
	 * Link Methods
	 * 
	 */

	public static function GetAdminLink() {
		return DotCoreDAL::GetRelationship(DotCoreAdminMessageDAL::ADMIN_LINK);
	}

	/**
	 * Links the admin DAL
	 *
	 * @return DotCoreOneToManyRelationship
	 */
	public function LinkAdmins() {
		$link = self::GetAdminLink();
		$this->GetDAL()->AddLink($link);
		return $link;
	}

	/**
	 * Gets the admin who posted the message
	 * @return array
	 */
	public static function GetAdmin(DotCoreAdminMessageRecord $role) {
		return $role->GetLinkValue(DotCoreAdminMessageDAL::ADMIN_LINK);
	}

	/*
	 *
	 * Busines Logic Methods:
	 *
	 */

	/**
	 *
	 * @param int $id
	 * @return DotCoreAdminMessageBLL
	 */
	public function ByAdminMessageID($id) {

		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldAdminMessageID(), $id));

		$this->Restraints($restraints);
		return $this;

	}

	/**
	 *
	 * @return DotCoreAdminMessageBLL
	 */
	public function OrderedByID($direction = DotCoreFieldSelectionOrder::DIRECTION_ASC) {
		$order = new DotCoreDALSelectionOrder();
		$order->AddOrderUnit(
			new DotCoreFieldSelectionOrder($this->getFieldAdminMessageID(), $direction)
		);
		$this->Order($order);
		return $this;
	}

}
?>

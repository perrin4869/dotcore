<?php

/**
 * DotCoreContactMemberBLL - Contains the business logic behind the contact members
 *
 * @author perrin
 */
class DotCoreContactMemberBLL extends DotCoreBLL {

	/*
	 *
	 * Abstract Methods Implementation:
	 *
	 */

	/**
	 *
	 * @return DotCoreContactMemberDAL
	 */
	public static function GetDAL() {
		return self::GetDALHelper('DotCoreContactMemberDAL');
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
	public function getFieldContactID()
	{
		return $this->GetDAL()->GetField(DotCoreContactMemberDAL::CONTACT_MEMBER_ID);
	}

	/**
	 * Gets the field that defines the email of the contact
	 * @return DotCoreEmailField
	 */
	public function getFieldEmail()
	{
		return $this->GetDAL()->GetField(DotCoreContactMemberDAL::CONTACT_MEMBER_EMAIL);
	}

	/**
	 * Gets the field that defines the date the contact was added
	 * @return DotCoreTimestampField
	 */
	public function getFieldDateAdded()
	{
		return $this->GetDAL()->GetField(DotCoreContactMemberDAL::CONTACT_MEMBER_DATE_ADDED);
	}

	/**
	 * Gets the field that defines the language of the contact member
	 * @return DotCoreIntField
	 */
	public function getFieldLanguageID()
	{
		return $this->GetDAL()->GetField(DotCoreContactMemberDAL::CONTACT_MEMBER_LANGUAGE_ID);
	}

	/*
	 *
	 * Busines Logic Methods:
	 *
	 */

	/**
	 *
	 * @param int $id
	 * @return DotCoreContactMemberBLL
	 */
	public function ByContactID($id)
	{
		$restraint = new DotCoreDALRestraint();
		$restraint->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldContactID(), $id));

		return $this->Restraints($restraint);
	}

	/**
	 *
	 * @param string $email
	 * @return DotCoreContactMemberBLL
	 */
	public function ByEmail($email)
	{
		$restraint = new DotCoreDALRestraint();
		$restraint->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldEmail(), $email));

		return $this->Restraints($restraint);
	}

	/**
	 *
	 * @param DotCoreContactMemberRecord $contact
	 * @return string
	 */
	public static function GetRemovalCode(DotCoreContactMemberRecord $contact)
	{
		return md5($contact->getContactMemberEmail() . DotCoreConfig::$SECRET_STRING);
	}

	public function ChangeHoldersInEmail(&$email_content, DotCoreContactMemberRecord $member)
	{
		$email_content = str_replace('[contact_name]', $member->getContactName(), $email_content);
		$email_content = str_replace('[email]', $member->getEmail(), $email_content);
	}

	/**
	 * If the given code is correct, it deletes the contact given
	 * @param DotCoreContactMemberRecord $contact
	 * @param string $code
	 * @return boolean TRUE if successfully deleted, FALSE otherwise
	 */
	public function CheckAndDelete(DotCoreContactMemberRecord $contact, $code)
	{
		if($code != $this->GetRemovalCode($contact))
		{
			return FALSE;
		}

		$this->Delete($contact);
		return TRUE;
	}

}
?>

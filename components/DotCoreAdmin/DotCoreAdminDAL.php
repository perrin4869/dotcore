<?php

/**
 * DotCoreAdminDAL - MySQL DAL for the admins
 *
 * @author perrin
 */
class DotCoreAdminDAL extends DotCoreDAL {

	public function  __construct()
	{
		parent::__construct(self::ADMIN_TABLE);

		$this->AddField(new DotCoreIntField(self::ADMIN_ID, $this, FALSE));
		$this->AddField(new DotCoreBooleanField(self::ADMIN_ADVANCED, $this, FALSE));

		$this->SetPrimaryField(self::ADMIN_ID);
	}

	const USER_ADMIN_LINK = 'user_admin_link';

	/**
	 *
	 * @return DotCoreAdminDAL
	 */
	public static function GetInstance()
	{
		return parent::GetDALInstance(__CLASS__);
	}

	const ADMIN_TABLE = 'dotcore_admins';

	const ADMIN_ID = 'admin_id';
	const ADMIN_ADVANCED = 'is_advanced';

	/**
	 * Returns a record of DotCoreAdminRecord
	 * @return DotCoreAdminRecord
	 */
	public function GetRecord()
	{
		return new DotCoreAdminRecord($this);
	}

	/**
	 * Returns a record of DotCoreLanguageRecord with some initializations for insertion
	 * @return DotCoreLanguageRecord
	 */
	public function GetNewRecord()
	{
		$new_admin = $this->GetRecord();
		$val = FALSE;
		$this->SetValueFromDAL(self::ADMIN_ADVANCED, $new_admin, $val); // No need for validation
		return $new_admin;
	}

}

// Add relationships
	
DotCoreDAL::AddRelationship(
		new DotCoreOneToOneRelationship(
			DotCoreAdminDAL::USER_ADMIN_LINK,
			DotCoreUserDAL::GetInstance()->GetField(DotCoreUserDAL::USER_ID),
			DotCoreAdminDAL::GetInstance()->GetField(DotCoreAdminDAL::ADMIN_ID)
		)
	);

?>
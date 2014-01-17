<?php

/**
 * DotCoreFeaturesCommandsDAL - Implements the data access logic for the commands used to embed features on this website
 *
 * @author perrin
 */
class DotCoreFeaturesCommandsDAL extends DotCoreDAL {

	public function  __construct()
	{
		parent::__construct(self::FEATURES_COMMANDS_TABLE);

		$field_feature_command = new DotCorePlainStringField(self::FEATURES_COMMANDS_COMMAND, $this, FALSE);
		
		$this->AddField(new DotCoreIntField(self::FEATURES_COMMANDS_FEATURE_ID, $this, FALSE));
		$this->AddField($field_feature_command);

		$this->AddUniqueKey(self::FEATURE_COMMAND_UNIQUE_KEY, $field_feature_command);

		$this->SetPrimaryField(self::FEATURES_COMMANDS_FEATURE_ID);
	}

	/**
	 *
	 * @return DotCoreFeaturesCommandsDAL
	 */
	public static function GetInstance()
	{
		return parent::GetDALInstance(__CLASS__);
	}

	const FEATURES_COMMANDS_TABLE = 'dotcore_features_commands';

	const FEATURES_COMMANDS_FEATURE_ID = 'features_commands_feature_id';
	const FEATURES_COMMANDS_COMMAND = 'feature_command';

	const FEATURE_COMMAND_UNIQUE_KEY = 'feature_command_unique_key';

	const FEATURE_FEATURE_COMMANDS_LINK = 'feature_feature_commands_link';

	/**
	 * Returns a record of DotCoreFeaturesCommandsRecord
	 * @return DotCoreFeaturesCommandsRecord
	 */
	public function GetRecord()
	{
		return new DotCoreFeaturesCommandsRecord($this);
	}

}

DotCoreDAL::AddRelationship(
	new DotCoreOneToManyRelationship(
			DotCoreFeaturesCommandsDAL::FEATURE_FEATURE_COMMANDS_LINK,
			DotCoreFeatureDAL::GetInstance()->GetField(DotCoreFeatureDAL::FEATURE_ID),
			DotCoreFeaturesCommandsDAL::GetInstance()->GetField(DotCoreFeaturesCommandsDAL::FEATURES_COMMANDS_FEATURE_ID)
		)
	);

?>
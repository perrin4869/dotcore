<?php

/**
 * DotCoreFeaturesCommandsBLL - Contains the business logic behind the features commands of the website
 *
 * @author perrin
 */
class DotCoreFeaturesCommandsBLL extends DotCoreBLL {

	/*
	 *
	 * Properties:
	 *
	 */

	/*
	 *
	 * Fields accessors:
	 *
	 */

	/**
	 * Gets the field that defines the unique ID of the feature invoked by the command
	 * @return DotCoreIntField
	 */
	public function getFieldFeatureID()
	{
		return $this->GetDAL()->GetField(DotCoreFeaturesCommandsDAL::FEATURES_COMMANDS_FEATURE_ID);
	}

	/**
	 * Gets the field that defines the command used to invoke the feature
	 * @return DotCoreStringField
	 */
	public function getFieldFeatureCommand()
	{
		return $this->GetDAL()->GetField(DotCoreFeaturesCommandsDAL::FEATURES_COMMANDS_COMMAND);
	}

	/*
	 *
	 * Abstract Methods Implementation:
	 *
	 */

	/**
	 *
	 * @return DotCoreFeaturesCommandsDAL
	 */
	public static function GetDAL() {
		return self::GetDALHelper('DotCoreFeaturesCommandsDAL');
	}

	/*
	 *
	 * Restraint methods:
	 *
	 */

   /**
	 * @param string $command
	 *
	 * @return DotCoreFeatureBLL
	 */
	public function ByFeatureCommand($command)
	{
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint(
				$this->getFieldFeatureCommand(),
				$command
			)
		);

		return $this->Restraints($restraints);
	}

}
?>

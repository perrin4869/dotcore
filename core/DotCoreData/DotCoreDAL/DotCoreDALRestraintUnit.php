<?php

/**
 * DotCoreDALRestraintUnit - Defines a single restraint (i.e., without any AND or OR).
 * They're fed to DotCoreDALRestraint to create complex Restraints.
 * They're used for MySQL restraints
 *
 * @author perrin
 */
abstract class DotCoreDALRestraintUnit extends DotCoreObject {

	/**
	 * Gets the SQL resulting from the restraint, compatible with MySQL.
	 */
	public abstract function GetStatement();
}

// TODO: Generalize, create DotCoreMySqlRestraintUnit, DotCoreWSRestraintUnit, DotCoreOracleRestraintUnit, and so on

?>

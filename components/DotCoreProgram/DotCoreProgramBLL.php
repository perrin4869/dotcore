<?php

/**
 * DotCoreProgramBLL - Contains the business logic behind the programs of the website
 *
 * @author perrin
 */
class DotCoreProgramBLL extends DotCoreBLL {

	/*
	 *
	 * Fields accessors:
	 *
	 */

	/**
	 * Gets the field that defines the unique ID of the program
	 * @return DotCoreAutoIncrementingKey
	 */
	public function getFieldProgramID()
	{
		return $this->GetDAL()->GetField(DotCoreProgramDAL::PROGRAM_ID);
	}

	/**
	 * Gets the field that defines the identifiying name of programs
	 * @return DotCorePlainStringField
	 */
	public function getFieldProgramName()
	{
		return $this->GetDAL()->GetField(DotCoreProgramDAL::PROGRAM_NAME);
	}

	/**
	 * Gets the field that defines the name of the class which implements this program
	 * @return DotCorePlainStringField
	 */
	public function getFieldProgramClass()
	{
		return $this->GetDAL()->GetField(DotCoreProgramDAL::PROGRAM_CLASS);
	}

	/**
	 * Gets the field that defines the url to the program's folder (in case it exists in a different domain)
	 * @return DotCoreURLField
	 */
	public function getFieldProgramDomainPath()
	{
		return $this->GetDAL()->GetField(DotCoreProgramDAL::PROGRAM_DOMAIN_PATH);
	}

	/**
	 * Gets the field that defines the path inside the server (absolute, or relative to the features repository path) where the program class resides
	 * @return DotCorePlainStringField
	 */
	public function getFieldProgramServerPath()
	{
		return $this->GetDAL()->GetField(DotCoreProgramDAL::PROGRAM_SERVER_PATH);
	}

	/*
	 *
	 * Abstract Methods Implementation:
	 *
	 */

	/**
	 *
	 * @return DotCoreProgramDAL
	 */
	public static function GetDAL() {
		return self::GetDALHelper('DotCoreProgramDAL');
	}

	/*
	 *
	 * Restraint Methods
	 *
	 */
	 
	/**
	 * 
	 * @param int $id
	 * @return DotCoreProgramBLL
	 */
	public function ByProgramID($id)
	{
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint(
				$this->getFieldProgramID(),
				$id
			)
		);

		return $this->Restraints($restraints);
	}

	/**
	 *
	 * @param string $class
	 * @return DotCoreProgramBLL
	 */
	public function ByProgramClass($class)
	{
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint(
				$this->getFieldProgramClass(),
				$class
			)
		);

		return $this->Restraints($restraints);
	}

	/*
	 *
	 * Helper methods:
	 *
	 */

	/**
	 * Gets the url to the program's installation folder
	 * @param DotCoreProgramRecord $program
	 * @return string
	 */
	public static function GetProgramFolderPath(DotCoreProgramRecord $program) {
		$domain_path = $program->getProgramDomainPath();
		if($program->GetDAL()->GetField(DotCoreProgramDAL::PROGRAM_DOMAIN_PATH)->IsEmpty($domain_path))
		{
			return self::GetLocalRootUrl($program);
		}
		else
		{
			return self::ParseIncludePath($domain_path);
		}
	}

	public static function ParseIncludePath($domain_path) {
		$domain_path = str_replace('{programs_repository_url}', DotCoreConfig::$PROGRAMS_REPOSITORY_URL, $domain_path);
		if($domain_path[strlen($domain_path)-1] != '/') {
			$domain_path .= '/';
		}
		return $domain_path;
	}

	/**
	 * Gets the path to the installation folder of the program on the server
	 * @param DotCoreProgramRecord $program
	 * @return string
	 */
	public static function GetProgramServerFolderPath(DotCoreProgramRecord $program) {
		$server_path = $program->getProgramServerPath();
		if($program->GetDAL()->GetField(DotCoreProgramDAL::PROGRAM_SERVER_PATH)->IsEmpty($server_path))
		{
			return self::GetLocalRootFolder($program);
		}
		else
		{
			// If the program resides in the repository
			$tmp_path = DotCoreConfig::$PROGRAMS_REPOSITORY_PATH.$server_path;
			if(is_dir($tmp_path))
			{
				return $tmp_path;
			}
			else{
				// It resides in the path defined by the server path
				return $server_path;
			}
		}
	}

	/**
	 * Gets the url to the local folder of the program
	 * @param DotCoreProgramRecord $program
	 * @return string
	 */
	public static function GetLocalRootUrl(DotCoreProgramRecord $program) {
		return DotCoreConfig::$PROGRAMS_LOCAL_URL.$program->getProgramClass();
	}

	/**
	 * Gets the path to the local folder of the program on the server
	 * @param DotCoreProgramRecord $program
	 * @return string
	 */
	public static function GetLocalRootFolder(DotCoreProgramRecord $program) {
		return DotCoreConfig::$PROGRAMS_LOCAL_PATH.$program->getProgramClass();
	}

	/**
	 * Includes a file in the installation folder of the program of the class $program_class
	 * @param string $program_class
	 * @param string $filename
	 */
	public function IncludeProgramFile($program_class, $filename) {
		$program = $this
			->Fields(array($this->getFieldProgramServerPath()))
			->ByProgramClass($program_class)
			->SelectFirstOrNull();

		if($program) {
			$program_installation = $this->GetProgramServerFolderPath($program);
			include($program_installation.'/'.$filename);
		}
		else {
			throw new DotCoreException('Requested program '.$program_class.' was not found.');
		}
	}

}
?>

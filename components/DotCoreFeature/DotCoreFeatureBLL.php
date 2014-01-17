<?php

/**
 * DotCoreFeatureBLL - Contains the business logic behind the features of the website
 *
 * @author perrin
 */
class DotCoreFeatureBLL extends DotCoreBLL {

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
	 * Gets the field that defines the unique ID of the features
	 * @return DotCoreAutoIncrementingKey
	 */
	public function getFieldFeatureID()
	{
		return $this->GetDAL()->GetField(DotCoreFeatureDAL::FEATURE_ID);
	}

	/**
	 * Gets the field that defines the identifiying name of features
	 * @return DotCorePlainStringField
	 */
	public function getFieldFeatureName()
	{
		return $this->GetDAL()->GetField(DotCoreFeatureDAL::FEATURE_NAME);
	}

	/**
	 * Gets the field that defines the name of the class which implements this feature
	 * @return DotCorePlainStringField
	 */
	public function getFieldFeatureClass()
	{
		return $this->GetDAL()->GetField(DotCoreFeatureDAL::FEATURE_CLASS);
	}

	/**
	 * Gets the field that defines the url to the feature's folder (in case it exists in a different domain)
	 * @return DotCoreURLField
	 */
	public function getFieldFeatureDomainPath()
	{
		return $this->GetDAL()->GetField(DotCoreFeatureDAL::FEATURE_DOMAIN_PATH);
	}

	/**
	 * Gets the field that defines the path inside the server (absolute, or relative to the features repository path) where the feature class resides
	 * @return DotCorePlainStringField
	 */
	public function getFieldFeatureServerPath()
	{
		return $this->GetDAL()->GetField(DotCoreFeatureDAL::FEATURE_SERVER_PATH);
	}

	/*
	 *
	 * Abstract Methods Implementation:
	 *
	 */

	/**
	 *
	 * @return DotCoreFeatureDAL
	 */
	public static function GetDAL() {
		return self::GetDALHelper('DotCoreFeatureDAL');
	}

	/*
	 *
	 * Restraint Methods
	 *
	 */
	 
	/**
	 * 
	 * @param int $id
	 * @return DotCoreFeatureBLL
	 */
	public function ByFeatureID($id)
	{
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint(
				$this->getFieldFeatureID(),
				$id
			)
		);

		return $this->Restraints($restraints);
	}

	/*
	 *
	 * Helper methods:
	 *
	 */
	
	protected static function ParseIncludePath($domain_path) {
		$domain_path = str_replace('{features_repository_url}', DotCoreConfig::$FEATURES_REPOSITORY_URL, $domain_path);
		if($domain_path[strlen($domain_path)-1] != '/') {
			$domain_path .= '/';
		}
		return $domain_path;
	}

	/**
	 * Gets the url to the feature's folder (be it in the same domain as the website, or not)
	 * @param DotCoreFeatureRecord $feature
	 * @return string
	 */
	public static function GetFeatureUrl(DotCoreFeatureRecord $feature) {
		$domain_path = $feature->getFeatureDomainPath();
		if($feature->GetDAL()->GetField(DotCoreFeatureDAL::FEATURE_DOMAIN_PATH)->IsEmpty($domain_path))
		{
			return self::GetFeatureLocalRootUrl($feature);
		}
		else
		{
			return self::ParseIncludePath($domain_path);
		}
	}

	/**
	 * Gets the path to the folder in which the features reside, be it with the rest of the features, or in the features repository
	 * @param DotCoreFeatureRecord $feature
	 * @return string
	 */
	public static function GetFeatureServerFolderPath(DotCoreFeatureRecord $feature) {
		$server_path = $feature->getFeatureServerPath();
		if($feature->GetDAL()->GetField(DotCoreFeatureDAL::FEATURE_SERVER_PATH)->IsEmpty($server_path))
		{
			return self::GetFeatureLocalRootFolder($feature);
		}
		else
		{
			// If the feature resides in the repository
			$tmp_path = DotCoreConfig::$FEATURES_REPOSITORY_PATH.$server_path.'/';
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
	 * Gets the path to the local folder of the feature on the server
	 * @param DotCoreFeatureRecord $feature
	 * @return string
	 */
	public static function GetFeatureLocalRootFolder(DotCoreFeatureRecord $feature) {
		return DotCoreConfig::$FEATURES_LOCAL_PATH.$feature->getFeatureClass().'/';
	}

	/**
	 * Gets the url to the local folder of the feature
	 * @param DotCoreFeatureRecord $feature
	 * @return string
	 */
	public static function GetFeatureLocalRootUrl(DotCoreFeatureRecord $feature) {
		return DotCoreConfig::$FEATURES_LOCAL_URL.$feature->getFeatureClass().'/';
	}

	public static function GetFeatureMessages(DotCoreFeatureRecord $feature, $language_code = NULL, $default_lang_code = NULL) {
		$path_to_lang = self::GetFeatureLocalRootFolder($feature).'lang.php';
		// echo $path_to_lang;
		if(file_exists($path_to_lang))
		{
			return DotCoreMessages::GetMessages($path_to_lang, $language_code, $default_lang_code);
		}
		else
		{
			return array();
		}
	}

}

?>
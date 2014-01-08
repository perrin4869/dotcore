<?php
	
/**
 * A class used for creating the correct DotCoreFeature
 * @author perrin
 *
 */
class FactoryFeature extends FactoryBase
{
    private function __construct()
    {
        $this->feature_command_bll = new DotCoreFeaturesCommandsBLL();
        $features_path = DotCoreFeaturesCommandsDAL::FEATURE_FEATURE_COMMANDS_LINK.'.';
        $this->feature_command_bll
            ->Fields(
                array(
                    $features_path.DotCoreFeatureDAL::FEATURE_NAME,
                    $features_path.DotCoreFeatureDAL::FEATURE_CLASS,
                    $features_path.DotCoreFeatureDAL::FEATURE_SERVER_PATH,
                    $features_path.DotCoreFeatureDAL::FEATURE_DOMAIN_PATH
                )
            );
    }
	
    private static $instance = NULL;
    private static $loaded_features = array();
    private static $features_commands_classes_dictionary = array();
    private $feature_command;
    private $feature_parameters;
    private $feature_content;

    /**
     *
     * @var DotCoreFeaturesCommandsBLL
     */
    private $feature_command_bll = NULL;

    /**
     * Gets the singleton instance of this Factory
     * @return FactoryFeature
     */
    public static function GetInstance()
    {
        if(self::$instance == NULL)
        {
            $class = __CLASS__;
            self::$instance = new $class;
        }

        return self::$instance;
    }

    public function GetFeatureCommand()
    {
        return $this->feature_command;
    }

    /**
     * Sets the command of the feature to load
     * @param string $name
     * @return FactoryFeature
     */
    public function SetFeatureCommand($name)
    {
        $this->feature_command = $name;
        return $this;
    }

    public function GetFeatureParameters()
    {
        return $this->feature_parameters;
    }

    /**
     * Sets the parameters to send to the created feature
     * @param array $params
     * @return FactoryFeature
     */
    public function SetFeatureParameters($params)
    {
        $this->feature_parameters = $params;
        return $this;
    }

    public function GetFeatureContent()
    {
        return $this->feature_content;
    }

    public function SetFeatureContent($content)
    {
        $this->feature_content = $content;
        return $this;
    }

    /**
     * Tries to load feature described by feature_record
     * @param DotCoreFeatureRecord $feature_record
     * @return True on successful load, false otherwise
     */
    public static function LoadFeature(DotCoreFeatureRecord $feature_record)
    {
        $feature_class = $feature_record->getFeatureClass();
        if(self::FeatureLoaded($feature_class))
        {
            return TRUE;
        }
        
        $include_path = DotCoreFeatureBLL::GetFeatureServerFolderPath($feature_record).'/'.$feature_class.'.php';
        if(file_exists($include_path))
        {
            require($include_path);
            self::$loaded_features[$feature_class] = 1; // Set as loaded
            return TRUE;
        }
        return FALSE;
    }

    public static function FeatureLoaded($feature_class)
    {
        return key_exists($feature_class, self::$loaded_features);
    }

    public function Create()
    {
        $feature_command = $this->feature_command;
        $feature_record = self::$features_commands_classes_dictionary[$feature_command];

        if($feature_record == -1)
        {
            // Already searched for it, and the command didn't map to anything
            return NULL;
        }

        // We either already searched for the record and found it, or we still haven't even searched for it at this point
        // (though we know we didn't fail for sure)
        // If the result is empty, search for the record, otherwise just return the record previously found
        if(empty($feature_record))
        {
            // Try to load the class
            $feature_command_record = $this->feature_command_bll
                ->ByFeatureCommand($feature_command)
                ->SelectFirstOrNull();

            // If no record was found or it just failed to load the feature inside record, fail
            if(
                empty($feature_command_record) ||
                !self::LoadFeature(
                    (($feature_record = $feature_command_record->GetLinkValue(DotCoreFeaturesCommandsDAL::FEATURE_FEATURE_COMMANDS_LINK)))
                )
            )
            {
                self::$features_commands_classes_dictionary[$feature_command] = -1; // Command has no class
                return NULL;
            }
            else
            {
                self::$features_commands_classes_dictionary[$feature_command] = $feature_record;
                $feature_class = $feature_record->getFeatureClass();
            }
        }
        else
        {
            $feature_class = $feature_record->getFeatureClass();
        }

        return new $feature_class($feature_record, $this->feature_parameters, $this->feature_content);
    }
}

?>
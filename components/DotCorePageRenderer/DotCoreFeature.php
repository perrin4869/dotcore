<?php

// +------------------------------------------------------------------------+
// | DotCoreExtension.php                                                   |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2003-2008. All rights reserved.          |
// | Version       0.01                                                     |
// | Last modified 17/03/2009                                               |
// | Email         juliangrinblat@gmail.com                                 |
// | Web           http://www.dotcore.co.il/                                |
// +------------------------------------------------------------------------+

/**
 * Class DotCoreFeature
 *
 * @version   0.01
 * @author    Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
abstract class DotCoreFeature extends DotCoreObject
{
    public function __construct(DotCoreFeatureRecord $record, $parameters, $content = NULL)
    {
        $this->feature_record = $record;
        $this->content = $content;
        $this->parameters = $parameters;
    }

    private static $futile_commands = array();
    private static $count_matches = 0;

    /**
     * Returns the name of the feature
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     *
     *
     * Feature Parsing:
     *
     *
     */

    /**
     * Function used to parse a string finding features embedded inside some content
     *
     * @param string &$content The content to parse, by reference so the feature content will be set
     * @return int the count of matches found
     */
    public static function ParseFeatures(&$content)
    {
        $feature_specifiers = array('feature', 'תוספת');
        $feature_specifier_regex = join('|', $feature_specifiers);

        // Used to match the following pattern -
        // 0- Whole pattern
        // 1- Command with "namespace"
        // 2- Command
        // 3- Parameters
        // 4- Content (optional, may not be there)

        // TODO:
        // We need to prevent closing tags from being matched where they don't belong, for example:
        /*
        {feature:title_message key="news-title"}{feature:general_content name="news"}</div><div id="contents-column-with-news">
            <p>{feature:title_message}ברוכים הבאים{/feature:title_message}
        */
        // Also, it's worth noting that the regex doesn't match nested features inside the contents of another features
        // And that is as expected, because we want to match those only if needed
        $regex = '/\{((?:(?:'.$feature_specifier_regex.')\:)([\p{L}0-9-_]+))\s*([^}]*)\s*}(?:([\s\S]*?)\{\/\1\})?/u';
        self::$count_matches = 0;
        // $limit = -1; Means that there's no limit to the replacements
        $content = preg_replace_callback(
            $regex,
            array(__CLASS__, 'ParseFeatures_preg_replace_callback'),
            $content,
            -1);
        return self::$count_matches;
    }

    private static function ParseFeatures_preg_replace_callback($match)
    {
        
        // Orders results so that $matches[0] is an array of first set of matches, $matches[1] is an array of second set of matches, and so on.
        if(key_exists(2, $match) && !key_exists($match[2], self::$futile_commands))
        {
            $command = $match[2];
            $feature_factory = FactoryFeature::GetInstance();
            
            $params_markup = trim($match[3]);
            $params = array();
            if(!empty($params_markup)) // If $params is empty, there's no point in running the regex
            {
                $regex = '/([\p{L}0-9_-]+)\=("|\')([^\2]+?)\2/u';
                preg_match_all($regex, $params_markup, $params_matches, PREG_SET_ORDER);
                // Don't count the first 2 elements (the whole match and the feature name)
                $param_matches_count = count($params_matches);
                for($j = 0; $j < $param_matches_count; $j++)
                {
                    $param = $params_matches[$j];
                    $params[$param[1]] = $param[3];
                }
            }

            if(key_exists(4, $match))
            {
                $content = $match[4];
            }
            else
            {
                $content = NULL;
            }

            $feature_factory->SetFeatureCommand($command);
            $feature_factory->SetFeatureParameters($params);
            $feature_factory->SetFeatureContent($content);
            $curr_feature = $feature_factory->Create();

            if($curr_feature)
            {
                self::$count_matches++;
                $curr_feature->setCreatingMarkup($match[0]);
                return $curr_feature->GetFeatureContent();
            }
            else
            {
                self::$futile_commands[$command] = TRUE;
            }
        }

        return $match[0]; // If no feature was created, return an empty string so it won't be parsed no more
    }


    /*
     *
     * DotCoreFeature Properties
     *
     */

    /**
     * Stores the record of this feature
     * @var DotCoreFeatureRecord
     */
    private $feature_record;

    /**
     * Stores the markup used to create this feature
     * @var string
     */
    private $creating_markup = NULL;

    /**
     * Stores the content given to the feature the moment it was created
     * @var string
     */
    private $content = NULL;

    /**
     * Holds the parameters that were used to create this feature
     * @var array
     */
    private $parameters = NULL;

    /**
     *
     * DotCoreFeature Getters
     *
     */

    /**
     * Gets the record of this feature
     * @return DotCoreFeatureRecord
     */
    public function getFeatureRecord() {
        return $this->feature_record;
    }

    /**
     * If set, it'll return the markup that was used to create this feature,
     * otherwise it'll return NULL
     * @return string
     */
    public function getCreatingMarkup() {
        return $this->creating_markup;
    }

    /**
     * Gets the content given to the feature when created, if any
     * @return string
     */
    public function getCreatingMarkupContent() {
        return $this->content;
    }

    /**
     *
     * DotCoreFeature Setters
     *
     */
     
    /**
     * Sets the markup used to create this feature
     * @param string $markup
     */
    private function setCreatingMarkup($markup) {
        $this->creating_markup = $markup;
    }

    /**
     *
     * Features Methods:
     *
     */

    /**
     * Error list for storing errors across the program
     * @var array
     */
    private $error_list = array();

    /**
     * Caches the messages for a given language
     * @var array of messages
     */
    private $messages = NULL;

    /**
     * Holds the configuration for this feature
     * @var DotCoreConfiguration
     */
    private $configuration = NULL;

    /**
     * Stores the error $error for later use
     * @param mixed $error
     * @return void
     */
    protected function AddError($error)
    {
        array_push($this->error_list, $error);
    }

    protected function GetErrors()
    {
        return $this->error_list;
    }

    protected function HasErrors()
    {
        return count($this->error_list) > 0;
    }

    /*
     *
     * Abstract Methods:
     *
     */

    /**
     * Returns the HTML that is displayed in the content of the page for this given feature, this is the place
     * to display Forms that this feature implements
     * @return unknown_type
     */
    abstract public function GetFeatureContent();

    /*
     *
     * Helper functions:
     *
     */

    protected function GetErrorsMarkup()
    {
        $count_errors = count($this->error_list);
        $string = '';

        if($count_errors > 0)
        {
            $string = '
            <div class="feedback">';
                for($i = 0; $i < $count_errors; $i++)
                {
                    if($i > 0)
                    {
                        $string .= "<br />";
                    }
                    $string .= $this->error_list[$i];
                }
            $string .= '
            </div>';
        }

        return $string;
    }

    protected function GetFeatureUrl()
    {
        return DotCoreFeatureBLL::GetFeatureUrl($this->feature_record);
    }

    /**
     * 
     * @param string|array $keys The key (or aliases) for the parameter whose value we're looking for
     * @return mixed The value of the parameter, or NULL if none is found
     */
    protected function GetParameter($keys, $default = NULL)
    {
        if(!is_array($keys))
        {
            if(key_exists($keys, $this->parameters))
            {
                return $this->parameters[$keys];
            }
        }
        else
        {
            $count_keys = count($keys);
            for($i = 0; $i < $count_keys; $i++)
            {
                 $key = $keys[$i];
                if(key_exists($key, $this->parameters))
                {
                    return $this->parameters[$key];
                }
            }
        }
        // No value was found
        return $default;
    }

    /**
     * Gets the messages used throughout the feature, and caches them
     * If it was translated to the language of the page, those messages will be used,
     * Otherwise the default language of the website will be tried, and if it fails, an empty messages array will be returned
     *
     * @return array of messages
     */
    protected function GetMessages()
    {
        if($this->messages == NULL)
        {
            $current_renderer = DotCorePageRenderer::GetCurrent();
            $lang_code = $current_renderer->GetLanguage()->getLanguageCode();
            $default_lang_code =  $current_renderer->GetDefaultLanguage()->getLanguageCode();
            $this->messages = DotCoreFeatureBLL::GetFeatureMessages($this->feature_record, $lang_code, $default_lang_code);
        }

        // TODO: Merge with global messages
        return $this->messages;
    }

    /**
     * Gets the configuration of this feature
     *
     * @return DotCoreConfiguration
     */
    protected function GetConfiguration()
    {
        if($this->configuration == NULL)
        {
            $local_config = DotCoreFeatureBLL::GetFeatureLocalRootFolder($this->feature_record).'configuration.php';
            if(is_file($local_config)) {
                $this->configuration = new DotCoreConfiguration($local_config);
            }
            else{
                $global_config = DotCoreFeatureBLL::GetFeatureServerFolderPath($this->feature_record).'configuration.php';
                if(is_file($global_config)) {
                    $this->configuration = new DotCoreConfiguration($global_config);
                }
                else {
                    throw new Exception('Could not find config file for this feature.');
                }
            }
            
        }
        return $this->configuration;
    }

}

?>
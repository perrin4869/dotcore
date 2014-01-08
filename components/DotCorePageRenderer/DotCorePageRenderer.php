<?php

/**
 * DotCorePageRenderer is used to render DotCorePageRecords as an HTML page
 *
 * @author perrin
 */
class DotCorePageRenderer extends DotCoreObject {

    /**
     * Constructs a new page renderer based on a given page, or on no page, in which case it'll render an empty page
     * @param DotCorePageRecord $page
     */
    public function  __construct() {

    }

    /**
     * Stores the renderer being currently run
     * @var DotCorePageRenderer
     */
    private static $current_renderer = NULL;

    /**
     * Holds the default language of the website
     * @var DotCoreLanguageRecord
     */
    private static $default_language = NULL;

    /**
     * Holds the language that is used for rendering pages by this DotCorePageRenderer
     * @var DotCoreLanguageRecord
     */
    private $language = NULL;

    /**
     * Holds a reference to the page record being rendered
     * @var DotCorePageRecord
     */
    private $page = NULL;

    /**
     * Stores the error message (if any) found while initilizing this Renderer
     * @var string
     */
    private $error_message = NULL;

    /**
     * Stores the general contents
     * @var array
     */
    private $general_contents = NULL;

    /**
     * Used to accumulate header content that is registered to this page
     * @var string
     */
    private $page_header_content_accumulator = '';

    /**
     * Stores the title of this renderer
     * @var string
     */
    private $title;

    /**
     * Stores the template being rendered
     * @var string
     */
    private $template;

    /**
     * Holds the loaded components
     * @var array
     */
    private $components = array();

    /**
     * Stores values kept through postbacks
     * @var array
     */
    private $stored_values = array();

    /**
     *
     * @var DotCoreConfiguration
     */
    private static $configuration = NULL;

    /**
     * Gets the renderer currently on work
     * @return DotCorePageRenderer
     */
    public static function GetCurrent() {
        return self::$current_renderer;
    }

    /**
     * Gets the record of the page being rendered
     * @return DotCorePageRecord
     */
    public function GetPageRecord() {
        return $this->page;
    }

    /**
     * Gets the language by used for rendering by this Renderer
     * @return DotCoreLanguageRecord
     */
    public function GetLanguage() {
        return $this->language;
    }

    /**
     * Saves the header content for printing with the whole page
     * @param string $content
     */
    public function RegisterHeaderContent($content) {
        $this->page_header_content_accumulator .= $content;
    }

    /**
     * Gets the general content by the name $name
     * @param string $name
     * @return string
     */
    public function GetGeneralContent($name) {
        return $this->general_contents[$name];
    }

    /**
     * Gets the title of the page
     * @return string
     */
    public function GetTitle() {
        return $this->title;
    }

    /**
     * Gets the style of the page renderer
     * @return string
     */
    public function GetStyle() {
        return $this->template;
    }

    /**
     *
     * @param string $key
     * @param string $val
     */
    public function StoreValue($key, $val) {
        $this->stored_values[$key] = $val;
    }

    /**
     *
     * @param string $key
     * @return boolean
     */
    public function IsValueStored($key) {
        return key_exists($key, $this->stored_values);
    }

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function RetrieveValue($key) {
        return $this->stored_values[$key];
    }

    /**
     *
     * @return array
     */
    public function GetStoredValues() {
        return $this->stored_values;
    }

    /**
     *
     * @param string $key
     */
    public function RemoveStoredValue($key) {
        unset($this->stored_values[$key]);
    }

    /**
     * Gets the error message (if any) gotten when parsing the webpage
     * @return string
     */
    public function GetErrorMessage() {
        return $this->error_message;
    }

    public function LoadComponent($component_name) {
        $components = &$this->GetAvailableComponents();
        if(key_exists($component_name, $components))
        {
            $this->RegisterHeaderContent($components[$component_name]."\n");
            unset($components[$component_name]);
        }
    }

    protected function &GetAvailableComponents() {
        if($this->components == NULL)
        {
            include($_SERVER['DOCUMENT_ROOT'].DotCoreConfig::$CLIENT_COMPONENTS_FILE);
            $this->components = $components;
        }
        return $this->components;
    }

    /**
     * Initilizes the properties of this renderer, i.e., loads the page and the language requested
     */
    public function Initilize() {
        clean_request();

        DotCoreMySql::SetConnectionData('localhost', DotCoreConfig::$DATABASE_USERNAME, DotCoreConfig::$DATABASE_PASSWORD, DotCoreConfig::$DATABASE_NAME);
    }

    protected function ParseURL() {
        $uri = urldecode($_SERVER['REQUEST_URI']);

        // Strip the query string from the URI, so it doesn't get on the way of parsing
        $question_mark = strpos($uri, '?' . $_SERVER['QUERY_STRING']);
        if($question_mark > -1) {
            $uri = substr($uri, 0, $question_mark);
        }

        $parameters = explode('/', $uri);
        $parameters_count = count($parameters);
        // Get rid of empty elements
        for($i = 0; $i < $parameters_count; $i++) {
            if($parameters[$i] == '') {
                unset($parameters[$i]);
            }
        }
        $parameters = array_values($parameters);

        $i = 0; // Keep track of the current parameter being parsed

        $lang_bll = new DotCoreLanguageBLL();
        $lang_bll->Fields(
            array(
                $lang_bll->getFieldLanguageCode(),
                $lang_bll->getFieldDirection(),
                $lang_bll->getFieldDefaultPage()));

        // First find out what the language is
        // If the parameter for languages was not passed, try to fill it with the default directly
        // (and improve performance, as opposed to trying to fill uselessly with an empty parameter)
        if(!isset($parameters[$i])) {
            $lang = $this->GetDefaultLanguage();
        }
        else {
            $lang = $lang_bll
                ->ByLanguageCode($parameters[$i])
                ->SelectFirstOrNull();

            if($lang == NULL) {
                $lang = $this->GetDefaultLanguage();
            }
            else {
            // Language was parsed with the first parameter, so let's look for the page in the second parameter
                ++$i;
            }
        }

        // If the language is still empty - no use to continue parsing
        if($lang == NULL) {
            $this->error_message = 'The desired language could not be determined.';
        }
        else {
            $this->language = $lang;
        }

        $page_bll = new DotCorePageBLL();
        $languages_path = DotCorePageDAL::LANGUAGE_LINK . '.';
        $page_bll
            ->Fields(array(
                $page_bll->getFieldName(),
                $page_bll->getFieldUrl(),
                $page_bll->getFieldTitle(),
                $page_bll->getFieldPageLanguageID(),
                $page_bll->getFieldPageParentID(),
                $page_bll->getFieldHeaderContent(),
                $languages_path.DotCoreLanguageDAL::LANGUAGE_CODE,
                $languages_path.DotCoreLanguageDAL::LANGUAGE_DIRECTION,
                $languages_path.DotCoreLanguageDAL::LANGUAGE_DEFAULT_PAGE_ID
            )
        );

        $page = NULL;
        if(isset($parameters[$i])) {
            $extension_place = strrpos($parameters[$i], '.');
            if($extension_place > -1) {
            // Start with 1 to get rid of the leading slash
                $page_url = substr($parameters[$i], 0, $extension_place);
            }
            else {
                $page_url = substr($parameters[$i], 0);
            }

            $page = $page_bll
                ->ByPageUrlAndLanguage($page_url, $lang->getLanguageID())
                ->SelectFirstOrNull();

            if($page == NULL) {
                $this->error_message = $this->GetPageNotFoundMessage($lang);
            }
        }
        else {
            $default_page = $lang->getLanguageDefaultPageID();
            if($default_page == NULL) {
                $this->error_message = sprintf('There\'s no default page set for the %s language', $this->language->getLanguageCode());
            }

            $page = $page_bll->ByPageID($default_page)->SelectFirstOrNull();
            
            // Shouldn't be empty, because there's a constrain on the Database for the default lenguage.
            // But if for some reason it still fails...
            if($page == NULL) {
                $this->error_message = $this->GetPageNotFoundMessage($lang);
            }
        }

        $this->page = $page;
    }

    /**
     * Renders the page completely with the header, footer, content and features
     * @return void
     */
    public function Render() {
        self::$current_renderer = $this;
        $this->ParseURL();
        $curr_lang_id = $this->language->getLanguageID();
        DotCoreMultiLanguageLink::SetContextLanguageID($curr_lang_id);

        $configure = self::GetConfiguration();
        $this->template = key_exists('template', $_REQUEST) ? $_REQUEST['template'] : $configure->GetValue('default_template');
        $template_path = $this->GetTemplateFolderPath() . '/template.tpl';
        
        if(file_exists($template_path))
        {
            $contents = file_get_contents($template_path);


            // Load general contents into a dictionary
            $general_contents_bll = new DotCoreGeneralContentBLL();
            $general_contents_multilang_bll = new DotCoreGeneralContentMultilangContentBLL();
            $general_contents_multilang_path = DotCoreGeneralContentDAL::GENERAL_CONTENTS_MULTILANG_CONTENTS_LINK.'.';
            $general_contents_records = $general_contents_bll
                ->Fields(
                    array(
                        $general_contents_bll->getFieldName(),
                        $general_contents_bll->getFieldContentType(),
                        $general_contents_multilang_path.DotCoreGeneralContentMultilangContentDAL::GENERAL_CONTENTS_MULTILANG_TEXT
                    )
                )
                ->AddLink(
                    new DotCoreMultiLanguageLink(
                        DotCoreDAL::GetRelationship(DotCoreGeneralContentDAL::GENERAL_CONTENTS_MULTILANG_CONTENTS_LINK),
                        $general_contents_multilang_bll->getFieldGeneralContentsMultilangLanguageID())
                )
                ->Select();
            $general_contents = array();

            $count_general_contents = count($general_contents_records);
            for($i = 0; $i < $count_general_contents; $i++) {
                $general_content = $general_contents_records[$i];
                $name = $general_content->getName();
                $multilang_contents = DotCoreGeneralContentBLL::GetMultilangContents($general_content);
                $this_language_content = $multilang_contents[0];
                if($this_language_content != NULL) {
                    if($general_content->getContentType() != DotCoreGeneralContentDAL::CONTENT_TYPE_RICH) {
                        $general_contents[$name] = htmlspecialchars($this_language_content->getText());
                    }
                    else {
                        $general_contents[$name] = $this_language_content->getText();
                    }
                }
                else {
                    $general_contents[$name] = '';
                }
            }
            $this->general_contents = $general_contents;

            $title_field = $configure->GetField('site_title');
            if($this->page != NULL) {
                $page_title = $this->page->getTitle();
                $this->title = $title_field->GetValue(DotCorePageBLL::GetPageLanguage($this->page)->getLanguageCode());
                if(!empty($page_title)) {
                    $this->title .= ' - ' . $page_title;
                }
            }
            else {
                $lang = $this->language;
                $this->title = $title_field->GetValue($lang->getLanguageCode());
            }

            $i = 0;
            do{
                $replacements = DotCoreFeature::ParseFeatures($contents);
            }while($replacements > 0);

            // Set the header content
            $contents = str_replace('{header_content}', $this->GetAllHeaderContent(), $contents);
            $contents = remove_empty_lines($contents);
            echo $contents;
        }
        else
        {
            throw new Exception('No template found.');
        }

        self::$current_renderer = NULL;
    }

    public function GetAllHeaderContent() {
        $result = $this->page_header_content_accumulator;
        if($this->page != NULL) {
            $result .= $this->page->getHeaderContent();
        }
        return $result;
    }

    /**
     * Gets the default language code for requests that do not explicitly request languages
     * @return DotCoreLanguageRecord
     */
    public static function GetDefaultLanguage()
    {
        if(self::$default_language == NULL)
        {
            $lang_bll = new DotCoreLanguageBLL();
            $lang_bll->Fields($lang_bll->GetDAL()->GetFieldsDefinitions());

            /*
            // First try to detect the user's language with the $_SERVER global
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
            {
                // Grab all the languages
                $langs = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
                $count_langs = count($langs);
                // Start going through each one
                for ($i = 0; $i < $count_langs; $i++)
                {
                    $lang_code = $langs[$i];
                    // ISO 639-1 Format (2 first letters)
                    $lang_code = substr($lang_code,0,2);

                    // Try to populate with the given code
                    $lang = $lang_bll->ByLanguageCode($lang_code)->SelectFirstOrNull();
                    if($lang != NULL)
                    {
                        self::$default_language = $lang;
                        break;
                    }
                }
            }
             * 
             */

            // We reached here, so use the default language as set in the cofiguration of the website
            if(self::$default_language == NULL) {
                self::$default_language = $lang_bll->ByLanguageCode(self::GetConfiguration()->GetValue('default_language'))->SelectFirstOrNull();
            }
        }
        
        return self::$default_language;
    }

    public static function IsDefaultLanguage($lang_code) {
        return self::GetDefaultLanguage()->getLanguageCode() == $lang_code;
    }

    public function GetPageNotFoundMessage(DotCoreLanguageRecord $lang)
    {
        $configuration = self::GetConfiguration();
        $page_not_found_field = $configuration->GetField('page_not_found_content');
        return $page_not_found_field->GetValue($lang->getLanguageCode());
    }

    public function GetImagesFolderUrl()
    {
        return DotCoreConfig::$IMAGES_URL . $this->template . '/';
    }

    public function GetTemplateFolderUrl()
    {
        return DotCoreConfig::$LOCAL_TEMPLATES_URL . $this->template . '/';
    }

    public function GetTemplateFolderPath()
    {
        return DotCoreConfig::$LOCAL_TEMPLATES_PATH . $this->template . '/';
    }

    /**
     *
     * @param array $params
     */
    public function GetPostbackUrl($params = array()) {
        $page_record = $this->GetPageRecord();
        if($page_record) {
            $url = DotCorePageBLL::GetPagePath($this->GetPageRecord());
        }
        else {
            $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
        $params = array_merge($this->stored_values, $params);
        if(count($params) > 0) {
            $query_params = array();
            foreach($params as $key=>$val) {
                array_push($query_params, $key.'='.$val);
            }
            $url .= '?' . join('&', $query_params);
        }
        return $url;
    }

    public static function GetConfiguration() {
        if(self::$configuration == NULL) {
            self::$configuration = new DotCoreConfiguration(DotCoreConfig::$CONFIG_FILE);
        }
        return self::$configuration;
    }

}
?>

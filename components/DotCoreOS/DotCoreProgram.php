<?php

/**
 * Base class for all DotCorePrograms. Provides the interface and some base functionality
 * @author perrin
 * @version 0.01
 *
 */
abstract class DotCoreProgram extends DotCoreObject
{
	
    /**
     * Constructor for DotCoreProgram
     *
     * @param DotCoreProgramRecord $program_record
     * @return DotCoreProgram
     */
    protected function __construct(DotCoreProgramRecord $program_record)
    {
        $this->program_record = $program_record;
    }

    /**
     * Error list for storing errors across the program
     * @var array
     */
    private $error_list = array();

    /**
     * Holds the messages used throughout the program, useful for multilang programs
     * @var array of strings
     */
    private $messages = NULL;

    /**
     * Holds the current language of this program
     * @var DotCoreLanguageRecord
     */
    private $language = NULL;

    /**
     * Holds the language whose data is currently being edited
     * @var DotCoreLanguageRecord
     */
    private $currently_editing_language = NULL;
	
    /**
     * Holds the record from the DAL that contains information about the program
     * @var DotCoreProgramRecord
     */
    private $program_record = NULL;

    /**
     * Caches the URL to this program
     * @var string
     */
    private $program_url = NULL;

    /**
     * Caches the folder path to this program
     * @var string
     */
    private $server_folder_path = NULL;

    /**
     *
     * @var string
     */
    private $program_local_folder = NULL;

    /**
     *
     * @var string
     */
    private $program_local_url = NULL;


    /*
     *
     * Programs management
     *
     */

    /*
     *
     * Error management
     *
     */

    /**
     *
     * @return DotCoreProgramRecord
     */
    public function GetProgramRecord() {
        return $this->program_record;
    }

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
	
    /**
     * Called before page rendering begins, useful to take care of initialization of the object after the
     * admin has finished initializing, and take care of user input from the controls of this program
     * @return void
     * @throws Exception if something goes wrong
     */
    public function ProcessInput() {}

    /**
     * Method called to retrieve header content requiered for the program, like stylesheets or scripts.
     * Override to load the necessary things for the program
     * @return string
     */
    public function GetHeaderContent()
    {
        return '';
    }
	
    /**
     * Gets the content of this program
     * @return string
     */
    public function GetContent()
    {
            return '';
    }

    /**
     * Gets a link to navigate through this program successfully. Additional parameters can be set with the $parameters array
     * @param $parameters dictionary<string, string>, where the key is the name of the parameter, and the value is the value of the parameter
     * @return void
     */
    public function GetLink($parameters = array())
    {
        $parameters = array_merge(array('program'=>$this->GetType()), $parameters);
        $query = '';
        $i = 0;
        foreach($parameters as $key=>$value) {
            if($i != 0) {
                $query .= '&amp;';
            }
            $query .= $key . '=' . urlencode($value);
            $i++;
        }
        $result = $_SERVER['PHP_SELF'].'?'.$query;
        return $result;
    }
	
    /**
     * Gets a sortable table header for sortable tables
     * @param string $header_name
     * @param string $label
     * @return string
     */
    public function GetSortableTableHeader($header_name, $orientation, $message)
    {
        $params = array('sort'=>$header_name, 'orientation'=>$orientation);
        return '<th><a href="'.$this->GetLink($params).'">' . $message .'</a></th>';
    }

    /**
     * Gets the messages used throughout the program, and caches them
     * If it was translated to the language of the page, those messages will be used,
     * Otherwise the default language of the website will be tried, and if it fails, an empty messages array will be returned
     *
     * @return array of messages
     */
    public function GetMessages()
    {
        if($this->messages == NULL)
        {
            $messages_file = $this->GetServerFolderPath().'/lang.php';
            if(!is_file($messages_file))
            {
                $this->messages = new DotCoreMessages();
            }
            else
            {
                $this->messages = DotCoreMessages::GetMessages($messages_file, $this->GetLanguage()->getLanguageCode());
            }

            // Merge with local messages
            $local_messages_file = $this->GetLocalRootFolder().'/lang.php';
            if(is_file($local_messages_file)) {
                $this->messages->merge(DotCoreMessages::GetMessages($local_messages_file, $this->GetLanguage()->getLanguageCode()));
            }

            // Merge with global messages
            $global_messages = DotCoreOS::GetInstance()->GetMessages();
            $this->messages->merge($global_messages);
            // Make sure the messages of this program can override global messages by using its object

        }

        return $this->messages;
    }

    /**
     *
     * @return DotCoreLanguageRecord
     */
    public function GetLanguage() {
        if($this->language == NULL) {
            $this->language = DotCoreOS::GetInstance()->GetLanguage();
        }
        return $this->language;
    }

    /**
     * Deprecated
     * @return string
     */
    public function GetFolderPath()
    {
        if($this->program_url == NULL) {
            $this->program_url = DotCoreProgramBLL::GetProgramFolderPath($this->program_record);
        }
        return $this->program_url;
    }

    public function GetProgramUrl()
    {
        return $this->GetFolderPath() . '/';
    }

    public function GetServerFolderPath()
    {
        if($this->server_folder_path == NULL) {
            $this->server_folder_path = DotCoreProgramBLL::GetProgramServerFolderPath($this->program_record);
        }
        return $this->server_folder_path;
    }

    public function GetLocalRootFolder()
    {
        if($this->program_local_folder == NULL) {
            $this->program_local_folder = DotCoreProgramBLL::GetLocalRootFolder($this->program_record);
        }
        return $this->program_local_folder;
    }

    public function GetLocalRootUrl()
    {
        if($this->program_local_url == NULL) {
            $this->program_local_url = DotCoreProgramBLL::GetLocalRootUrl($this->program_record);
        }
        return $this->program_local_url;
    }

    public function GetCurrentEditingLanguage($cookie_name) {
        if($this->currently_editing_language == NULL) {
            // Determine what's the current language
            $languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();

            if(isset($_REQUEST['change_language']) && key_exists($_REQUEST['change_language'], $languages))
            {
                $this->currently_editing_language = $languages[$_REQUEST['change_language']];
            }

            if(
                $this->currently_editing_language == NULL &&
                isset($_COOKIE[$cookie_name]) &&
                key_exists($_COOKIE[$cookie_name], $languages))
            {
                $this->currently_editing_language = $languages[$_COOKIE[$cookie_name]];
            }

            if($this->currently_editing_language == NULL)
            {
                $default_lang_code = DotCoreOS::GetInstance()->GetConfiguration()->GetValue('admin_language');
                foreach($languages as $language_id => $language)
                {
                    if($language->getLanguageCode() == $default_lang_code)
                    {
                        break;
                    }
                }
                if(key_exists($language_id, $languages))
                {
                    $this->currently_editing_language = $languages[$language_id];
                }
            }

            if($this->currently_editing_language == NULL)
            {
                throw new Exception($messages['ErrorUndeterminedLanguage']);
            }
            else
            {
                $expires = time() + 60 * 60 * 24 * 365;
                setcookie($cookie_name, $this->currently_editing_language->getLanguageID(), $expires);
            }
        }
        return $this->currently_editing_language;
    }
}

?>
<?php

class ConfigurationEditor extends DotCoreProgram
{
    public function __construct(DotCoreProgramRecord $program_record)
    {
        parent::__construct($program_record);

        // Check for permissions
        $admin = DotCoreOS::GetInstance()->GetAdmin();
        if(!DotCoreAdminBLL::IsInRole($admin, DotCoreConfig::$ROLE_CONFIGURATION))
        {
            throw new PermissionDeniedException(DotCoreConfig::$ROLE_CONFIGURATION);
        }

        $this->mode = self::MODE_NORMAL;
    }

    /*
     *
     * Properties:
     *
     */

    const MODE_NORMAL = 1;

    private $changed = FALSE;
    /**
     *
     * @var ConfigurationFileEditor
     */
    private $form_generator = NULL;
    private $mode;

    /**
     * Gets the title for this program
     * @return string
     */
    public function GetTitle()
    {
        $messages = $this->GetMessages();
        return $messages["AdminTitleEditConfiguration"];
    }

    protected function GetForm() {

        if($this->form_generator == NULL)
        {
            $messages = $this->GetMessages();
            $configure = DotCorePageRenderer::GetConfiguration();

            $lang_bll = new DotCoreLanguageBLL();
            $langs_dictionary = $lang_bll->GetLanguagesCodeDictionary();
            $langs_code_dictionary = array();
            foreach($langs_dictionary as $lang_code => $lang) {
                $langs_code_dictionary[$lang_code] = $messages[$lang->getLanguageCode()];
            }
            $lang_element = new DotCoreComboBoxFormElement('languages-combo', $langs_code_dictionary);
            
            $this->form_generator = new ConfigurationFileEditor($configure, $messages, $this->GetLink());
            $this->form_generator->AddCustomElement('default_language', $lang_element);
            $this->form_generator->GenerateForm();
        }
        return $this->form_generator->getGeneratedForm();
    }
	
    /**
     * Gets the interface to use for the configuration of this feature
     * @return string
     */
    public function GetContent()
    {
        $messages = $this->GetMessages();
        $result = '';

        if($this->HasErrors())
        {
            $result .= $this->GetErrorsMarkup();
        }
        if($this->changed == TRUE)
        {
            $result .= '<p class="feedback">'.$messages['MessageSuccessfulChanges'].'</p>';
        }

        $result .= $this->GetForm()->__toString();
        return $result;
    }
	
    /**
     * Processes the input from the administration interface
     * @return void
     */
    public function ProcessInput()
    {
        $form = $this->GetForm();
        // Handle user data
        if($form->WasSubmitted())
        {
            $this->form_generator->ProcessForm();
            $this->changed = TRUE;
        }
    }
}

?>
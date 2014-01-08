<?php

class EventsManager extends DotCoreProgram
{
    public function __construct(DotCoreProgramRecord $program_record)
    {
        parent::__construct($program_record);

        // Check for permissions
        $admin = DotCoreOS::GetInstance()->GetAdmin();
        if(!DotCoreAdminBLL::IsInRole($admin, DotCoreConfig::$ROLE_EVENTS))
        {
            throw new PermissionDeniedException(DotCoreConfig::$ROLE_EVENTS);
        }
		
        if(isset($_REQUEST['add']))
        {
            $this->mode = self::MODE_ADD;
        }
        elseif(isset($_REQUEST['edit']))
        {
            $this->mode = self::MODE_EDIT;
        }
        else
        {
            $this->mode = self::MODE_NORMAL;
        }
    }
	
    /*
     *
     * Properties:
     *
     */

    const MODE_NORMAL = 1;
    const MODE_EDIT = 2;
    const MODE_ADD = 3;

    const LANGUAGE_COOKIE = 'events_manager_language';

    private $mode;

    /**
     *
     * @var DotCoreFormGenerator
     */
    private $events_form_generator = NULL;

    private $deleted = FALSE;
    private $edited = FALSE;
    private $inserted = FALSE;

    /*
     *
     * Overriding
     *
     */

    public function GetHeaderContent()
    {
        $result = '';
        $messages = $this->GetMessages();
        
        if($this->mode == self::MODE_NORMAL)
        {
            $current_language = $this->GetCurrentEditingLanguage(self::LANGUAGE_COOKIE);
            $languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
            $i = 0;
            foreach($languages as $language)
            {
                if($i != 0)
                {
                    $tab_items .= ',';
                }

                if($current_language->getLanguageID() == $language->getLanguageID())
                {
                    $active_tab = $i;
                }

                $i++;
                $tab_items .= '
                    {
                        contentEl: "events_'.$language->getLanguageCode().'",
                        title: "'.$messages[$language->getLanguageCode()].'",
                        data: { lang_id: '.$language->getLanguageID().' },
                        listeners: {activate: EventsManager.OnEventsLanguageTabActivate}
                    }';
            }

            $result .= '
            <link href="'.DotCoreConfig::$STYLES_URL.'extjs_tabs/tabs_styles.css" type="text/css" rel="stylesheet" />
            <!--[if lte IE 7]>
            <link href="'.DotCoreConfig::$STYLES_URL.'extjs_tabs/tabs_styles_iefix.css" type="text/css" rel="stylesheet" />
            <![endif]-->

            <script type="text/javascript" src="'.$this->GetFolderPath().'/events_manager.js"></script>
            <script type="text/javascript">
            //<![CDATA[
                var activeTab = '.$active_tab.';
                var tabItems = ['.$tab_items.'];
                var languageCookieName = "'.self::LANGUAGE_COOKIE.'";
            //]]>
            </script>
            ';
        }

        return $result;
    }
	
    /**
     * Gets the title for this program
     * @return string
     */
    public function GetTitle()
    {
        $messages = $this->GetMessages();

        if($this->mode == self::MODE_ADD)
        {
            return $messages['AdminTitleAddEvents'];
        }
        elseif($this->mode == self::MODE_EDIT)
        {
            return $messages['AdminTitleEditEvents'];
        }
        else
        {
            return $messages['AdminTitleManageEvents'];
        }
    }
	
    /**
     * Gets the interface to use for the configuration of this feature
     * 
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

        if($this->inserted)
        {
            $result .= '<p class="feedback">' . $messages['MessageEventAddedSuccessfully'] . '</p>';
        }
        if($this->edited)
        {
            $result .= '<p class="feedback">' . $messages['MessageEventEditedSuccessfully'] . '</p>';
        }
        if($this->deleted)
        {
            $result .= '<p class="feedback">' . $messages['MessageEventDeletedSuccessfully'] . '</p>';
        }

        if($this->mode == self::MODE_ADD || $this->mode == self::MODE_EDIT)
        {
            $result .= $this->events_form_generator->GetForm();
        }
        else
        {
            // Print table
            $result .= $this->GetTable();
        }
		
        return $result;
    }
	
    /**
     * Processes the input from the administration interface
     * @return void
     */
    public function ProcessInput()
    {
        $messages = $this->GetMessages();

        // Determine what's the current language
        $languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
        $current_language = $this->GetCurrentEditingLanguage(self::LANGUAGE_COOKIE);
		
        if(
            $this->mode == self::MODE_ADD ||
            $this->mode == self::MODE_EDIT)
        {
            $events_bll = new DotCoreEventBLL();
            $event_fields = array(
                $events_bll->getFieldTitle(),
                $events_bll->getFieldDescription(),
                $events_bll->getFieldDetails(),
                $events_bll->getFieldDate()
            );
            
            if($this->mode == self::MODE_ADD)
            {
                $this->event = $events_bll->GetNewRecord();
                $this->event->setEventLanguageID($current_language->getLanguageID());
            }
            else
            {
                $this->event = $events_bll
                    ->Fields($event_fields)
                    ->ByEventID($_REQUEST['edit'])
                    ->SelectFirstOrNull();
            }

            if($this->event != NULL)
            {
                $form_parameters = array();

                if($this->event->IsEmpty()) {
                    $form_parameters['add'] = $this->event->getEventID();
                    $submit_label = $messages['LabelAdd'];
                } else {
                    $form_parameters['edit'] = $this->event->getEventID();
                    $submit_label = $messages['LabelUpdate'];
                }

                $this->events_form_generator = new DotCoreFormGenerator('event_form', $this->GetLink($form_parameters));
                $this->events_form_generator
                    ->SetRecord($this->event)
                    ->SetMessages($messages)
                    ->SetFields($event_fields)
                    ->Generate();
                $this->events_form_generator->GenerateSubmitButton($submit_label);

                $form = $this->events_form_generator->GetForm();
                if($form->WasSubmitted())
                {
                    $events_bll->BeginTransaction($this->event);
                    $this->events_form_generator->ProcessForm();
                    $this->events_form_generator->Validate();
                    if(
                        $this->events_form_generator->HasErrors() == FALSE &&
                        $this->events_form_generator->TrySave($events_bll)
                        )
                    {
                        $events_bll->CommitTransaction($this->event);
                    }

                    if($events_bll->TransactionCommitted())
                    {
                        if($this->mode == self::MODE_ADD) {
                            $this->inserted = TRUE;
                        }
                        else {
                            $this->edited = TRUE;
                        }

                        $this->mode = self::MODE_NORMAL;
                    }
                    else {
                        $events_bll->Rollback($this->event);
                    }
                }
            }	
        }
        else
        {
            // Default events table mode
            if(isset($_REQUEST['delete']))
            {
                $events_bll = new DotCoreEventBLL();
                $event = $events_bll
                    ->Fields(
                        array(
                            $events_bll->getFieldEventID()
                        )
                    )
                    ->ByEventID($_REQUEST['delete'])
                    ->SelectFirstOrNull();

                if($event != NULL)
                {
                    try
                    {
                        $events_bll->Delete($event);
                        $this->deleted = TRUE;
                    }
                    catch(Exception $ex)
                    {
                        $this->AddError($ex->getMessage());
                    }
                }
                else
                {
                    $this->AddError($messages['ErrorNotFound']);
                }
            }
        }
    }

    /*
     *
     * Table Builders:
     *
     */
	
    public function GetTable()
    {
        $messages = $this->GetMessages();

        $result = '';
        $result .='
        <div id="events_panel">';

        // Determine what's the current language
        $languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();

        $events_bll = new DotCoreEventBLL();
        $events_order = new DotCoreDALSelectionOrder();
        $events_order->AddOrderUnit(
            new DotCoreFieldSelectionOrder($events_bll->getFieldEventLanguageID(), DotCoreFieldSelectionOrder::DIRECTION_ASC)
        );
        $events = $events_bll
            ->Fields(
                array(
                    $events_bll->getFieldTitle(),
                    $events_bll->getFieldDescription()
                )
            )
            ->Order($events_order)
            ->Select();

        $count_events = count($events);
        $i = 0;

        foreach($languages as $lang_id => $language)
        {
            $result .= '
                <div class="x-hide-display" id="events_'.$language->getLanguageCode().'">';
            
            $result .= '
                <table cellpadding="5" cellspacing="0" class="datagrid">
                <thead>
                    <th>'.$messages[DotCoreEventDAL::EVENTS_TITLE.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
                    <th>'.$messages['TableHeaderEdit'].'</th>
                    <th>'.$messages['TableHeaderDelete'].'</th>
                </thead>';

            for($j = 0; $i < $count_events && $events[$i]->getEventLanguageID() == $lang_id; $j++, $i++)
            {
                $class = ($j % 2 == 1) ? '' : 'class="alternating"';
                $curr_event = $events[$i];
                $event_id = $curr_event->getEventID();

                $result .= '
                    <tr '.$class.'>
                        <td>' . $curr_event->getEventTitle() . '</td>
                        <td><a href="'.$this->GetLink(array('edit'=>$event_id)).'">'.$messages['TableActionsEdit'].'</a></td>
                        <td><a href="'.$this->GetLink(array('delete'=>$event_id)).'" onclick="return confirm(\''.$messages['MessageEventConfirmDeletion'].'\');">'.$messages['TableActionsDelete'].'</a></td>
                    </tr>';

            }

            $result .= '
                    </table>
                    <div class="sub_menu">
                    <a href="'.$this->GetLink(array('add'=>1, 'change_language'=>$lang_id)).'">' . $messages['AdminTitleAddEvents'] . '</a>
                    </div>
                </div>';
        }

        $result .= '
            </div>';
		
        return $result;
    }
}

?>
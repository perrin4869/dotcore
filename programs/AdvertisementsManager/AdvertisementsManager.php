<?php

class AdvertisementsManager extends DotCoreProgram
{
    public function __construct(DotCoreProgramRecord $program_record)
    {
        parent::__construct($program_record);

        // Check for permissions
        $admin = DotCoreOS::GetInstance()->GetAdmin();
        if(!DotCoreAdminBLL::IsInRole($admin, DotCoreConfig::$ROLE_ADVERTISEMENTS))
        {
            throw new PermissionDeniedException(DotCoreConfig::$ROLE_ADVERTISEMENTS);
        }

        if(isset($_REQUEST["add"]))
        {
            $this->mode = self::MODE_ADD;
        }
        elseif(isset($_REQUEST["edit"]))
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

    /**
     * Stores the advertisement being worked on
     * @var DotCoreAdvertisementRecord
     */
    private $ad = NULL;

    /**
     * Form generator used to edit ad by this editor
     * @var DotCoreFormGenerator
     */
    private $ad_form_generator;

    private $mode;

    private $deleted = FALSE;
    private $edited = FALSE;
    private $inserted = FALSE;
	
    /**
     * Gets the title for this program
     * @return string
     */
    public function GetTitle()
    {
        $messages = $this->GetMessages();

        if($this->mode == self::MODE_ADD)
        {
            return $messages['AdTitleInsert'];
        }
        elseif($this->mode == self::MODE_EDIT)
        {
            return $messages['AdTitleEdit'];
        }
        else
        {
            return $messages['AdTitleManage'];
        }
    }
	
    /**
     * Gets the interface to use for the configuration of this feature
     * @return string
     */
    public function GetContent()
    {
        $messages = $this->GetMessages();
        $result = '';

        if($this->deleted)
        {
            $result .= '<p class="feedback">' . $messages['MessageAdDeletedSuccessfully'] . '</p>';
        }
        if($this->inserted)
        {
            $result .= '<p class="feedback">' . $messages['MessageAdAddedSuccessfully'] . '</p>';
        }
        if($this->edited)
        {
            $result .= '<p class="feedback">' . $messages['MessageAdEditedSuccessfully'] . '</p>';
        }
        if($this->HasErrors())
        {
            $result .= $this->GetErrorsMarkup();
        }

        if($this->mode == self::MODE_ADD || $this->mode == self::MODE_EDIT)
        {
            // Show form
            $result .= $this->ad_form_generator->GetForm();
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
		
        if($this->mode == self::MODE_ADD || $this->mode == self::MODE_EDIT)
        {
            $ad_bll = new DotCoreAdvertisementBLL();
            $ad_fields = array(
                $ad_bll->getFieldText(),
                $ad_bll->getFieldIsActive(),
                $ad_bll->getFieldMediaName(),
                $ad_bll->getFieldUrl(),
            );

            if($this->mode == self::MODE_ADD)
            {
                $this->ad = $ad_bll->GetNewRecord();
                $this->ad->setAdvertisementIsActive(TRUE);
            }
            else
            {
                $this->ad = $ad_bll
                    ->Fields($ad_fields)
                    ->ByAdvertisementID($_REQUEST['edit'])
                    ->SelectFirstOrNull();
            }

            if($this->ad != NULL)
            {
                $form_parameters = array();

                if($this->ad->IsEmpty()) {
                    $form_parameters['add'] = $this->ad->getAdvertisementID();
                    $submit_label = $messages['LabelAdd'];
                } else {
                    $form_parameters['edit'] = $this->ad->getAdvertisementID();
                    $submit_label = $messages['LabelUpdate'];
                }

                $this->ad_form_generator = new DotCoreFormGenerator('ad_form', $this->GetLink($form_parameters));
                $this->ad_form_generator
                    ->SetRecord($this->ad)
                    ->SetMessages($messages)
                    ->SetFields($ad_fields)
                    ->Generate();
                $this->ad_form_generator->GenerateSubmitButton($submit_label);

                $form = $this->ad_form_generator->GetForm();
                if($form->WasSubmitted())
                {
                    $ad_bll->BeginTransaction($this->ad);
                    $this->ad_form_generator->ProcessForm();
                    $this->ad_form_generator->Validate();
                    if(
                        !$this->ad_form_generator->HasErrors() &&
                        $this->ad_form_generator->TrySave($ad_bll))
                    {
                        $ad_bll->CommitTransaction($this->ad);
                    }

                    if($ad_bll->TransactionCommitted())
                    {
                        if($this->mode == self::MODE_ADD)
                        {
                            $this->inserted = TRUE;
                        }
                        else
                        {
                            $this->edited = TRUE;
                        }

                        $this->mode = self::MODE_NORMAL;
                    }
                    else
                    {
                        $ad_bll->Rollback($this->ad);
                    }
                }
            }
            else
            {
                $this->AddError($messages['ErrorAdNotFound']);
            }
        }
        else
        {
            if(isset($_REQUEST['delete']))
            {
                $ad_bll = new DotCoreAdvertisementBLL();
                $this->ad = $ad_bll
                    ->Fields(
                        array(
                            $ad_bll->getFieldAdvertisementID()
                        )
                    )
                    ->ByAdvertisementID($_REQUEST['delete'])
                    ->SelectFirstOrNull();

                if($this->ad != NULL)
                {
                    try {
                        $ad_bll->Delete($this->ad);
                        $this->deleted = TRUE;
                    }
                    catch(Exception $ex)
                    {
                        $this->AddError($ex->getMessage());
                    }
                }
                else
                {
                    $this->AddError($messages['ErrorAdNotFound']);
                }
            }
        }
    }
	
    public function GetTable()
    {
        $messages = $this->GetMessages();

        $result = '';

        $result .= '
            <table cellpadding="5" cellspacing="0" class="datagrid">
                <thead>
                    <th>'.$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_TEXT.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
                    <th>'.$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_URL.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
                    <th>'.$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_IS_ACTIVE.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
                    <th>'.$messages['TableHeaderEdit'].'</th>
                    <th>'.$messages['TableHeaderDelete'].'</th>
                </thead>';
			

        $ads_bll = new DotCoreAdvertisementBLL();
        $ads = $ads_bll
            ->Fields(
                array(
                    $ads_bll->getFieldMediaName(),
                    $ads_bll->getFieldIsActive(),
                    $ads_bll->getFieldUrl()
                )
            )
            ->Select();
        $count_ads = count($ads);
        for($i = 0; $i < $count_ads; $i++)
        {
            $ad = $ads[$i];
            $class = ($i % 2 != 0) ? "" : 'class="alternating"';
			
            $result .= '
                <tr '.$class.'>
                    <td>' . $ad->getAdvertisementText() . '</td>
                    <td>' . $ad->getAdvertisementUrl() . '</td>
                    <td><input type="checkbox" disabled="disabled"'.($ad->getAdvertisementIsActive() == TRUE ? ' checked="checked"' : '').'></td>
                    <td><a href="'.$this->GetLink(array('edit'=>$ad->getAdvertisementID())).'">'.$messages['TableActionsEdit'].'</a></td>
                    <td><a href="'.$this->GetLink(array('delete'=>$ad->getAdvertisementID())).'" onclick="return confirm(\''.$messages['MessageAdConfirmDeletion'].'\');">'.$messages['TableActionsDelete'].'</a></td>
                </tr>';
        }
			
        $result .= '
            </table>';

        $result .= '
            <div class="sub_menu">
                <a href="'.$this->GetLink(array('add'=>1)).'">' . $messages['AdTitleInsert'] . '</a>
            </div>';
		
        return $result;
    }

}

?>
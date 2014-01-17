<?php

class ContactUsManager extends DotCoreProgram
{
	public function __construct(DotCoreProgramRecord $program_record)
	{
		parent::__construct($program_record);

		// Check for permissions
		$admin = DotCoreOS::GetInstance()->GetAdmin();
		if(!DotCoreAdminBLL::IsInRole($admin, DotCoreConfig::$ROLE_CONTACT_US_RECIPIENTS))
		{
			throw new PermissionDeniedException(DotCoreConfig::$ROLE_CONTACT_US_RECIPIENTS);
		}

		if(isset($_REQUEST['add'])) {
			$this->mode = self::CONTACT_US_MODE_ADD;
		}
		elseif(isset($_REQUEST['edit'])) {
			$this->mode = self::CONTACT_US_MODE_EDIT;
		}
		elseif(isset($_REQUEST['edit_fields'])) {
			$this->mode = self::CONTACT_US_MODE_EDIT_FIELDS;
		}
		elseif(isset($_REQUEST['edit_email'])) {
			$this->mode = self::CONTACT_US_MODE_EDIT_EMAIL;
		}
		else
		{
			$this->mode = self::CONTACT_US_MODE_NORMAL;
		}
	}

	/*
	 *
	 * Properties:
	 *
	 */

	/**
	 * Shows a simple table for all the galleries
	 * @var int
	 */
	const CONTACT_US_MODE_NORMAL = 1;
	/**
	 * Shows the form needed for adding a gallery
	 * @var int
	 */
	const CONTACT_US_MODE_ADD = 2;
	/**
	 * Shows the form needed for editing a gallery
	 * @var int
	 */
	const CONTACT_US_MODE_EDIT = 3;
	/**
	 * Shows the form needed for editing field options
	 * @var int
	 */
	const CONTACT_US_MODE_EDIT_FIELDS = 4;
	/**
	 * Shows the form needed for editing email options
	 * @var int
	 */
	const CONTACT_US_MODE_EDIT_EMAIL = 5;

	const LANGUAGE_COOKIE = 'contact_us_manager_lang_cookie';


	private $mode;

	/**
	 * Holds the currently worked on contact us recipient record
	 * @var DotCoreContactUsRecipientRecord
	 */
	private $contact_us_recipient = NULL;

	/**
	 * Stores the generator for the form of contact us recipient
	 * @var DotCoreFormGenerator
	 */
	private $contact_us_recipient_form_generator = NULL;

	/**
	 *
	 * @var MessagesFileEditor
	 */
	private $contact_us_email_messages_editor = NULL;

	/**
	 *
	 * @var MessagesFileEditor
	 */
	private $contact_us_fields_messages_editor = NULL;

	private $inserted = FALSE;
	private $edited = FALSE;
	private $deleted = FALSE;
	private $edited_email = FALSE;
	private $edited_fields = FALSE;


	/**
	 * Gets the title for this program
	 * @return string
	 */
	public function GetTitle()
	{
		$messages = $this->GetMessages();
		$title = '';

		if($this->mode == self::CONTACT_US_MODE_ADD)
		{
			$title = $messages['TitleAddContactUsRecipient'];
		}
		else
		{
			$title = $messages['TitleEditContactUsRecipient'];
		}

		return $title;
	}

	public function GetHeaderContent()
	{
		$result = '';
		$messages = $this->GetMessages();

		if($this->mode == self::CONTACT_US_MODE_NORMAL)
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
						contentEl: "news_'.$language->getLanguageCode().'",
						title: "'.$messages[$language->getLanguageCode()].'",
						data: { lang_id: '.$language->getLanguageID().' }
					}';
			}
			
			if(DotCoreOS::GetInstance()->GetLanguage()->getLanguageDirection() == DotCoreLanguageDAL::LANGUAGES_DIRECTION_RTL) {
				$result .= '
				<link href="'.DotCoreConfig::$STYLES_URL.'extjs_tabs/tabs_styles.css" type="text/css" rel="stylesheet" />
				<!--[if lte IE 7]>
				<link href="'.DotCoreConfig::$STYLES_URL.'extjs_tabs/tabs_styles_iefix.css" type="text/css" rel="stylesheet" />
				<![endif]-->';
			}

			$result .= '
				<script type="text/javascript" src="'.$this->GetFolderPath() . '/contact_us_recipient_editor.js"></script>
				<script type="text/javascript">
				//<![CDATA[
				var activeTab = '.$active_tab.';
				var tabItems = ['.$tab_items.'];
				//]]>
				</script>';
		}
		elseif($this->mode == self::CONTACT_US_MODE_EDIT_FIELDS || $this->mode == self::CONTACT_US_MODE_EDIT_EMAIL) {
			if(DotCoreLanguageBLL::IsMultilanguage())
			{
				$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
				$i = 0;
				$tab_items = array();
				foreach($languages as $language)
				{
					$tab_item = '
					{
						contentEl: "messages-'.$language->getLanguageCode().'",
						title: "'.$messages[$language->getLanguageCode()].'"
					}';

					array_push($tab_items, $tab_item);
				}
				if(DotCoreOS::GetInstance()->GetLanguage()->getLanguageDirection() == DotCoreLanguageDAL::LANGUAGES_DIRECTION_RTL) {
					$result .= '
					<link href="'.DotCoreConfig::$STYLES_URL.'extjs_tabs/tabs_styles.css" type="text/css" rel="stylesheet" />
					<!--[if lte IE 7]>
					<link href="'.DotCoreConfig::$STYLES_URL.'extjs_tabs/tabs_styles_iefix.css" type="text/css" rel="stylesheet" />
					<![endif]-->';
				}

				$result .= '
				<script type="text/javascript" src="'.$this->GetFolderPath() .'/contact_us_messages_editor.js"></script>
				<script type="text/javascript">
					//<![CDATA[
					var tabItems = ['.join(',', $tab_items).'];
					//]]>
				</script>';
			}
		}

		return $result;
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

		if($this->inserted)
		{
			$result .= '<p class="feedback">'.$messages['MessageSuccessfulAddition'].'</p>';
		}
		if($this->edited || $this->edited_email || $this->edited_fields)
		{
			$result .= '<p class="feedback">'.$messages['MessageSuccessfulChanges'].'</p>';
		}
		if($this->deleted)
		{
			$result .= '<p class="feedback">'.$messages['MessageSuccessfulDeletion'].'</p>';
		}

		if($this->mode == self::CONTACT_US_MODE_ADD || $this->mode == self::CONTACT_US_MODE_EDIT)
		{
			$result .= $this->contact_us_recipient_form_generator->GetForm();
		}
		elseif($this->mode == self::CONTACT_US_MODE_EDIT_FIELDS) {
			if($this->contact_us_fields_messages_editor != NULL) {
				$result .= $this->contact_us_fields_messages_editor->getGeneratedForm()->__toString();
			}
		}
		elseif($this->mode == self::CONTACT_US_MODE_EDIT_EMAIL) {
			if($this->contact_us_email_messages_editor != NULL) {
				$result .= $this->contact_us_email_messages_editor->getGeneratedForm()->__toString();
			}
		}
		else
		{
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

		if($this->mode == self::CONTACT_US_MODE_ADD || $this->mode == self::CONTACT_US_MODE_EDIT)
		{
			$contact_us_recipient_bll = new DotCoreContactUsRecipientBLL();
			$contact_us_recipient_fields = array(
				$contact_us_recipient_bll->getFieldName(),
				$contact_us_recipient_bll->getFieldEmail()
			);

			if($this->mode == self::CONTACT_US_MODE_ADD)
			{
				$this->contact_us_recipient = $contact_us_recipient_bll->GetNewRecord();
				$this->contact_us_recipient->setContactUsRecipientLanguageID($current_language->getLanguageID());
			}
			else
			{
				$this->contact_us_recipient = $contact_us_recipient_bll
					->Fields($contact_us_recipient_fields)
					->ByContactUsRecipientID($_REQUEST['edit'])
					->SelectFirstOrNull();
			}

			if($this->contact_us_recipient != NULL)
			{
				if($this->mode == self::CONTACT_US_MODE_ADD) {
					$link_params = array('add'=>'');
					$label_submit_button = $messages['LabelAdd'];
				}
				else {
					$link_params = array('edit'=>$this->contact_us_recipient->getContactUsRecipientID());
					$label_submit_button = $messages['LabelUpdate'];
				}

				$form_generator = new DotCoreFormGenerator('contact_us_recipient_form', $this->GetLink($link_params));

				$form_generator
					->SetRecord($this->contact_us_recipient)
					->SetFields($contact_us_recipient_fields)
					->SetMessages($messages)
					->AddUniqueKeyFieldMapping(DotCoreContactUsRecipientDAL::CONTACT_US_UNIQUE_EMAIL_KEY, $contact_us_recipient_bll->getFieldEmail())
					->Generate();
				$form_generator->GenerateSubmitButton($label_submit_button);

				$this->contact_us_recipient_form_generator = $form_generator;
				$form = $this->contact_us_recipient_form_generator->GetForm();
				if($form->WasSubmitted())
				{
					$contact_us_recipient_bll->BeginTransaction($this->contact_us_recipient);
					$this->contact_us_recipient_form_generator->ProcessForm();
					$this->contact_us_recipient_form_generator->Validate();
					if(
						$this->contact_us_recipient_form_generator->HasErrors() == FALSE &&
						$this->contact_us_recipient_form_generator->TrySave($contact_us_recipient_bll)
						)
					{
						$contact_us_recipient_bll->CommitTransaction($this->contact_us_recipient);
					}

					if($contact_us_recipient_bll->TransactionCommitted())
					{
						if($this->mode == self::CONTACT_US_MODE_ADD)
						{
							$this->inserted = TRUE;
						}
						else
						{
							$this->edited = TRUE;
						}

						$this->mode = self::CONTACT_US_MODE_NORMAL;
					}
					else
					{
						$contact_us_recipient_bll->Rollback($this->contact_us_recipient);
					}
				}
			}
			else
			{
				$this->AddError($messages['MessageContactUsRecipientNotFound']);
			}

		}
		elseif($this->mode == self::CONTACT_US_MODE_EDIT_FIELDS) {
			$contact_us_fields_messages = new DotCoreMessages($this->GetContactUsConfigFolder().'contact_fields.php');
			$this->contact_us_fields_messages_editor = new MessagesFileEditor($contact_us_fields_messages, $_SERVER['REQUEST_URI']);
			$form = $this->contact_us_fields_messages_editor->GenerateForm();
			if($form->WasSubmitted())
			{
				if($this->contact_us_fields_messages_editor->ProcessForm())
				{
					$this->edited_fields = TRUE;
				}
				else
				{
					$this->AddError($messages['MessageFailedChanges']);
				}
			}
		}
		elseif($this->mode == self::CONTACT_US_MODE_EDIT_EMAIL) {
			$contact_us_fields_messages = new DotCoreMessages($this->GetContactUsConfigFolder().'lang.php');
			$this->contact_us_email_messages_editor = new MessagesFileEditor($contact_us_fields_messages, $_SERVER['REQUEST_URI']);
			$form = $this->contact_us_email_messages_editor->GenerateForm();
			if($form->WasSubmitted())
			{
				if($this->contact_us_email_messages_editor->ProcessForm())
				{
					$this->edited_fields = TRUE;
				}
				else
				{
					$this->AddError($messages['MessageFailedChanges']);
				}
			}
		}
		else
		{
			if(isset($_REQUEST['delete']))
			{
				$contact_us_recipient_bll = new DotCoreContactUsRecipientBLL();
				$this->contact_us_recipient = $contact_us_recipient_bll
					->Fields(
						array(
							$contact_us_recipient_bll->getFieldContactUsRecipientID()
						)
					)
					->ByContactUsRecipientID($_REQUEST['delete'])
					->SelectFirstOrNull();

				if($this->contact_us_recipient != NULL)
				{
					try
					{
						$contact_us_recipient_bll->Delete($this->contact_us_recipient);
						$this->deleted = TRUE;
					}
					catch(Exception $ex)
					{
						$this->AddError($ex->getMessage());
					}
				}
				else
				{
					$this->AddError($messages['MessageContactUsRecipientNotFound']);
				}
			}
		}
	}

	protected function GetContactUsConfigFolder() {
		return DotCoreContactUs::GetConfigFolder();
	}

	public function GetTable()
	{
		$messages = $this->GetMessages();
		$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
		$result = '';

		$contact_us_recipient_bll = new DotCoreContactUsRecipientBLL();
		$recipients = $contact_us_recipient_bll
			->Fields(array(
					$contact_us_recipient_bll->getFieldName(),
					$contact_us_recipient_bll->getFieldEmail()
				))
			->OrderedByLanguage()
			->Select();
		$count_recipients = count($recipients);

		$result .='
		<div id="contact_us_recipient_panel">';
		$i = 0;
		foreach($languages as $lang_id => $language)
		{
			$result .= '
				<div class="x-hide-display" id="news_'.$language->getLanguageCode().'">';

			$result .= '
				<table cellpadding="5" cellspacing="0" class="datagrid">
				<thead>
						<th>'.$messages['TableHeaderContactUsRecipientName'].'</th>
						<th>'.$messages['TableHeaderContactUsRecipientEmail'].'</th>
						<th class="command">'.$messages['TableHeaderEdit'].'</th>
						<th class="command">'.$messages['TableHeaderDelete'].'</th>
				</thead>';

			for($j = 0; $i < $count_recipients && $recipients[$i]->getContactUsRecipientLanguageID() == $lang_id; $j++, $i++)
			{
				$class = ($j % 2 == 1) ? '' : 'class="alternating"';
				$recipient = $recipients[$i];
				$news_id = $recipient->getContactUsRecipientID();

				$result .= '
					<tr '.$class.'>
						<td>' . $recipient->getContactUsRecipientName() . '</td>
						<td>' . $recipient->getContactUsRecipientEmail() . '</td>
						<td class="command"><a href="'.$this->GetLink(array('edit'=>$recipient->getContactUsRecipientID())).'"><img alt="'.$messages['TableActionsEdit'].'" src="'.DotCoreConfig::$GLOBAL_ADMIN_URL.'images/edit.gif" /></a></td>
						<td class="command"><a href="'.$this->GetLink(array('delete'=>$recipient->getContactUsRecipientID())).'" onclick="return confirm(\''.$messages['MessageContactUsRecipientDeletionConfirm'].'\');"><img alt="'.$messages['TableActionsDelete'].'" src="'.DotCoreConfig::$GLOBAL_ADMIN_URL.'images/delete.gif" /></a></td>
					</tr>';
			}

			$result .= '
					</table>
					<div class="sub_menu">
						<a href="'.$this->GetLink(array('add'=>1,'change_language'=>$lang_id)).'">'.$messages['TitleAddContactUsRecipient'].'</a>
					</div>
				</div>';
			}
		$result .= '</div>';

		return $result;
	}

}

?>
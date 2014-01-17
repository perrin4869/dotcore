<?php

class PagesManager extends DotCoreProgram
{
	public function __construct(DotCoreProgramRecord $program_record)
	{
		parent::__construct($program_record);

		// Check for permissions
		$admin = DotCoreOS::GetInstance()->GetAdmin();
		if(!DotCoreAdminBLL::IsInRole($admin, DotCoreConfig::$ROLE_PAGES))
		{
			throw new PermissionDeniedException(DotCoreConfig::$ROLE_PAGES);
		}

		if(isset($_REQUEST['add']))
		{
			$this->mode = self::PAGES_EDITOR_MODE_ADD;
		}
		elseif(isset($_REQUEST['edit']))
		{
			$this->mode = self::PAGES_EDITOR_MODE_EDIT;
		}
		elseif(isset($_REQUEST['edit_general_contents']))
		{
			$this->mode = self::PAGES_EDITOR_MODE_EDIT_SHARED_CONTENTS;
		}
		elseif(isset($_REQUEST['file_manager']))
		{
			$this->mode = self::PAGES_EDITOR_MODE_FILE_MANAGER;
		}
		elseif(isset($_REQUEST['templates_editor']))
		{
			$this->mode = self::PAGES_EDITOR_MODE_TEMPLATES_EDITOR;
		}
		elseif(isset($_REQUEST['templates_messages_editor']))
		{
			$this->mode = self::PAGES_EDITOR_MODE_TEMPLATES_MESSAGES_EDITOR;
		}
		elseif(isset($_REQUEST['templates_configuration_editor']))
		{
			$this->mode = self::PAGES_EDITOR_MODE_TEMPLATES_CONFIGURATION_EDITOR;
		}
		else
		{
			$this->mode = self::PAGES_EDITOR_MODE_NORMAL;
		}
	}

	/**
	 * Properties:
	 *
	 */

	/**
	 * Stores the hierarchy of pages in an array, for each language (the key is the ID of the language)
	 * @var array of DotCorePagesRecord
	 */
	private $pages_hierarchy = NULL;

	/**
	 *
	 * @var DotCoreFormGenerator
	 */
	private $page_form_generator = NULL;

	/**
	 * Stores all the general contents, ordered by language
	 * @var array of array of
	 */
	private $general_contents = NULL;

	/**
	 * Holds the form for General Contents
	 * @var DotCoreForm
	 */
	private $general_contents_form = NULL;

	/**
	 *
	 * @var ConfigurationFileEditor
	 */
	private $template_config_editor = NULL;

	/*
	 *
	 *
	 * @var MessagesFileEditor
	 */
	private $template_msg_editor = NULL;

	private $deleted = FALSE;
	private $edited = FALSE;
	private $inserted = FALSE;
	private $edited_shared_contents = FALSE;
	private $template_edited = FALSE;

	const PAGES_EDITOR_MODE_ADD = 1;
	const PAGES_EDITOR_MODE_EDIT = 2;
	const PAGES_EDITOR_MODE_NORMAL = 4;
	const PAGES_EDITOR_MODE_EDIT_SHARED_CONTENTS = 6;
	const PAGES_EDITOR_MODE_FILE_MANAGER = 7;
	const PAGES_EDITOR_MODE_TEMPLATES_EDITOR = 8;
	const PAGES_EDITOR_MODE_TEMPLATES_CONFIGURATION_EDITOR = 9;
	const PAGES_EDITOR_MODE_TEMPLATES_MESSAGES_EDITOR = 10;

	const LANGUAGE_COOKIE = 'pages_language';

	private $mode;

	/**
	 * Gets the title for this program
	 * @return string
	 */
	public function GetTitle()
	{
		$messages = $this->GetMessages();

		if($this->mode == self::PAGES_EDITOR_MODE_ADD)
		{
			return $messages['TitlePageInsert'];
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_EDIT)
		{
			return $messages['TitlePageEdit'];
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_EDIT_SHARED_CONTENTS)
		{
			return $messages['AdminTitleEditSharedContents'];
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_FILE_MANAGER)
		{
			return $messages['AdminTitleUploadImages'];
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_TEMPLATES_EDITOR)
		{
			return $messages['AdminTitleTemplatesEditor'];
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_TEMPLATES_MESSAGES_EDITOR)
		{
			return $messages['AdminTitleTemplatesMessagesEditor'];
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_TEMPLATES_CONFIGURATION_EDITOR)
		{
			return $messages['AdminTitleTemplatesConfigurationEditor'];
		}
		else
		{
			return $messages['AdminTitleEditPages'];
		}
	}

	public function GetHeaderContent()
	{
		$result = '';
		$messages = $this->GetMessages();
		$program_name = $this->GetType();

		if($this->mode == self::PAGES_EDITOR_MODE_ADD)
		{
			$result .= '
			<script type="text/javascript" src="'.$this->GetFolderPath().'custom-form-elements.js"></script>
			<link type="text/css" href="'.$this->GetFolderPath().'custom-form-elements.css" />

			<script type="text/javascript">
			//<![CDATA[
			function OnPageAdderLoad()
			{
				var pageName = document.getElementById("'.DotCorePageDAL::PAGE_NAME.'");
				if (pageName != null)
				{
				addEvent(pageName, "keyup", OnPageNameChange);
				addEvent(pageName, "change", OnPageNameChange);
				}
			}

			function OnPageNameChange()
			{
				var pageUrl, pageTitle;
				pageUrl = document.getElementById("'.DotCorePageDAL::PAGE_URL.'");
				pageTitle = document.getElementById("'.DotCorePageDAL::PAGE_TITLE.'");

				var val = this.value;
				var url = val.replace(/[ /]/g, "-").toLowerCase();

				if(pageUrl != null)
				{
					pageUrl.value = url;
				}

				if(pageTitle != null)
				{
					pageTitle.value = val;
				}
			}

			addEvent(window, "load", OnPageAdderLoad);
			//]]>
			</script>';
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_EDIT_SHARED_CONTENTS)
		{
			$tab_items = '';
			$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
			$current_language = $this->GetCurrentEditingLanguage(self::LANGUAGE_COOKIE);
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
					contentEl: "general_contents_'.$language->getLanguageCode().'",
					title: "'.$messages[$language->getLanguageCode()].'",
					data: { lang_id: '.$language->getLanguageID().' },
					listeners: {activate: GeneralContentsEditor.GeneralContentLanguageTabActivate}
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
			<script type="text/javascript" src="'.$this->GetFolderPath().'general_contents.js"></script>
			<script type="text/javascript">
			//<![CDATA[
			var activeTab = '.$active_tab.';
			var tabItems = ['.$tab_items.'];
			var languageCookieName = "'.self::LANGUAGE_COOKIE.'";
			//]]>
			</script>';
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_TEMPLATES_MESSAGES_EDITOR) {
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
				<script type="text/javascript" src="'.$this->GetFolderPath() .'template_messages_editor.js"></script>
				<script type="text/javascript">
					//<![CDATA[
					var tabItems = ['.join(',', $tab_items).'];
					//]]>
				</script>';
			}
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_NORMAL)
		{
			$tab_items = '';
			$active_tab = '';

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
					contentEl: "table_'.$language->getLanguageCode().'",
					title: "'.$messages[$language->getLanguageCode()].'",
					data: { lang_id: '.$language->getLanguageID().' },
					listeners: {activate: PagesManager.OnPagesLanguageTabActivate}
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
			<link href="'.$this->GetFolderPath().'custom-form-elements.css" type="text/css" rel="stylesheet" />
			<script type="text/javascript" src="'.$this->GetFolderPath().'custom-form-elements.js"></script>
			<script type="text/javascript" src="'.$this->GetFolderPath().'pages_manager.js"></script>

			<script type="text/javascript">
			//<![CDATA[
			var activeTab = '.$active_tab.';
			var tabItems = ['.$tab_items.'];
			var languageCookieName = "'.self::LANGUAGE_COOKIE.'";
			//]]>
			</script>';
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

		if($this->inserted == TRUE)
		{
			$result .= '<p class="feedback">' . $messages['MessagePageAddedSuccessfully'] . '</p>';
		}
		elseif($this->edited == TRUE)
		{
			$result .= '<p class="feedback">' . $messages['MessagePageEditedSuccessfully'] . '</p>';
		}

		if($this->HasErrors())
		{
			$result .= $this->GetErrorsMarkup();
		}

		if($this->mode == self::PAGES_EDITOR_MODE_ADD || $this->mode == self::PAGES_EDITOR_MODE_EDIT)
		{
			if($this->page_form_generator != NULL)
			{
				$result .= $this->page_form_generator->GetForm()->__toString();
			}
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_EDIT_SHARED_CONTENTS)
		{
			$general_contents = $this->GetGeneralContents();
			// If no contents are found, report it
			$items_count = count($general_contents);
			if($items_count > 0)
			{
				$general_contents_form = $this->GetGeneralContentsForm();
				if($general_contents_form->WasSubmitted())
				{
					if($this->edited_shared_contents)
					{
						$result .= '<p class="feedback">'.$messages['MessageSuccessfulChanges'].'</p>';
					}
					else
					{
						$result .= '<p class="feedback">'.$messages['MessageFailedChanges'].'</p>';
					}
				}

				$result .= $general_contents_form->__toString();
				$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
			}
			else
			{
				$result .= '<p class="feedback">'.$messages['ErrorContentsNotFound'].'</p>';
			}
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_FILE_MANAGER)
		{
			$filename = $_SERVER['DOCUMENT_ROOT'].'/images/user_files';
			if(!is_dir($filename))
			{
				mkdir($filename, 0755);
			}
			$file_manager = new FileManager($filename);
			$result .= $file_manager->create();
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_TEMPLATES_EDITOR)
		{
			if($this->template_edited) {
				$result .= '<p class="feedback">'.$messages['MessageSuccessfulChanges'].'</p>';
			}

			$result .= $this->GetTemplatesTable();
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_TEMPLATES_CONFIGURATION_EDITOR) {
			if($this->template_config_editor != NULL) {
				$result .= $this->template_config_editor->getGeneratedForm()->__toString();
			}
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_TEMPLATES_MESSAGES_EDITOR) {
			if($this->template_msg_editor != NULL) {
				$result .= $this->template_msg_editor->getGeneratedForm()->__toString();
			}
		}
		else // if($this->mode == self::PAGES_EDITOR_MODE_NORMAL)
		{
			if($this->deleted)
			{
				$result .= '<p class="feedback">' . $messages['MessagePageDeleteSuccess'] . '</p>';
			}

			// Print table
			$result .= $this->GetAllLanguagesTables();
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
		$current_language = $this->GetCurrentEditingLanguage(self::LANGUAGE_COOKIE);

		if($this->mode == self::PAGES_EDITOR_MODE_ADD || $this->mode == self::PAGES_EDITOR_MODE_EDIT)
		{
			// Prepare the BLLs
			$pages_bll = new DotCorePageBLL();
			$contents_bll = new DotCoreContentBLL();

			// Prepare the records to be updated | inserted
			if($this->mode == self::PAGES_EDITOR_MODE_ADD) {
				$page = $pages_bll->GetNewRecord();
				$page->setPageLanguageID($current_language->getLanguageID());
				$page->setOrder($pages_bll->GetCount()); // TODO: Think real space
			}
			else {
				$page = $pages_bll
					->Fields(array
						(
							$pages_bll->getFieldName(),
							$pages_bll->getFieldTitle(),
							$pages_bll->getFieldUrl(),
							$pages_bll->getFieldPageParentID(),
							$pages_bll->getFieldPageLanguageID(),
							$pages_bll->getFieldAppearsInNav(),
							$pages_bll->getFieldHeaderContent(),
						)
					)
					->ByPageID($_REQUEST['edit'])
					->SelectFirstOrNull();
			}

			if($page == NULL) {
				$this->AddError($messages['ErrorPageNotFound']);
			}
			else {

				// Prepare parameters for the form
				if($this->mode == self::PAGES_EDITOR_MODE_ADD) {
					$link_parameters = array('add'=>'');
				}
				else {
					$link_parameters = array('edit'=>$page->getPageID());
				}

				// Prepare the form
				$this->page_form_generator = new DotCoreFormGenerator('page-form', $this->GetLink($link_parameters));

				// Prepare the hints for the form
				$parent_pages_choices = array();
				$parent_pages_choices[0] = $messages['LabelParentPage'];
				$this->GetParentPagesChoices(
					$this->GetPagesHierarchy($current_language->getLanguageID()),
					$parent_pages_choices,
					$page->getPageID());

				$page_fields = array(
					$pages_bll->getFieldName(),
					$pages_bll->getFieldUrl(),
					$pages_bll->getFieldTitle(),
					$pages_bll->getFieldAppearsInNav(),
					$pages_bll->getFieldPageParentID()
				);

				$curr_admin = DotCoreOS::GetInstance()->GetAdmin();
				if($curr_admin->isAdvanced())
				{
					$header_content_field = $pages_bll->getFieldHeaderContent();
					array_push($page_fields, $header_content_field);
					$this->page_form_generator->SetCustomFormElement(
						DotCorePageDAL::PAGE_HEADER_CONTENT,
						new DotCoreMultilineTextFormElement(
							$this->page_form_generator->GetUniqueElementName(
								$page,
								$header_content_field
							)
						)
					);
				}

				$this->page_form_generator
					->SetRichElementClass('advanced-rich-editor')
					->SetFieldChoices(DotCorePageDAL::PAGE_PARENT_ID, $parent_pages_choices)
					->AddUniqueKeyFieldMapping(DotCorePageDAL::PAGE_UNIQUE_URL, $pages_bll->getFieldUrl())
					->SetFields($page_fields)
					->SetRecord($page)
					->SetMessages($messages);
				$this->page_form_generator->Generate();

				// Prepare the contents part of the form
				$contents_fields = array($contents_bll->getFieldText());
				$this->page_form_generator->SetFields($contents_fields);

				if($this->mode == self::PAGES_EDITOR_MODE_ADD) {
					$content_record = $contents_bll->GetNewRecord();
					// setContentTemplateContentID ACTUALLY SETS THE ID FOR NOW, IT'S TEMPORARY
					// $content_record->setContentTemplateContentID(1); // No templates for now, so arbitrary number
					// We'll set the page number when we insert it, we can't do now, because it'll be empty
					$contents = array($content_record);
					$this->page_form_generator
						->SetRecord($content_record);
					$this->page_form_generator->Generate();
				}
				else {
					// TODO: Proper template
					$contents = $contents_bll
						->Fields($contents_fields)
						->ByPageID($page->getPageID())
						->Select();
					$count_contents_records = count($contents);
					for($i = 0; $i < $count_contents_records; $i++) {
						$this->page_form_generator->SetRecord($contents[$i]);
						$this->page_form_generator->Generate();
					}
				}

				if($this->mode == self::PAGES_EDITOR_MODE_ADD) {
					$this->page_form_generator->GenerateSubmitButton($messages['LabelPageAdd']);
				}
				else {
					$this->page_form_generator->GenerateSubmitButton($messages['LabelPageEdit']);
				}

				$form = $this->page_form_generator->GetForm();
				if($form->WasSubmitted())
				{
					$pages_bll->BeginTransaction($page);

					// OK, the form is completely generated, now to process it
					$this->page_form_generator
						->SetFields($page_fields)
						->SetRecord($page)
						->ProcessForm();
					$this->page_form_generator->Validate();

					$count_contents_records = count($contents);
					$this->page_form_generator->SetFields($contents_fields);
					for($i = 0; $i < $count_contents_records; $i++)
					{
						$this->page_form_generator
							->SetRecord($contents[$i])
							->ProcessForm();
						$this->page_form_generator->Validate();
					}

					if(!$this->page_form_generator->HasErrors())
					{
						$result = $this->page_form_generator
							->SetRecord($page)
							->TrySave($pages_bll);
						if($result)
						{
							// If the result is false, don't try to set the contents, because we may be trying to set the page id to null
							for($i = 0; $i < $count_contents_records; $i++)
							{
								$content = $contents[$i];
								$content->setContentPageID($page->getPageID());
								$result =
									$result  &&
									$this->page_form_generator
										->SetRecord($content)
										->TrySave($contents_bll);
							}
						}

						if($result)
						{
							$pages_bll->CommitTransaction($page);
						}
					}

					if($pages_bll->TransactionCommitted())
					{
						if($this->mode == self::PAGES_EDITOR_MODE_ADD) {
							$this->inserted = TRUE;
						}
						else {
							$this->edited = TRUE;
						}

						// Reload the pages hierarchy so the correct data appears on screen
						// TODO: Non-lazy solution!
						$this->LoadPagesHierarchy();
						$this->mode = self::PAGES_EDITOR_MODE_NORMAL;
					}
					else
					{
						$pages_bll->Rollback($page);
					}
				}
			}
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_EDIT_SHARED_CONTENTS)
		{
			$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
			$general_contents_bll = new DotCoreGeneralContentBLL();
			$general_contents_bll->AddLink(DotCoreGeneralContentDAL::GENERAL_CONTENTS_MULTILANG_CONTENTS_LINK);
			$general_contents_multilang_bll = new DotCoreGeneralContentMultilangContentBLL();

			$general_contents = $this->GetGeneralContents($general_contents_bll);

			$general_contents_form = $this->GetGeneralContentsForm();
			if($general_contents_form->WasSubmitted())
			{
				$count_general_contents = count($general_contents);

				$general_contents_bll->BeginTransaction($general_contents);
				try {
					for($i = 0; $i < $count_general_contents; $i++)
					{
						$general_content = $general_contents[$i];
						$general_contents_multilang = DotCoreBLL::GetDictionary(
							$general_contents_bll->GetMultilangContents($general_content),
							$general_contents_multilang_bll->getFieldGeneralContentsMultilangLanguageID());
						foreach($languages as $language)
						{
							$element_name = 'general_content_text'.$general_content->GetUniqueCode().'_'.$language->GetUniqueCode();
							$value = $general_contents_form
								->GetFormElement($element_name)
								->GetValue();

							if(key_exists($language->getLanguageID(), $general_contents_multilang))
							{
								$current_lang_content = $general_contents_multilang[$language->getLanguageID()];
							}
							else
							{
								$current_lang_content = $general_contents_multilang_bll->GetNewRecord();
								$current_lang_content->setLanguageID($language->getLanguageID());
								DotCoreGeneralContentBLL::AddMultilangContent($general_content, $current_lang_content);
							}

							$current_lang_content->setText($value);
						}
						$general_contents_bll->Save($general_content);
					}

					$general_contents_bll->CommitTransaction($general_contents);
				}
				catch(Exception $ex) {
					$general_contents_bll->Rollback($general_contents);
				}

				if($general_contents_bll->TransactionCommitted())
				{
					$this->edited_shared_contents = TRUE;
				}
			}
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_TEMPLATES_CONFIGURATION_EDITOR) {
			$template_id = $_REQUEST['templates_configuration_editor'];
			$template_bll = new DotCoreTemplateBLL();
			$template = $template_bll
				->Fields(
					array(
						$template_bll->getFieldFolder()
					)
				)
				->ByTemplateID($template_id)
				->SelectFirstOrNull();

			if($template) {
				$template_configuration = DotCoreTemplateBLL::GetTemplateConfiguration($template);
				if($template_configuration) {
					$config_labels = $messages;
					$config_template_labels = DotCoreTemplateBLL::GetTemplateFolderPath($template).'configuration_labels.php';
					if(is_file($config_template_labels)) {
						$config_labels->merge(
							new DotCoreMessages(
								$config_template_labels,
								DotCoreOS::GetInstance()
									->GetLanguage()
									->getLanguageCode()
								)
							);
					}
					$this->template_config_editor = new ConfigurationFileEditor(
						$template_configuration,
						$config_labels,
						$_SERVER['REQUEST_URI']);
					$form = $this->template_config_editor->GenerateForm();
					if($form->WasSubmitted())
					{
						if($this->template_config_editor->ProcessForm())
						{
							$this->mode = self::PAGES_EDITOR_MODE_TEMPLATES_EDITOR;
							$this->template_edited = TRUE;
						}
						else
						{
							$this->AddError($messages['ErrorTemplateConfigurationEditFail']);
						}
					}
				}
				else {
					$this->AddError($messages['ErrorTemplateConfigurationEditFail']);
					$this->mode = self::PAGES_EDITOR_MODE_TEMPLATES_EDITOR;
				}
			}
			else {
				$this->AddError($messages['ErrorTemplateNotFound']);
				$this->mode = self::PAGES_EDITOR_MODE_TEMPLATES_EDITOR;
			}
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_TEMPLATES_MESSAGES_EDITOR) {
			$template_id = $_REQUEST['templates_messages_editor'];
			$template_bll = new DotCoreTemplateBLL();
			$template = $template_bll
				->Fields(
					array(
						$template_bll->getFieldFolder()
					)
				)
				->ByTemplateID($template_id)
				->SelectFirstOrNull();

			if($template) {
				$template_messages = DotCoreTemplateBLL::GetTemplateMessages($template);
				if($template_messages) {
					$this->template_msg_editor = new MessagesFileEditor($template_messages, $_SERVER['REQUEST_URI']);
					$form = $this->template_msg_editor->GenerateForm();
					if($form->WasSubmitted())
					{
						if($this->template_msg_editor->ProcessForm())
						{
							$this->mode = self::PAGES_EDITOR_MODE_TEMPLATES_EDITOR;
							$this->template_edited = TRUE;
						}
						else
						{
							$this->AddError($messages['ErrorTemplateMessagesEditFail']);
						}
					}
				}
				else {
					$this->AddError($messages['ErrorTemplateMessagesEditFail']);
					$this->mode = self::PAGES_EDITOR_MODE_TEMPLATES_EDITOR;
				}
			}
			else {
				$this->AddError($messages['ErrorTemplateNotFound']);
				$this->mode = self::PAGES_EDITOR_MODE_TEMPLATES_EDITOR;
			}
		}
		elseif($this->mode == self::PAGES_EDITOR_MODE_NORMAL)
		{
			if(isset($_REQUEST['delete']))
			{
				$pages_bll = new DotCorePageBLL();
				$page = $pages_bll
					->Fields(
						array(
							$pages_bll->getFieldPageID()
						)
					)
					->ByPageID($_REQUEST['delete'])
					->SelectFirstOrNull();

				// We need to execute 2 deletions - one for the page, and another for its contents
				// Start a transaction
				$pages_bll->BeginTransaction($page);

				if($page != NULL)
				{
					$contents_bll = new DotCoreContentBLL();
					$contents_bll->ByPageID($page->getPageID());
					
					try {
						$pages_bll->Delete($page);
						$contents_bll->Delete();
						$pages_bll->CommitTransaction($page);
						$this->deleted = TRUE;
					}
					catch(Exception $ex)
					{
						$this->AddError($ex->getMessage());
					}
				}
				else
				{
					$this->AddError($messages['ErrorPageNotFound']);
				}

				if(!$pages_bll->TransactionCommitted())
				{
					$pages_bll->Rollback($page);
				}
			}
		}
	}

	public function GetAllLanguagesTables()
	{
		$result = '';
		$result .= '<div id="PagesTabs">';

		$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
		foreach($languages as $language)
		{
			$result .= '<div class="x-hide-display" id="table_'.$language->getLanguageCode().'">';
			$result .= $this->GetTable($language->getLanguageID());
			$result .= '</div>';
		}

		$result .= '
		</div>';

		return $result;
	}

	public function GetTable($language)
	{
		$result = '';
		$messages = $this->GetMessages();
		$link_params = array('add'=>1, 'change_language'=>$language);

		$result .= '
			<table cellpadding="5" cellspacing="0" class="datagrid">
			<thead>
				<th>'.$messages['LabelPageName'].'</th>
				<th class="command">'.$messages['LabelPageAppearsInNav'].'</th>
				<th class="command">'.$messages['TableHeaderEdit'].'</th>
				<th class="command">'.$messages['TableHeaderDelete'].'</th>
				<th class="command">'.$messages['TableHeaderPreview'].'</th>
				<th class="command">'.$messages['TableHeaderMove'].'</th>
				<th class="command">'.$messages['TableHeaderDefaultPage'].'</th>
			</thead>
			<tbody>';

			$pages_hierarchy = $this->GetPagesHierarchy($language);
			$pages_counter = 0;
			foreach($pages_hierarchy as $page_node)
			{
				$this->GetTableHelper($result, $page_node, 1, $pages_counter);
			}

		$result .= '
			</tbody>
		</table>';

		$result .= '
		<div class="sub_menu">
			<a href="'.$this->GetLink($link_params).'">
				<img alt="' . $messages['AdminTitleAddPages'] . '" src="'.$this->GetFolderPath().'add-page.png" /> '.$messages['AdminTitleAddPages'].'
			</a>
		</div>';
		return $result;
	}

	/**
	 * Helper for GetTable, iterates through the nodes
	 * @param string $result The result will be stored here
	 * @param DotCoreTreeNode $page_node Holds the node for which we build the page row
	 * @param int $generation The Generation of this node
	 * @param int $pages_counter Counter to check for alternating rows
	 * @return void
	 */
	private function GetTableHelper(&$result, DotCoreTreeNode $page_node, $generation, &$pages_counter)
	{
		$this->GetPageTableRow($result, $page_node, $generation, $pages_counter);

		++$generation;
		foreach($page_node->nodes as $child_node)
		{
			$this->GetTableHelper($result, $child_node, $generation, $pages_counter);
		}
	}

	/**
	 * Gets the table row for a given $pageNode and stores the result in $result
	 * @param string $result The result will be stored here
	 * @param DotCoreTreeNode $page_node Holds the node for which we build the page row
	 * @param int $generation The Generation of this node
	 * @param int $pages_counter Counter to check for alternating rows
	 * @return void
	 */
	private function GetPageTableRow(&$result, DotCoreTreeNode $page_node, $generation, &$pages_counter)
	{
		$messages = $this->GetMessages();

		$program_name = $this->GetType();

		$class = ($pages_counter % 2 == 0) ? '' : ' class="alternating"';
		$checked = $page_node->value->getPageAppearsInNav() ? 'checked="checked"' : '';
		$padding = 20 * ($generation - 1);

		// No performance penalty - the dictionary is cached
		$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
		$this_language = $languages[$page_node->value->getPageLanguageID()];
		$radio_name = "default_lang_".$this_language->getLanguageCode();
		// Check if default
		$default = ($this_language->getLanguageDefaultPageID() == $page_node->value->getPageID()) ? ' checked="checked"' : '';
		$order = $page_node->value->getOrder();
		$id = $page_node->value->getPageID();
		$dir = DotCoreOS::GetInstance()->GetLanguage()->getLanguageDirection() == DotCoreLanguageDAL::LANGUAGES_DIRECTION_LTR ? 'left' : 'right';

		$result .= '
			<tr'.$class.' dotcore:page_id="'.$id.'" dotcore:page_generation="'.$generation.'">
				<td style="padding-'.$dir.': '.$padding.'px">
					<img alt="expand" src="'.DotCoreConfig::$GLOBAL_ADMIN_URL.'images/arrow.png" />' . $page_node->value->getName() . '
				</td>
				<td>
					<input type="checkbox" dotcore:page_id="'.$page_node->value->getPageID().'" class="styled" '.$checked.'/>
				</td>
				<td class="command">
					<a href="'.$this->GetLink(array('edit'=>$page_node->value->getPageID())).'">
						<img alt="'.$messages['TableActionsEdit'].'" src="'.DotCoreConfig::$GLOBAL_ADMIN_URL.'images/edit.png" />
					</a>
				</td>
				<td class="command">
					<a href="'.$this->GetLink(array('delete'=>$page_node->value->getPageID())).'" onclick="return confirm(\''.$messages['MessagePageConfirmDelete'].'\');">
						<img alt="'.$messages['TableActionsDelete'].'" src="'.DotCoreConfig::$GLOBAL_ADMIN_URL.'images/delete.png" />
					</a>
				</td>
				<td class="command">
					<a href="/'.$this_language->getLanguageCode().'/'.$page_node->value->getUrl().'" target="_blank">
						<img alt="'.$messages['TableHeaderPreview'].'" src="'.DotCoreConfig::$GLOBAL_ADMIN_URL.'images/view.png" />
					</a>
				</td>
				<td class="command">
					<img alt="'.$messages['MoveUp'].'" src="'.DotCoreConfig::$GLOBAL_ADMIN_URL.'images/up.gif" name="up_arrow" />
					<img alt="'.$messages['MoveDown'].'" src="'.DotCoreConfig::$GLOBAL_ADMIN_URL.'images/down.gif" name="down_arrow" />
				</td>
				<td class="command">
					<input type="radio" class="styled" value="'.$page_node->value->getPageID().'" name="'.$radio_name.'"'.$default.' />
				</td>
			</tr>';

		++$pages_counter;
	}

	protected function GetListItemContent(DotCorePageRecord $page)
	{
		$messages = $this->GetMessages();

		return  $page->getPageName() . ' - ' .$page->getPageUrl() . '
				<a href="'.$this->GetLink(array('edit'=>$page->getPageID())).'">'.$messages['TableActionsEdit'].'</a>
				<a href="'.$this->GetLink(array('delete'=>$page->getPageID())).'" onclick="return confirm(\''.$messages['MessagePageConfirmDelete'].'\'">'.$messages['TableActionsDelete'].'</a>';
	}

	protected function GetTemplatesTable() {
		$messages = $this->GetMessages();

		$result = '';
		$result .= '
				<table cellpadding="5" cellspacing="0" class="datagrid">
				<thead>
						<th>'.$messages['TableHeaderTemplateName'].'</th>
						<th>'.$messages['TableHeaderTemplateFolder'].'</th>
						<th class="command">'.$messages['TableHeaderEditContents'].'</th>
						<th class="command" style="width: 90px;">'.$messages['TableHeaderEditConfiguration'].'</th>
				</thead>';

		$template_bll = new DotCoreTemplateBLL();
		$templates = $template_bll
			->Fields(
				array(
					$template_bll->getFieldName(),
					$template_bll->getFieldFolder()
				)

			)
			->Select();
		$count_templates = count($templates);

		for($i = 0; $i <  $count_templates; $i++)
		{
			$template = $templates[$i];
			$class = ($i % 2 != 0) ? '' : 'class="alternating"';

			$edit_contents_td = $messages['TableHeaderEditContents'];
			$edit_configuration_td = $messages['TableHeaderEditConfiguration'];
			if(DotCoreTemplateBLL::GetTemplateMessages($template))
			{
				$edit_contents_td = '<a href="'.$this->GetLink(array('templates_messages_editor'=>$template->getTemplateID())).'">'.$edit_contents_td.'</a>';
			}
			if(DotCoreTemplateBLL::GetTemplateConfiguration($template))
			{
				$edit_configuration_td = '<a href="'.$this->GetLink(array('templates_configuration_editor'=>$template->getTemplateID())).'">'.$edit_configuration_td.'</a>';
			}

			$result .= '<tr '.$class.'>';
			$result .= '<td>' . $template->getTemplateName() . '</td>';
			$result .= '<td>' . $template->getTemplateFolder() . '</td>';
			$result .= '<td class="command">'.$edit_contents_td.'</td>';
			$result .= '<td class="command" style="width: 90px;">'.$edit_configuration_td.'</td>';
			$result .= '</tr>';
		}

		$result .= '
				</table>';

		return $result;
	}

	/*
	 *
	 *
	 * General Contents Methods:
	 *
	 *
	 */

	/**
	 * Gets the general contents that are edited by this manager
	 * @return array
	 */
	protected function GetGeneralContents(DotCoreGeneralContentBLL $general_contents_bll = NULL)
	{
		if($this->general_contents === NULL) // If it's an empty array
		{
			$general_contents_multilang_bll = new DotCoreGeneralContentMultilangContentBLL();
			$general_contents_multilang_path = new DotCoreDALPath(DotCoreGeneralContentDAL::GENERAL_CONTENTS_MULTILANG_CONTENTS_LINK);
			$this->general_contents = $general_contents_bll
				->Fields(
					array(
						$general_contents_bll->getFieldContentType(),
						$general_contents_bll->getFieldContentDescription(),
						new DotCoreDALEntityPath(
							$general_contents_multilang_bll->getFieldGeneralContentsMultilangLanguageID(),
							$general_contents_multilang_path),
						new DotCoreDALEntityPath(
							$general_contents_multilang_bll->getFieldGeneralContentMultilangText(),
							$general_contents_multilang_path)
					)
				)
				->Ordered()
				->Select();
		}
		return $this->general_contents;
	}

	/**
	 * Gets the general contents form used by this manager
	 * @return DotCoreForm
	 */
	protected function GetGeneralContentsForm() {
		if($this->general_contents_form == NULL) {
			$this->general_contents_form = new DotCoreForm('general_contents_form', $this->GetLink(array('edit_general_contents'=>'')));

			$messages = $this->GetMessages();
			$general_contents = $this->GetGeneralContents();
			$general_contents_multilang_bll = new DotCoreGeneralContentMultilangContentBLL();
			$count_general_contents = count($general_contents);
			$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
			$this->general_contents_form->InsertMarkup('
				<div id="shared_contents_panel">');
			foreach($languages as $language)
			{
				$language_id = $language->getLanguageID();
				$this->general_contents_form->InsertMarkup('
					<div class="x-hide-display" id="general_contents_'.$language->getLanguageCode().'">');

				for($i = 0; $i < $count_general_contents; $i++)
				{
					$general_content = $general_contents[$i];
					$general_content_type = $general_content->getContentType();
					$general_content_form_element_name = 'general_content_text'.$general_content->GetUniqueCode().'_'.$language->GetUniqueCode();
					switch($general_content_type)
					{
						case DotCoreGeneralContentDAL::CONTENT_TYPE_ONE_LINE:
							$element = new DotCoreTextFormElement($general_content_form_element_name);
							break;
						case DotCoreGeneralContentDAL::CONTENT_TYPE_MULTILINE:
							$element = new DotCoreMultilineTextFormElement($general_content_form_element_name);
							break;
						case DotCoreGeneralContentDAL::CONTENT_TYPE_RICH:
							$element = new DotCoreRichTextFormElement($general_content_form_element_name);
							$element->SetRichEditorClass('rich-general-content');
							break;
						default:
							$element = new DotCoreRichTextFormElement($general_content_form_element_name);
							$element->SetRichEditorClass('rich-general-content');
							break;
					}
					$general_content_multilang = DotCoreBLL::GetDictionary(
							DotCoreGeneralContentBLL::GetMultilangContents($general_content),
							$general_contents_multilang_bll->getFieldGeneralContentsMultilangLanguageID());
					if(key_exists($language_id, $general_content_multilang))
					{
						$element->SetDefaultValue($general_content_multilang[$language_id]->getText());
					}
					$general_content_multilang = $general_content_multilang[$language->getLanguageID()];
					$this->general_contents_form->AddFormElement($element, $general_content->getDescription());
				}
				$this->general_contents_form->AddFormElement(
					new DotCoreSubmitFormElement(
						'general_contents_submit_'.$language->getLanguageID(),
						$messages['LabelUpdate']
					)
				);
				$this->general_contents_form->InsertMarkup('
					</div>');
			}
			$this->general_contents_form->InsertMarkup('
				</div>');
		}

		return $this->general_contents_form;
	}

	/*
	 *
	 *
	 * Pages Methods
	 *
	 *
	 */

	/**
	 * Returns the hierarchy of pages for a given language
	 * @param int $lang The ID of the requested language
	 * @return array The hierarchy of languages in a tree
	 */
	protected function GetPagesHierarchy($lang)
	{
		if($this->pages_hierarchy == NULL) {
			$this->LoadPagesHierarchy();
		}

		return $this->pages_hierarchy[$lang];
	}

	protected function LoadPagesHierarchy() {

		$pages_bll = new DotCorePageBLL();
		// Sort them by Language, and then by Order, since each language has a different order
		$this->pages_hierarchy = $pages_bll
			->OrderedByLanguageAndOrder()
			->Fields(
				array(
					$pages_bll->getFieldName(),
					$pages_bll->getFieldPageLanguageID(),
					$pages_bll->getFieldAppearsInNav(),
					$pages_bll->getFieldUrl(),
					$pages_bll->getFieldOrder(),
				)
			)
			->SelectHierarchyByLanguages();
		$pages_bll->FinalizeSelection();

		// In the case there are no pages for a certain language, let's make that language an empty array
		$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
		foreach($languages as $language_id => $lang)
		{
			if(!key_exists($language_id, $this->pages_hierarchy))
			{
				$this->pages_hierarchy[$language_id] = array();
			}
		}
	}

	/**
	 * Gets an array of pages with the hierarchy, excluding the current page
	 * @param array $parents
	 * @param array $result
	 * @param int $current_id
	 */
	protected function GetParentPagesChoices($parent_nodes, &$result, $current_id = NULL, $numeric_prefix = '')
	{
		$i = 1;
		foreach($parent_nodes as $node)
		{
			// Don't let a parent be the child of itself, or of one of its children
			if($node->value->getPageID() != $current_id)
			{
				$result[$node->value->getPageID()] = $numeric_prefix . $i . ' - ' . $node->value->getName();
				$this->GetParentPagesChoices($node->nodes, $result, $current_id, $numeric_prefix . $i . '.');
			}
			$i++;
		}
	}
}

?>

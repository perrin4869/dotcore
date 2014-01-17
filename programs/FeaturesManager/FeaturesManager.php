<?php

class FeaturesManager extends DotCoreProgram
{
	public function __construct(DotCoreProgramRecord $program_record)
	{
		parent::__construct($program_record);

		// Check for permissions
		$admin = DotCoreOS::GetInstance()->GetAdmin();
		if(!DotCoreAdminBLL::IsInRole($admin, DotCoreConfig::$ROLE_FEATURES))
		{
			throw new PermissionDeniedException(DotCoreConfig::$ROLE_FEATURES);
		}

		if(isset($_REQUEST['edit_contents'])){
			$this->mode = self::MODE_EDIT_MESSAGES;
		}
		elseif(isset($_REQUEST['edit_configuration'])) {
			$this->mode = self::MODE_EDIT_CONFIGURATION;
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

	/**
	 * Shows a simple table for all the features
	 * @var int
	 */
	const MODE_NORMAL = 1;
	/**
	 * Shows the form needed for editing message files
	 * @var int
	 */
	const MODE_EDIT_MESSAGES = 2;
	/**
	 * Shows the form needed for editing configuration files
	 * @var int
	 */
	const MODE_EDIT_CONFIGURATION = 3;


	private $mode;

	/**
	 * Holds the messages editor used in this program
	 * @var MessagesFileEditor
	 */
	private $messages_editor = NULL;

	/**
	 * Holds the configuration editor used in this program
	 * @var ConfigurationFileEditor
	 */
	private $configuration_editor = NULL;

	/**
	 *  Holds the feature currently being used
	 * @var DotCoreFeatureRecord
	 */
	private $current_feature = NULL;

	/**
	 *
	 * @var array
	 */
	private static $configuration_cache = array();

	private $edited_messages = FALSE;
	private $edited_configuration = FALSE;
	
	/**
	 * Gets the title for this program
	 * @return string
	 */
	public function GetTitle()
	{
		$messages = $this->GetMessages();
		$title = '';

		if($this->mode == self::MODE_EDIT_MESSAGES)
		{
			$title = $messages['TitleEditFeaturesMessages'] . ' - ' . $this->current_feature->getFeatureName();
		}
		else
		{
			$title = $messages['TitleManageFeatures'];
		}

		return $title;
	}
	
	public function GetHeaderContent()
	{
		$result = '';
		$messages = $this->GetMessages();

		if($this->mode == self::MODE_EDIT_MESSAGES)
		{
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
				<script type="text/javascript" src="'.$this->GetFolderPath() .'/features_contents_editor.js"></script>
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

		if($this->edited_messages)
		{
			$result .= '<p class="feedback">'.$messages['MessageSuccessfulChanges'].'</p>';
		}
		if($this->edited_configuration)
		{
			$result .= '<p class="feedback">'.$messages['MessageSuccessfulChanges'].'</p>';
		}

		if($this->mode == self::MODE_EDIT_MESSAGES)
		{
			$result .= $this->messages_editor->getGeneratedForm()->__toString();
		}
		elseif($this->mode == self::MODE_EDIT_CONFIGURATION)
		{
			$result .= $this->configuration_editor->getGeneratedForm()->__toString();
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
		
		if($this->mode == self::MODE_EDIT_MESSAGES)
		{
			$feature_bll = new DotCoreFeatureBLL();
			$feature = $feature_bll
				->Fields(
					array(
						 $feature_bll->getFieldFeatureName()
					)
				)
				->ByFeatureID($_REQUEST['edit_contents'])
				->SelectFirstOrNull();

			if($feature != NULL)
			{
				$this->current_feature = $feature;
				$feature_messages = DotCoreFeatureBLL::GetFeatureMessages($this->current_feature);
				
				if(empty($feature_messages))
				{
					$this->mode = self::MODE_NORMAL;
					$this->AddError($messages['FeatureHasNoMessages']);
				}
				else
				{
					$contents_manager = $this->GetFeatureMessagesEditor($feature_messages);
					$form = $contents_manager->GenerateForm();
					if($form->WasSubmitted())
					{
						if($contents_manager->ProcessForm())
						{
							$this->mode = self::MODE_NORMAL;
							$this->edited_messages = TRUE;
						}
						else
						{
							$this->AddError($messages['FeatureContentsEditFailed']);
						}
					}
				}
			}
			else
			{
				$this->mode = self::MODE_NORMAL;
				$this->AddError($messages['FeatureNotFound']);
			}
		}
		elseif($this->mode == self::MODE_EDIT_CONFIGURATION)
		{
			$feature_bll = new DotCoreFeatureBLL();
			$feature = $feature_bll
				->Fields(
					array(
						 $feature_bll->getFieldFeatureName()
					)
				)
				->ByFeatureID($_REQUEST['edit_configuration'])
				->SelectFirstOrNull();

			if($feature != NULL)
			{
				$this->current_feature = $feature;
				$feature_configuration = $this->GetFeatureConfiguration($this->current_feature);
				$lang_code = DotCoreOS::GetInstance()->GetLanguage()->getLanguageCode();

				// First check if the global configuration labels exist, and load them
				// Then override them with the local version
				if(is_file(DotCoreFeatureBLL::GetFeatureServerFolderPath($feature).'configuration_labels.php')) {
					$messages->merge(
						new DotCoreMessages(
							DotCoreFeatureBLL::GetFeatureServerFolderPath($feature).'configuration_labels.php',
							$lang_code
						)
					);
				}
				if(is_file(DotCoreFeatureBLL::GetFeatureLocalRootFolder($feature).'configuration_labels.php')) {
					$messages->merge(
						new DotCoreMessages(
							DotCoreFeatureBLL::GetFeatureLocalRootFolder($feature).'configuration_labels.php',
							$lang_code
						)
					);
				}

				$configuration_editor = $this->GetFeatureConfigurationEditor($feature_configuration,$messages);
				$form = $configuration_editor->GenerateForm();
				if($form->WasSubmitted())
				{
					if($configuration_editor->ProcessForm())
					{
						$this->mode = self::MODE_NORMAL;
						$this->edited_configuration = TRUE;
					}
					else
					{
						$this->AddError($messages['FeatureConfigurationEditFailed']);
					}
				}
			}
			else
			{
				$this->mode = self::MODE_NORMAL;
				$this->AddError($messages['FeatureNotFound']);
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
						<th>'.$messages['TableHeaderFeatureName'].'</th>
						<th>'.$messages['TableHeaderFeatureCommands'].'</th>
						<th class="command">'.$messages['TableHeaderEditContents'].'</th>
						<th class="command" style="width: 90px;">'.$messages['TableHeaderEditConfiguration'].'</th>
				</thead>';

		$features_bll = new DotCoreFeatureBLL();
		$features_commands_path = DotCoreFeaturesCommandsDAL::FEATURE_FEATURE_COMMANDS_LINK . '.';
		$features = $features_bll
			->Fields(
				array(
					$features_bll->getFieldFeatureName(),
					$features_commands_path . DotCoreFeaturesCommandsDAL::FEATURES_COMMANDS_COMMAND
				)

			)
			->Select();
		$count_features = count($features);
		
		for($i = 0; $i <  $count_features; $i++)
		{
			$feature = $features[$i];
			$class = ($i % 2 != 0) ? '' : 'class="alternating"';

			$commands = $feature->GetLinkValue(DotCoreFeaturesCommandsDAL::FEATURE_FEATURE_COMMANDS_LINK);
			$count_commands = count($commands);
			if($count_commands > 0)
			{
				$feature_commands = '<ul>';
				for($j = 0; $j < $count_commands; $j++)
				{
					$feature_commands .= '<li>'.$commands[$j]->getFeatureCommand().'</li>';
				}
				$feature_commands .= '</ul>';
			}
			else
			{
				$feature_commands = $messages['FeatureCommandsEmpty'];
			}

			$edit_contents_td = $messages['TableHeaderEditContents'];
			$edit_configuration_td = $messages['TableHeaderEditConfiguration'];
			if(file_exists(DotCoreFeatureBLL::GetFeatureLocalRootFolder($feature).'lang.php'))
			{
				$edit_contents_td = '<a href="'.$this->GetLink(array('edit_contents'=>$feature->getFeatureID())).'">'.$edit_contents_td.'</a>';
			}
			if($this->GetFeatureConfiguration($feature))
			{
				$edit_configuration_td = '<a href="'.$this->GetLink(array('edit_configuration'=>$feature->getFeatureID())).'">'.$edit_configuration_td.'</a>';
			}

			$result .= '<tr '.$class.'>';
			$result .= '<td>' . $feature->getFeatureName() . '</td>';
			$result .= '<td>' . $feature_commands . '</td>';
			$result .= '<td class="command">'.$edit_contents_td.'</td>';
			$result .= '<td class="command" style="width: 90px;">'.$edit_configuration_td.'</td>';
			$result .= '</tr>';
		}

		$result .= '
				</table>';

		return $result;
	}

	protected function GetFeatureConfiguration(DotCoreFeatureRecord $feature) {
		if(!key_exists($feature->getFeatureClass(), self::$configuration_cache)) {
			if(file_exists(DotCoreFeatureBLL::GetFeatureLocalRootFolder($feature).'configuration.php')) {
				self::$configuration_cache[$feature->getFeatureClass()] = new DotCoreConfiguration(DotCoreFeatureBLL::GetFeatureLocalRootFolder($feature).'configuration.php');
			}
			else {
				self::$configuration_cache[$feature->getFeatureClass()] = NULL;
			}
		}
		return self::$configuration_cache[$feature->getFeatureClass()];
	}

	/**
	 *
	 * @param DotCoreMessages $messages
	 * @return MessagesFileEditor
	 */
	protected function GetFeatureMessagesEditor(DotCoreMessages $messages)
	{
		if($this->messages_editor == NULL) {
			$this->messages_editor = new MessagesFileEditor($messages, $_SERVER['REQUEST_URI']);
			$this->messages_editor->GenerateForm();
		}
		return $this->messages_editor;
	}

	/**
	 *
	 * @param DotCoreConfiguration $configuration
	 * @return ConfigurationFileEditor
	 */
	protected function GetFeatureConfigurationEditor(DotCoreConfiguration $configuration, DotCoreMessages $messages)
	{
		if($this->configuration_editor == NULL) {
			$this->configuration_editor = new ConfigurationFileEditor($configuration, $messages, $_SERVER['REQUEST_URI']);
			$this->configuration_editor->GenerateForm();
		}
		return $this->configuration_editor;
	}
	
}

?>
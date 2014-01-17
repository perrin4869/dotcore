<?php

class NewsEditor extends DotCoreProgram
{
	public function __construct(DotCoreProgramRecord $program_record)
	{
		parent::__construct($program_record);

		// Check for permissions
		$admin = DotCoreOS::GetInstance()->GetAdmin();
		if(!DotCoreAdminBLL::IsInRole($admin, DotCoreConfig::$ROLE_NEWS))
		{
			throw new PermissionDeniedException(DotCoreConfig::$ROLE_NEWS);
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

	const LANGUAGE_COOKIE = 'news_editor_language';

	/**
	 * Form generator used to edit news by this editor
	 * @var DotCoreFormGenerator
	 */
	private $news_form_generator;

	private $mode;

	private $deleted = FALSE;
	private $inserted = FALSE;
	private $edited = FALSE;

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
						contentEl: "news_'.$language->getLanguageCode().'",
						title: "'.$messages[$language->getLanguageCode()].'",
						data: { lang_id: '.$language->getLanguageID().' }
					}';
			}

			$result .= '
				<link href="'.DotCoreConfig::$STYLES_URL.'extjs_tabs/tabs_styles.css" type="text/css" rel="stylesheet" />
				<!--[if lte IE 7]>
				<link href="'.DotCoreConfig::$STYLES_URL.'extjs_tabs/tabs_styles_iefix.css" type="text/css" rel="stylesheet" />
				<![endif]-->

				<script type="text/javascript" src="'.$this->GetFolderPath() . '/news_editor.js"></script>
				<script type="text/javascript">
				//<![CDATA[
				var activeTab = '.$active_tab.';
				var tabItems = ['.$tab_items.'];
				//]]>
				</script>';
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
			return $messages['AdminTitleAddNews'];
		}
		else
		{
			return $messages['AdminTitleEditNews'];
		}
	}
	
	/**
	 * Gets the interface to use for the configuration of this feature
	 * @return string
	 */
	public function GetContent()
	{
		$messages = $this->GetMessages();
		$result = "";

		if($this->deleted)
		{
			$result .= '<p class="feedback">' . $messages['MessageNewsDeletedSuccessfully'] . '</p>';
		}
		if($this->inserted)
		{
			$result .= '<p class="feedback">' . $messages['MessageNewsAddedSuccessfully'] . '</p>';
		}
		if($this->edited)
		{
			$result .= '<p class="feedback">' . $messages['MessageNewsEditedSuccessfully'] . '</p>';
		}
		if($this->HasErrors())
		{
			$result .= $this->GetErrorsMarkup();
		}

		if($this->mode == self::MODE_ADD || $this->mode == self::MODE_EDIT)
		{
			// Show form
			$result .= $this->news_form_generator->GetForm()->__toString();
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

		// Prepare the news BLL
		$news_bll = new DotCoreNewsBLL();

		// Determine what's the current language
		$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
		$current_language = $this->GetCurrentEditingLanguage(self::LANGUAGE_COOKIE);

		if($this->mode == self::MODE_ADD || $this->mode == self::MODE_EDIT)
		{
			$news_fields = array(
					$news_bll->getFieldDate(),
					$news_bll->getFieldTitle(),
					$news_bll->getFieldShortContent(),
					$news_bll->getFieldContent()
				);

			if($this->mode == self::MODE_ADD)
			{
				$news = $news_bll->GetNewRecord();
				$news->setNewsLanguageID($current_language->getLanguageID());
			}
			else
			{
				$news = $news_bll
					->Fields($news_fields)
					->ByNewsID($_REQUEST['edit'])
					->SelectFirstOrNull();
			}

			if($news != NULL)
			{
				$form_parameters = array();

				if($news->IsEmpty()) {
					$form_parameters['add'] = $news->getNewsID();
					$submit_label = $messages['LabelAdd'];
				} else {
					$form_parameters['edit'] = $news->getNewsID();
					$submit_label = $messages['LabelUpdate'];
				}

				$this->news_form_generator = new DotCoreFormGenerator('news_form', $this->GetLink($form_parameters));
				$this->news_form_generator
					->SetMessages($messages)
					->SetRecord($news)
					->SetFields($news_fields)
					->Generate();
				$this->news_form_generator->GenerateSubmitButton($submit_label);

				$form = $this->news_form_generator->GetForm();
				if($form->WasSubmitted())
				{
					$news_bll->BeginTransaction($news);
					$this->news_form_generator->ProcessForm();
					$this->news_form_generator->Validate();
					
					if(
						$this->news_form_generator->HasErrors() == FALSE &&
						$this->news_form_generator->TrySave($news_bll)
						)
					{
						$news_bll->CommitTransaction($news);
					}

					if($news_bll->TransactionCommitted())
					{
						if($this->mode == self::MODE_ADD) {
							$this->inserted = TRUE;
						}
						else {
							$this->edited = TRUE;
						}

						$this->mode = self::MODE_NORMAL;
					}
					else
					{
						$news_bll->Rollback($news);
					}
				}
			}
			else
			{
				$this->AddError($messages['ErrorNotFound']);
			}
		}
		elseif($this->mode == self::MODE_NORMAL)
		{
			if(isset($_REQUEST['delete']))
			{
				$news = $news_bll
					->Fields(
						array(
							$news_bll->getFieldNewsID()
						)
					)
					->ByNewsID($_REQUEST['delete'])
					->SelectFirstOrNull();

				if($news != NULL)
				{
					try {
						$news_bll->Delete($news);
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
	
	public function GetTable()
	{
		$messages = $this->GetMessages();
		
		$result = '';

		$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();

		$news_bll = new DotCoreNewsBLL();
		$news_order = new DotCoreDALSelectionOrder();
		$news_order->AddOrderUnit(
			new DotCoreFieldSelectionOrder($news_bll->getFieldLanguageID(), DotCoreFieldSelectionOrder::DIRECTION_ASC)
		);
		$news = $news_bll
			->Fields(
				array(
					$news_bll->getFieldTitle(),
					$news_bll->getFieldShortContent(),
					$news_bll->getFieldDate()
				)
			)
			->Order($news_order)
			->Select();

		$count_news = count($news);

		$result .='
			<div id="news_panel">';
			$i = 0;
			foreach($languages as $lang_id => $language)
			{
				$result .= '
					<div class="x-hide-display" id="news_'.$language->getLanguageCode().'">';
			
				$result .= '
					<table cellpadding="5" cellspacing="0" class="datagrid">
					<thead>
						<th>'.$messages[DotCoreNewsDAL::NEWS_TITLE.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
						<th style="width: 100px;">'.$messages[DotCoreNewsDAL::NEWS_DATE.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
						<th class="command">'.$messages['TableHeaderEdit'].'</th>
						<th class="command">'.$messages['TableHeaderDelete'].'</th>
					</thead>';

				for($j = 0; $i < $count_news && $news[$i]->getNewsLanguageID() == $lang_id; $j++, $i++)
				{
					$class = ($j % 2 == 1) ? '' : 'class="alternating"';
					$curr_news = $news[$i];
					$news_id = $curr_news->getNewsID();

					$result .= '
							<tr '.$class.'>
								<td>' . $curr_news->getNewsTitle() . '</td>
								<td>' . date('d-m-Y',$curr_news->getNewsDate()) . '</td>
								<td class="command"><a href="'.$this->GetLink(array('edit'=>$news_id)).'">'.$messages['TableActionsEdit'].'</a></td>
								<td class="command"><a href="'.$this->GetLink(array('delete'=>$news_id)).'" onclick="return confirm(\''.$messages['MessageNewsConfirmDeletion'].'\');">'.$messages['TableActionsDelete'].'</a></td>
							</tr>';
				}

				$result .= '
						</table>
						<div class="sub_menu">
						<a href="'.$this->GetLink(array('add'=>1, 'change_language'=>$lang_id)).'">' . $messages['AdminTitleAddNews'] . '</a>
						</div>
					</div>';
				}
				$result .= '
			</div>';
		
		return $result;
	}

}

?>
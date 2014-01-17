<?php

class LinksEditor extends DotCoreProgram
{
	public function __construct(DotCoreProgramRecord $program_record)
	{
		parent::__construct($program_record);

		// Check for permissions
		$admin = DotCoreOS::GetInstance()->GetAdmin();
		if(!DotCoreAdminBLL::IsInRole($admin, DotCoreConfig::$ROLE_LINKS))
		{
			throw new PermissionDeniedException(DotCoreConfig::$ROLE_LINKS);
		}

		// Check for permissions
		$admin = DotCoreOS::GetInstance()->GetAdmin();
		if(!DotCoreAdminBLL::IsInRole($admin, DotCoreConfig::$ROLE_LINKS))
		{
			throw new PermissionDeniedException(DotCoreConfig::$ROLE_LINKS);
		}

		if(isset($_REQUEST['edit']))
		{
			$this->mode = self::MODE_EDIT_LINK;
		}
		elseif(isset($_REQUEST['add']))
		{
			$this->mode = self::MODE_ADD_LINK;
		}
		else
		{
			$this->mode = self::MODE_DEFAULT;
		}
	}

	/*
	 *
	 * Properties:
	 *
	 */

	const MODE_DEFAULT = 1;
	const MODE_EDIT_LINK = 2;
	const MODE_ADD_LINK = 5;

	const LANGUAGE_COOKIE = 'links_editor_language';

	/**
	 * Holds the current mode of this feature
	 * @var string
	 */
	private $mode;

	/**
	 * Holds the link being worked on
	 * @var DotCoreLinkRecord
	 */
	private $link = NULL;

	/**
	 * Generator used to generate $links_form
	 * @var DotCoreFormGenerator
	 */
	private $links_form_generator = NULL;

	private $edited = FALSE;
	private $deleted = FALSE;
	private $inserted = FALSE;

	/**
	 * Gets the title for this program
	 * @return string
	 */
	public function GetTitle()
	{
		$messages = $this->GetMessages();

		if($this->mode == self::MODE_EDIT_LINK)
		{
			return $messages['TitleEditLink'];
		}
		elseif($this->mode == self::MODE_ADD_LINK)
		{
			return $messages['TitleAddLink'];
		}
		else
		{
			return $messages['TitleLinksEditor'];
		}
	}

	public function GetHeaderContent()
	{
		$result = '';
		if($this->mode == self::MODE_DEFAULT)
		{
			$current_language = $this->GetCurrentEditingLanguage(self::LANGUAGE_COOKIE);
			$messages = $this->GetMessages();
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
						contentEl: "links_'.$language->getLanguageCode().'",
						title: "'.$messages[$language->getLanguageCode()].'",
						data: { lang_id: '.$language->getLanguageID().' },
						listeners: {activate: LinksEditor.OnLinkLanguageTabActivate}
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
				<script type="text/javascript" src="'.$this->GetProgramUrl().'links_editor.js"></script>
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

		if($this->HasErrors())
		{
			$result .= $this->GetErrorsMarkup();
		}

		if($this->inserted)
		{
			$result .= '<p class="feedback">'.$messages['MessageSuccessfulAddition'].'</p>';
		}
		if($this->edited)
		{
			$result .= '<p class="feedback">'.$messages['MessageSuccessfulChanges'].'</p>';
		}
		if($this->deleted)
		{
			$result .= '<p class="feedback">'.$messages['MessageSuccessfulDeletion'].'</p>';
		}

		if($this->mode == self::MODE_ADD_LINK || $this->mode == self::MODE_EDIT_LINK)
		{
			$result .= $this->links_form_generator->GetForm()->__toString();
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

		if($this->mode == self::MODE_ADD_LINK || $this->mode == self::MODE_EDIT_LINK)
		{
			$links_bll = new DotCoreLinkBLL();
			$links_fields = array(
				$links_bll->getFieldUrl(),
				$links_bll->getFieldTitle(),
				$links_bll->getFieldDescription(),
				$links_bll->getFieldLogo()
			);
			$lang_id = $current_language->getLanguageID();

			if($this->mode == self::MODE_ADD_LINK)
			{
				$this->link = $links_bll->GetNewRecord();
				$this->link->setLinkLanguageID($lang_id);
				$this->link->setLinkOrder($links_bll->ByLanguageID($lang_id)->GetCount() + 1);
			}
			else
			{
				$this->link = $links_bll
					->Fields($links_fields)
					->ByLinkID($_REQUEST['edit'])
					->SelectFirstOrNull();
			}

			if($this->link != NULL)
			{
				$form_parameters = array();

				if($this->link->IsEmpty()) {
					$form_parameters['add'] = $this->link->getLinkID();
					$submit_label = $messages['LabelAdd'];
				} else {
					$form_parameters['edit'] = $this->link->getLinkID();
					$submit_label = $messages['LabelUpdate'];
				}

				$form_generator = new DotCoreFormGenerator('links_form', $this->GetLink($form_parameters));
				$form_generator
					->SetFields($links_fields)
					->SetMessages($messages)
					->SetRecord($this->link)
					->Generate();
				$form_generator->GenerateSubmitButton($submit_label);

				$this->links_form_generator = $form_generator;
				$form = $this->links_form_generator->GetForm();
				if($form->WasSubmitted())
				{
					$links_bll->BeginTransaction($this->links);
					$this->links_form_generator->ProcessForm();
					$this->links_form_generator->Validate();
					if(
						$this->links_form_generator->HasErrors() == FALSE &&
						$this->links_form_generator->TrySave($links_bll)
						)
					{
						$links_bll->CommitTransaction($this->link);
					}

					if($links_bll->TransactionCommitted())
					{
						if($this->mode == self::MODE_ADD_LINK)
						{
							$this->inserted = TRUE;
						}
						else
						{
							$this->edited = TRUE;
						}

						$this->mode = self::MODE_DEFAULT;
					}
					else
					{
						$links_bll->Rollback($this->link);
					}
				}
			}
			else
			{
				$this->AddError($messages['MessageLinkNotFound']);
			}

		}
		else
		{
			if(isset($_REQUEST['delete']))
			{
				$links_bll = new DotCoreLinkBLL();
				$this->link = $links_bll
					->Fields(
						array(
							$links_bll->getFieldLinkID(),
							$links_bll->getFieldLogo() // Needs to be loaded so that the logo will be deleted along with the record
						)
					)
					->ByLinkID($_REQUEST['delete'])
					->SelectFirstOrNull();

				if($this->link != NULL)
				{
					try
					{
						$links_bll->Delete($this->link);
						$this->deleted = TRUE;
					}
					catch(Exception $ex)
					{
						$this->AddError($ex->getMessage());
					}
				}
				else
				{
					$this->AddError($messages['MessageLinkNotFound']);
				}
			}
		}
	}

	public function GetTable()
	{
		$messages = $this->GetMessages();
		$program_name = $this->GetType();
		$result = '';

		$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();

		$links_bll = new DotCoreLinkBLL();
		$links = $links_bll
			->Fields(
				array(
					$links_bll->getFieldTitle(),
					$links_bll->getFieldDescription(),
					$links_bll->getFieldLanguageID(),
					$links_bll->getFieldLogo(),
					$links_bll->getFieldOrder(),
					$links_bll->getFieldUrl()
				)
			)
			->OrderedByLanguageAndOrder()
			->Select();
		$count_links = count($links);

		$result .='
			<div id="links_panel">';
			$i = 0;
			foreach($languages as $lang_id => $language)
			{
				$result .= '
					<div class="x-hide-display" id="links_'.$language->getLanguageCode().'">';

				$result .= '
					<table cellpadding="5" cellspacing="0" class="datagrid">
					<thead>
						<th>'.$messages['TableHeaderLinkTitle'].'</th>
						<th>'.$messages['TableHeaderLinkLogo'].'</th>
						<th class="command">'.$messages['TableHeaderMove'].'</th>
						<th class="command">'.$messages['TableHeaderEdit'].'</th>
						<th class="command">'.$messages['TableHeaderDelete'].'</th>
					</thead>';

				for($j = 0; $i < $count_links && $links[$i]->getLinkLanguageID() == $lang_id; $j++, $i++)
				{
					$class = ($j % 2 == 1) ? '' : 'class="alternating"';
					$curr_link = $links[$i];
					$link_id = $curr_link->getLinkID();

					$result .= '
						<tr '.$class.' dotcore:link_id="'.$link_id.'">
							<td>' . $curr_link->getLinkTitle() . '</td>
							<td class="image"><img alt="" src="'.$curr_link->getLinkLogoPath().'" /></td>
							<td class="command">
								<img alt="'.$messages['MoveUp'].'" src="'.$this->GetProgramUrl().'/up.gif" name="up_arrow" />
								<img alt="'.$messages['MoveDown'].'" src="'.$this->GetProgramUrl().'/down.gif" name="down_arrow" />
							</td>
							<td class="command"><a href="'.$this->GetLink(array('edit'=>$link_id)).'">'.$messages['TableActionsEdit'].'</a></td>
							<td class="command"><a href="'.$this->GetLink(array('delete'=>$link_id)).'" onclick="return confirm(\''.$messages['MessageLinkDeletionConfirm'].'\');">'.$messages['TableActionsDelete'].'</a></td>
						</tr>';
				}

				$result .= '
						</table>
						<div class="sub_menu">
						<a href="'.$this->GetLink(array('add'=>1, 'change_language'=>$lang_id)).'">' . $messages['TitleAddLink'] . '</a>
						</div>
					</div>';
				}
				$result .= '
			</div>';

		return $result;
	}

}

?>
<?php

class UsersManager extends DotCoreProgram
{
	public function __construct(DotCoreProgramRecord $program_record)
	{
		parent::__construct($program_record);

		if(isset($_REQUEST['add']))
		{
			$this->RequireAdminPermissions();
			$this->mode = self::MODE_ADD;
		}
		elseif(isset($_REQUEST['edit']))
		{
			$this->RequireAdminPermissions();
			$this->mode = self::MODE_EDIT;
		}
		elseif(isset($_REQUEST['edit_profile']))
		{
			$this->mode = self::MODE_EDIT_PROFILE;
		}
		else
		{
			$this->RequireAdminPermissions();
			$this->mode = self::MODE_NORMAL;
		}
	}
	
	/*
	 *
	 * Properties:
	 *
	 */

	const MODE_ADD = 1;
	const MODE_EDIT = 2;
	const MODE_NORMAL = 3;
	const MODE_EDIT_PROFILE = 4;

	/**
	 * Holds the form used for editing admins
	 * @var DotCoreFormGenerator
	 */
	private $admin_form_generator = NULL;

	private $deleted = FALSE;
	private $edited = FALSE;
	private $inserted = FALSE;
	private $edited_profile = FALSE;

	private $mode;

	protected function RequireAdminPermissions() {
		if(!$this->HasAdminPermissions())
		{
			throw new PermissionDeniedException(DotCoreConfig::$ROLE_ADMINS);
		}
	}

	protected function HasAdminPermissions() {
		$admin = DotCoreOS::GetInstance()->GetAdmin();
		return DotCoreAdminBLL::IsInRole($admin, DotCoreConfig::$ROLE_ADMINS);
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
			return $messages['AdminTitleAddAdmins'];
		}
		elseif($this->mode == self::MODE_EDIT)
		{
			return $messages['AdminTitleEditAdmins'];
		}
		elseif($this->mode == self::MODE_EDIT_PROFILE)
		{
			return $messages['AdminTitleEditProfile'];
		}
		else
		{
			return $messages['AdminTitleMainAdmins'];
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

		if($this->inserted)
		{
			$result .= '<p class="feedback">' . $messages['MessageUserAdditionSucceed'] . '</p>';
		}
		elseif($this->edited)
		{
			$result .= '<p class="feedback">' . $messages['MessageSuccessfulChanges'] . '</p>';
		}
		elseif($this->edited_profile)
		{
			$result .= '<p class="feedback">' . $messages['MessageSuccessfulChanges'] . '</p>';
		}

		if($this->HasErrors())
		{
			$result .= $this->GetErrorsMarkup();
		}

		if($this->mode == self::MODE_ADD || $this->mode == self::MODE_EDIT || $this->mode == self::MODE_EDIT_PROFILE)
		{
			$result .= $this->admin_form_generator->GetForm()->__toString();
		}
		else
		{
			if($this->deleted)
			{
				$result .= '<p class="feedback">' . $messages['MessageUserDeletionSucceed'] . '</p>';
			}

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

		if(
			$this->mode == self::MODE_ADD ||
			$this->mode == self::MODE_EDIT ||
			$this->mode == self::MODE_EDIT_PROFILE)
		{
			$admin_bll = new DotCoreAdminBLL();
			$current_admin = DotCoreOS::GetInstance()->GetAdmin();

			$users_path = DotCoreAdminDAL::USER_ADMIN_LINK . '.';
			$roles_path = DotCoreRoleDAL::ADMIN_ROLES_LINK . '.';
			$roles_multilang_path = $roles_path . DotCoreRoleMultilangDAL::ROLE_MULTILANG_LINK . '.';

			$admin_bll->Fields(
				array(
					$admin_bll->getFieldAdminID(),
					$users_path.DotCoreUserDAL::USER_EMAIL,
					$users_path.DotCoreUserDAL::USER_FIRST_NAME,
					$users_path.DotCoreUserDAL::USER_LAST_NAME,
					$users_path.DotCoreUserDAL::USER_PASSWORD,
					$users_path.DotCoreUserDAL::USER_PHONE,
					$users_path.DotCoreUserDAL::USER_NAME,
					$roles_path.DotCoreRoleDAL::ROLES_ID
				)
			);
			
			$user_bll = new DotCoreUserBLL();

			if($this->mode == self::MODE_EDIT) {
				if($_REQUEST['edit'] == $current_admin->getAdminID())
				{
					$admin = $current_admin;
				}
				else
				{
					$admin = $admin_bll->ByAdminID($_REQUEST['edit'])->SelectFirstOrNull();
				}
			}
			elseif($this->mode == self::MODE_EDIT_PROFILE) {
				$admin = $current_admin;
				// Use the current admin so the changes are automatic
				// We can use it because it has all the fields and roles we need loaded
			}
			elseif($this->mode == self::MODE_ADD) {
				$admin = $admin_bll->GetNewRecord();
				DotCoreAdminBLL::SetUser($admin, $user_bll->GetNewRecord());
			}

			if($admin != NULL)
			{

				if($this->mode == self::MODE_ADD) {
					$link_params = array('add'=>'');
					$label_submit_button = $messages['LabelUserInsertUser'];
				}
				elseif($this->mode == self::MODE_EDIT_PROFILE) {
					$link_params = array('edit_profile'=>$_REQUEST['edit_profile']);
					$label_submit_button = $messages['LabelUpdate'];
				}
				else {
					$link_params = array('edit'=>$admin->getAdminID());
					$label_submit_button = $messages['LabelUpdate'];
				}

				$this->admin_form_generator = new DotCoreFormGenerator('admin_form', $this->GetLink($link_params));

				$users_path = new DotCoreDALPath(array(DotCoreAdminDAL::USER_ADMIN_LINK));
				$admin_fields = array(
					new DotCoreDALFieldPath($user_bll->getFieldUserName(), $users_path),
					new DotCoreDALFieldPath($user_bll->getFieldEmail(), $users_path),
					new DotCoreDALFieldPath($user_bll->getFieldPassword(), $users_path),
					new DotCoreDALFieldPath($user_bll->getFieldFirstName(), $users_path),
					new DotCoreDALFieldPath($user_bll->getFieldLastName(), $users_path),
					new DotCoreDALFieldPath($user_bll->getFieldPhone(), $users_path)
				);

				$this->admin_form_generator
					->SetMessages($messages)
					->SetRecord($admin)
					->SetFields($admin_fields)
					->Generate();
				$form = $this->admin_form_generator->GetForm();

				if($this->HasAdminPermissions()) {
					$curr_lang_id = DotCoreOS::GetInstance()->GetLanguage()->getLanguageID();
					$roles_bll = new DotCoreRoleBLL();
					$roles_multilang_bll = new DotCoreRoleMultilangBLL();
					$roles_multilang_path = DotCoreRoleMultilangDAL::ROLE_MULTILANG_LINK . '.';
					$roles = $roles_bll
						->Fields(
							array(
								$roles_bll->getFieldRoleID(),
								$roles_multilang_path . DotCoreRoleMultilangDAL::ROLE_NAME
							)
						)
						->AddLink(
							new DotCoreMultiLanguageLink(
								DotCoreDAL::GetRelationship(DotCoreRoleMultilangDAL::ROLE_MULTILANG_LINK),
								$roles_multilang_bll->getFieldLanguageID(),
								$curr_lang_id)
						)
						->GetRolesIDDictionary();
						
					$roles_dictionary = array();
					foreach($roles as $role)
					{
						$multilang_roles = DotCoreRoleBLL::GetRolesMultilanguageProperties($role);
						if(count($multilang_roles) > 0) {
							$role_name = $multilang_roles[0]->getRoleName();
						}
						else {
							$role_name = $role->getRoleDesc();
						}
						$roles_dictionary[$role->getRoleID()] = $role_name;
					}

					$current_roles = DotCoreAdminBLL::GetRoles($admin);
					$count_roles = count($current_roles);
					$current_roles_dictionary = array();
					for($i = 0; $i < $count_roles; $i++) {
						$current_roles_dictionary[$current_roles[$i]->getRoleID()] = TRUE;
					}

					$roles_checkboxes = new DotCoreMultipleCheckBoxFormElement('roles_checkboxes', $roles_dictionary);
					$roles_checkboxes->SetDefaultValue($current_roles_dictionary);
					$form->AddFormElement($roles_checkboxes, $messages['LabelRoles']);
				}

				$this->admin_form_generator->GenerateSubmitButton($label_submit_button);

				if($form->WasSubmitted())
				{
					// OK, finished generating, get to processing
					$admin_bll->BeginTransaction($admin);
					$messages = $this->GetMessages();

					$this->admin_form_generator->ProcessForm();
					if($this->HasAdminPermissions()) {
						$new_roles_dictionary = array();
						$new_roles_value = $form->GetFormElement('roles_checkboxes')->GetValue();
						foreach($new_roles_value as $selected_role=>$val)
						{
							$new_roles_dictionary[$selected_role] = $roles[$selected_role];
						}
						DotCoreAdminBLL::ExchangeRoles($admin, $new_roles_dictionary);
					}
					$this->admin_form_generator->Validate();
					
					if(
						$this->admin_form_generator->HasErrors() == FALSE &&
						$this->admin_form_generator->TrySave($admin_bll)
						)
					{
						$admin_bll->CommitTransaction($admin);
					}

					if($admin_bll->TransactionCommitted())
					{
						if($this->mode == self::MODE_ADD)
						{
							$this->inserted = TRUE;
						}
						elseif($this->mode == self::MODE_EDIT)
						{
							$this->edited = TRUE;
						}
						elseif($this->mode == self::MODE_EDIT_PROFILE)
						{
							$this->edited_profile = TRUE;
						}

						if($this->HasAdminPermissions()) {
							$this->mode = self::MODE_NORMAL;
						}
					}
					else {
						$admin_bll->Rollback($admin);
					}
				}
			}
			else
			{
				$this->AddError($messages['NoUserFound']);
			}
		}
		else //if($this->mode == self::MODE_NORMAL)
		{
			if(isset($_REQUEST['delete']))
			{
				$user_bll = new DotCoreUserBLL();
				$user = $user_bll
					->Fields(
						array(
							$user_bll->getFieldUserID()
						)
					)
					->ByUserID($_REQUEST['delete'])
					->SelectFirstOrNull();

				if($user != NULL)
				{
					try {
						$user_bll->Delete($user);
						$this->deleted = TRUE;
					}
					catch(Exception $ex)
					{
						$this->AddError($ex->getMessage());
					}
				}
				else
				{
					$this->AddError($messages['NoUserFound']);
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
			<th>'.$messages[DotCoreUserDAL::USER_NAME.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
			<th>'.$messages[DotCoreUserDAL::USER_FIRST_NAME.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
			<th>'.$messages[DotCoreUserDAL::USER_LAST_NAME.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
			<th>'.$messages[DotCoreUserDAL::USER_EMAIL.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
			<th>'.$messages[DotCoreUserDAL::USER_PHONE.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
			<th>'.$messages[DotCoreUserDAL::USER_DATE_CREATED.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
			<th>'.$messages[DotCoreUserDAL::USER_LAST_LOGIN.DotCoreFormGenerator::MESSAGE_LABEL].'</th>
			<th class="command">'.$messages['TableHeaderEdit'].'</th>
			<th class="command">'.$messages['TableHeaderDelete'].'</th>
		</thead>';

		$admin_bll = new DotCoreAdminBLL();
		$user_path = DotCoreAdminDAL::USER_ADMIN_LINK . '.';
		$admins = $admin_bll
			->Fields(
				array(
					$user_path . DotCoreUserDAL::USER_NAME,
					$user_path . DotCoreUserDAL::USER_FIRST_NAME,
					$user_path . DotCoreUserDAL::USER_LAST_NAME,
					$user_path . DotCoreUserDAL::USER_EMAIL,
					$user_path . DotCoreUserDAL::USER_PHONE,
					$user_path . DotCoreUserDAL::USER_DATE_CREATED,
					$user_path . DotCoreUserDAL::USER_LAST_LOGIN
				)
			)
			->Select();

		$count_admins = count($admins);
		for($i = 0; $i < $count_admins; $i++)
		{
			$admin = $admins[$i];
			$user = DotCoreAdminBLL::GetUser($admin);
			$user_id = $user->getUserID();
			$class = ($i % 2 != 0) ? "" : 'class="alternating"';
			$result .= '
				<tr '.$class.'>
					<td>' . $user->getUserName() . '</td>
					<td>' . $user->getUserFirstName() . '</td>
					<td>' . $user->getUserLastName() . '</td>
					<td>' . $user->getUserEmail() . '</td>
					<td>' . $user->getUserPhone() . '</td>
					<td>' . date(DotCoreConfig::$DATE_FORMAT, $user->getUserDateCreated()) . '</td>
					<td>' . date(DotCoreConfig::$DATE_TIME_FORMAT, $user->getUserLastLogin()) . '</td>
					<td class="command"><a href="'.$this->GetLink(array('edit'=>$user_id)).'"><img alt="'.$messages['TableActionsEdit'].'" src="/admin/images/edit.gif" /></a></td>
					<td class="command"><a href="'.$this->GetLink(array('delete'=>$user_id)).'" onclick="return confirm(\''.$messages['MessageConfirmUserDeletion'].'\');"><img alt="'.$messages['TableActionsDelete'].'" src="/admin/images/delete.gif" /></a></td>
				</tr>';
		}

		$result .= '
		</table>';

		$result .= '<div class="sub_menu">';
		$result .= '<a href="'.$this->GetLink(array('add'=>'1')).'">' . $messages['AdminTitleAddAdmins'] . '</a>';
		$result .= '</div>';

		return $result;
	}
	
}

?>
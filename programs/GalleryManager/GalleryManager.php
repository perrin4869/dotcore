<?php

class GalleryManager extends DotCoreProgram
{
	public function __construct(DotCoreProgramRecord $program_record)
	{
		parent::__construct($program_record);

		// Check for permissions
		$admin = DotCoreOS::GetInstance()->GetAdmin();
		if(!DotCoreAdminBLL::IsInRole($admin, DotCoreConfig::$ROLE_GALLERY))
		{
			throw new PermissionDeniedException(DotCoreConfig::$ROLE_GALLERY);
		}

		if(isset($_REQUEST['add']))
		{
			$this->mode = self::GALLERY_MANAGER_MODE_ADD;
		}
		elseif(isset($_REQUEST['edit']))
		{
			$this->mode = self::GALLERY_MANAGER_MODE_EDIT;
		}
		elseif(isset($_REQUEST['manage_images']))
		{
			$this->mode = self::GALLERY_MANAGER_MODE_MANAGE_IMAGES;
		}
		elseif(isset($_REQUEST['add_image'])) {
			$this->mode = self::GALLERY_MANAGER_MODE_ADD_IMAGES;
		}
		elseif(isset($_REQUEST['edit_image'])) {
			$this->mode = self::GALLERY_MANAGER_MODE_EDIT_IMAGES;
		}
		else
		{
			$this->mode = self::GALLERY_MANAGER_MODE_NORMAL;
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
	const GALLERY_MANAGER_MODE_NORMAL = 1;
	/**
	 * Shows the form needed for adding a gallery
	 * @var int
	 */
	const GALLERY_MANAGER_MODE_ADD = 2;
	/**
	 * Shows the form needed for editing the gallery
	 * @var int
	 */
	const GALLERY_MANAGER_MODE_EDIT = 3;
	/**
	 * Shows the file manager for the gallery
	 * @var int
	 */
	const GALLERY_MANAGER_MODE_MANAGE_IMAGES = 4;
	/**
	 * Shows an edit form for an image from a gallery
	 * @var int
	 */
	const GALLERY_MANAGER_MODE_EDIT_IMAGES = 5;
	/**
	 * Shows a form for the addition of an image to a gallery
	 * @var int
	 */
	const GALLERY_MANAGER_MODE_ADD_IMAGES = 6;

	private $mode;

	/**
	 * Holds the gallery currently worked on
	 * @var DotCoreGalleryRecord 
	 */
	private $gallery = NULL;

	/**
	 * Holds the gallery image currently worked on
	 * @var DotCoreGalleryImage
	 */
	private $gallery_image = NULL;

	/**
	 * The form generator for the editing of galleries
	 * @var DotCoreFormGenerator
	 */
	private $gallery_form_generator = NULL;

	/**
	 * The form generator for the editing of gallery images
	 * @var DotCoreFormGenerator
	 */
	private $gallery_image_form_generator = NULL;


	private $inserted = FALSE;
	private $edited = FALSE;
	private $deleted = FALSE;

	private $image_deleted = FALSE;
	private $image_edited = FALSE;
	private $image_inserted = FALSE;

	/*
	 * BLL Accessors
	 */
	
	/**
	 * Gets the title for this program
	 * @return string
	 */
	public function GetTitle()
	{
		$messages = $this->GetMessages();
		$title = '';

		if($this->mode == self::GALLERY_MANAGER_MODE_ADD)
		{
			$title = $messages['TitleAddGallery'];
		}
		else
		{
			$title = $messages['TitleEditGallery'];
		}

		return $title;
	}
	
	public function GetHeaderContent()
	{
		$result = '';

		if($this->mode == self::GALLERY_MANAGER_MODE_MANAGE_IMAGES) {
			$result .= '
			<script type="text/javascript" src="'.$this->GetFolderPath().'/gallery_images_editor.js"></script>
			';
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

		if($this->mode == self::GALLERY_MANAGER_MODE_ADD || $this->mode == self::GALLERY_MANAGER_MODE_EDIT)
		{
			$result .= $this->gallery_form_generator->GetForm()->__toString();
		}
		elseif($this->mode == self::GALLERY_MANAGER_MODE_ADD_IMAGES || $this->mode == self::GALLERY_MANAGER_MODE_EDIT_IMAGES)
		{
			$result .= $this->gallery_image_form_generator->GetForm()->__toString();
		}
		elseif($this->mode == self::GALLERY_MANAGER_MODE_MANAGE_IMAGES && $this->gallery != NULL)
		{
			$path = DotCoreGalleryBLL::GetGalleryRootPath($this->gallery);
			$result .= '<h2>'.$this->gallery->getGalleryName().'</h2>';
			$file_manager = new FileManager($path);
			$result .= $file_manager->create();

			if($this->image_deleted) {
				$result .= '<p class="feedback">'.$messages['MessageSuccessfulImageDeletion'].'</p>';
			}
			if($this->image_inserted) {
				$result .= '<p class="feedback">'.$messages['MessageSuccessfulImageInsertion'].'</p>';
			}
			if($this->image_edited) {
				$result .= '<p class="feedback">'.$messages['MessageSuccessfulChanges'].'</p>';
			}

			$result .= '<form method="post" action="'.$this->GetLink(array('sync_images'=>1,'manage_images'=>$this->gallery->getGalleryID())).'">';
			$result .= '<button id="synchronize-gallery-button">'.$messages['LabelSyncButton'].'</button>';
			$result .= '</form>';
			$result .= $this->GetGalleryImagesTable($this->gallery);
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
		
		if($this->mode == self::GALLERY_MANAGER_MODE_ADD || $this->mode == self::GALLERY_MANAGER_MODE_EDIT)
		{
			$gallery_bll = new DotCoreGalleryBLL();
			$gallery_fields = array(
				$gallery_bll->getFieldGalleryName(),
				$gallery_bll->getFieldGalleryFolder()
			);

			if($this->mode == self::GALLERY_MANAGER_MODE_ADD)
			{
				$this->gallery = $gallery_bll->GetNewRecord();
			}
			else
			{
				$this->gallery = $gallery_bll
					->Fields(
						array(
							$gallery_bll->getFieldGalleryName(),
							$gallery_bll->getFieldGalleryFolder()
						)
					)
					->ByGalleryID($_REQUEST['edit'])
					->SelectFirstOrNull();
			}

			if($this->gallery != NULL)
			{
				if($this->mode == self::GALLERY_MANAGER_MODE_ADD) {
					$link_params = array('add'=>'');
					$label_submit_button = $messages['LabelAdd'];
				}
				else {
					$link_params = array('edit'=>$this->gallery->getGalleryID());
					$label_submit_button = $messages['LabelUpdate'];
				}

				$form_generator = new DotCoreFormGenerator('gallery_form', $this->GetLink($link_params));

				$form_generator
					->SetFields($gallery_fields)
					->SetMessages($messages)
					->SetRecord($this->gallery)
					->Generate();
				$form_generator->GenerateSubmitButton($label_submit_button);

				$this->gallery_form_generator = $form_generator;

				$form = $this->gallery_form_generator->GetForm();
				if($form->WasSubmitted())
				{
					$gallery_bll->BeginTransaction($this->gallery);
					// OK, finished generating, get to processing
					$this->gallery_form_generator->ProcessForm();
					$this->gallery_form_generator->Validate();
					if(
						$this->gallery_form_generator->HasErrors() == FALSE &&
						$this->gallery_form_generator->TrySave($gallery_bll)
						)
					{
						$gallery_bll->CommitTransaction($this->gallery);
					}

					if($gallery_bll->TransactionCommitted())
					{
						if($this->mode == self::GALLERY_MANAGER_MODE_ADD)
						{
							$this->inserted = TRUE;
						}
						else
						{
							$this->edited = TRUE;
						}

						$this->mode = self::GALLERY_MANAGER_MODE_NORMAL;
					}
					else {
						$gallery_bll->Rollback($this->gallery);
					}
				}
			}
			else
			{
				$this->AddError($messages['MessageGalleryNotFound']);
			}
			
		}
		elseif($this->mode == self::GALLERY_MANAGER_MODE_MANAGE_IMAGES)
		{
			if(isset($_REQUEST['manage_images']))
			{
				$gallery_bll = new DotCoreGalleryBLL();
				$this->gallery = $gallery_bll
					->Fields(
						array(
							$gallery_bll->getFieldGalleryName(),
							$gallery_bll->getFieldGalleryFolder()
						)
					)
					->ByGalleryID($_REQUEST['manage_images'])
					->SelectFirstOrNull();
					
				if($this->gallery == NULL)
				{
					$this->AddError($messages['MessageGalleryNotFound']);
				}
			}
			if(isset($_REQUEST['sync_images'])) {
				DotCoreGalleryBLL::SynchronizeGallery($this->gallery);
			}
			if(isset($_REQUEST['delete_image'])) {
				$gallery_image_bll = new DotCoreGalleryImageBLL();
				$gallery_image = $gallery_image_bll
					->Fields(
						array(
							$gallery_image_bll->getFieldImageID()
						)
					)
					->ByImageID($_REQUEST['delete_image'])
					->SelectFirstOrNull();

				if($gallery_image != NULL)
				{
					try
					{
						$gallery_image_bll->Delete($gallery_image);
						$this->image_deleted = TRUE;
					}
					catch(Exception $ex)
					{
						$this->AddError($ex->getMessage());
					}
				}
				else
				{
					$this->AddError($messages['MessageGalleryImageNotFound']);
				}
			}
		}
		elseif($this->mode == self::GALLERY_MANAGER_MODE_ADD_IMAGES || $this->mode == self::GALLERY_MANAGER_MODE_EDIT_IMAGES)
		{
			$gallery_image_bll = new DotCoreGalleryImageBLL();
			$gallery_image_fields = array(
				$gallery_image_bll->getFieldImageID(),
				$gallery_image_bll->getFieldImagePath(),
				$gallery_image_bll->getFieldImageTitle(),
				$gallery_image_bll->getFieldImageDescription(),
				$gallery_image_bll->getFieldGalleryID()
			);

			if($this->mode == self::GALLERY_MANAGER_MODE_ADD_IMAGES)
			{
				$this->gallery_image = $gallery_image_bll->GetNewRecord();
			}
			else
			{
				$this->gallery_image = $gallery_image_bll
					->Fields($gallery_image_fields)
					->ByImageID($_REQUEST['edit_image'])
					->SelectFirstOrNull();
			}

			if($this->gallery_image != NULL)
			{
				if($this->mode == self::GALLERY_MANAGER_MODE_ADD_IMAGES) {
					$link_params = array('add_image'=>'');
					$label_submit_button = $messages['LabelAdd'];
				}
				else {
					$link_params = array('edit_image'=>$this->gallery_image->getImageID());
					$label_submit_button = $messages['LabelUpdate'];
				}

				$galleries_bll = new DotCoreGalleryBLL();
				$galleries = $galleries_bll
					->Fields(array($galleries_bll->getFieldGalleryName()))
					->SelectDictionary($galleries_bll->getFieldGalleryID());
				$galleries_dictionary = array();
				foreach($galleries as $gallery) {
					$galleries_dictionary[$gallery->getGalleryID()] = $gallery->getGalleryName();
				}

				// For now, is the gallery we're returning to
				$this->gallery = $galleries[$this->gallery_image->getGalleryID()];

				$form_generator = new DotCoreFormGenerator('gallery_image_form', $this->GetLink($link_params));
				$form_generator
					->SetFields($gallery_image_fields)
					->SetMessages($messages)
					->SetRecord($this->gallery_image)
					->AddUniqueKeyFieldMapping(DotCoreGalleryImageDAL::GALLERY_IMAGE_UNIQUE_PATH, $gallery_image_bll->getFieldImagePath())
					->SetFieldChoices(DotCoreGalleryImageDAL::GALLERY_IMAGE_GALLERY_ID, $galleries_dictionary)
					->Generate();
				$form_generator->GenerateSubmitButton($label_submit_button);

				$this->gallery_image_form_generator = $form_generator;

				$form = $this->gallery_image_form_generator->GetForm();
				if($form->WasSubmitted())
				{
					$gallery_image_bll->BeginTransaction($this->gallery_image);
					// OK, finished generating, get to processing
					$this->gallery_image_form_generator->ProcessForm();
					$this->gallery_image_form_generator->Validate();
					if(
						$this->gallery_image_form_generator->HasErrors() == FALSE &&
						$this->gallery_image_form_generator->TrySave($gallery_image_bll)
						)
					{
						$gallery_image_bll->CommitTransaction($this->gallery_image);
					}

					if($gallery_image_bll->TransactionCommitted())
					{
						if($this->mode == self::GALLERY_MANAGER_MODE_ADD_IMAGES)
						{
							$this->gallery = $galleries[$this->gallery_image->getGalleryID()];
							$this->image_inserted = TRUE;
						}
						else
						{
							$this->image_edited = TRUE;
						}

						$this->mode = self::GALLERY_MANAGER_MODE_MANAGE_IMAGES;
					}
					else {
						$gallery_image_bll->Rollback($this->gallery_image);
					}
				}
			}
			else
			{
				$this->AddError($messages['MessageGalleryImageNotFound']);
			}

		}
		else
		{
			if(isset($_REQUEST['delete']))
			{
				$gallery_bll = new DotCoreGalleryBLL();
				$this->gallery = $gallery_bll
					->Fields(
						array(
							$gallery_bll->getFieldGalleryID()
						)
					)
					->ByGalleryID($_REQUEST['delete'])
					->SelectFirstOrNull();

				if($this->gallery != NULL)
				{
					try
					{
						$gallery_bll->Delete($this->gallery);
						$this->deleted = TRUE;
					}
					catch(Exception $ex)
					{
						$this->AddError($ex->getMessage());
					}
				}
				else
				{
					$this->AddError($messages['MessageGalleryNotFound']);
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
						<th>'.$messages['TableHeaderGalleryName'].'</th>
						<th>'.$messages['TableHeaderGalleryFolder'].'</th>
						<th class="command">'.$messages['TableHeaderGalleryEditImages'].'</th>
						<th class="command">'.$messages['TableHeaderEdit'].'</th>
						<th class="command">'.$messages['TableHeaderDelete'].'</th>
				</thead>';

		$gallery_bll = new DotCoreGalleryBLL();
		$galleries = $gallery_bll
			->Fields(array(
					$gallery_bll->getFieldGalleryName(),
					$gallery_bll->getFieldGalleryFolder()
				))
			->Select();
		$count_galleries = count($galleries);
		
		for($i = 0; $i < $count_galleries; $i++)
		{
			$gallery = $galleries[$i];
			$class = ($i % 2 != 0) ? '' : ' class="alternating"';

			$result .= '
				<tr'.$class.'>
					<td>' . $gallery->getGalleryName() . '</td>
					<td>' . $gallery->getGalleryFolder() . '</td>
					<td class="command">
						<a href="'.$this->GetLink(array('manage_images'=>$gallery->getGalleryID())).'" name="gallery_edit_button">'.$messages['TableHeaderGalleryEditImages'].'</a>
					</td>
					<td class="command"><a href="'.$this->GetLink(array('edit'=>$gallery->getGalleryID())).'"><img alt="'.$messages['TableActionsEdit'].'" src="/admin/images/edit.gif" /></a></td>
					<td class="command"><a href="'.$this->GetLink(array('delete'=>$gallery->getGalleryID())).'" onclick="return confirm(\''.$messages['MessageGalleryDeletionConfirm'].'\');"><img alt="'.$messages['TableActionsDelete'].'" src="/admin/images/delete.gif" /></a></td>
				</tr>';
		}

		$result .= '
			</table>';

		$result .= '
			<div class="sub_menu">
				<a href="'.$this->GetLink(array('add'=>1)).'">'.$messages['TitleAddGallery'].'</a>
			</div>';

		return $result;
	}

	function GetGalleryImagesTable(DotCoreGalleryRecord $gallery) {
		$messages = $this->GetMessages();

		$gallery_images_bll = new DotCoreGalleryImageBLL();
		$gallery_images = $gallery_images_bll
			->Fields(
				array(
					$gallery_images_bll->getFieldImagePath(),
					$gallery_images_bll->getFieldImageDescription(),
					$gallery_images_bll->getFieldImageOrder(),
					$gallery_images_bll->getFieldImageTitle(),
					$gallery_images_bll->getFieldGalleryID()
				)
			)
			->ByGalleryID($gallery->getGalleryID())
			->Ordered()
			->Select();

		$result = '';
		
		$result .= '
			<table cellpadding="5" cellspacing="0" class="datagrid">
				<thead>
					<th>'.$messages['TableHeaderGalleryImageTitle'].'</th>
					<th>'.$messages['TableHeaderGalleryImage'].'</th>
					<th>'.$messages['TableHeaderGalleryImageDescription'].'</th>
					<th class="command">'.$messages['TableHeaderMove'].'</th>
					<th class="command">'.$messages['TableHeaderEdit'].'</th>
					<th class="command">'.$messages['TableHeaderDelete'].'</th>
				</thead>';

		$count_gallery_images = count($gallery_images);
		for($i = 0; $i < $count_gallery_images; $i++) {
			$curr_image = $gallery_images[$i];
			$class = ($i % 2 != 0) ? '' : ' class="alternating"';
			$result .= '
				<tr dotcore:img_id="'.$curr_image->getImageID().'"'.$class.'>
					<td>'.$curr_image->getImageTitle().'</td>
					<td><img alt="" src="'.ResizeMethods::Resize(
						DotCoreGalleryDAL::GALLERY_FOLDER_PATH.'/'.$this->gallery->getGalleryFolder().'/'.$curr_image->getImagePath(),
						array(
							'w'=>100,
							'h'=>100,
							'crop'=>TRUE
						)
					).'" /></td>
					<td>'.$curr_image->getImageDescription().'</td>
					<td class="command">
						<img alt="'.$messages['MoveUp'].'" src="'.DotCoreConfig::$GLOBAL_ADMIN_URL.'images/up.gif" name="up_arrow" />
						<img alt="'.$messages['MoveDown'].'" src="'.DotCoreConfig::$GLOBAL_ADMIN_URL.'images/down.gif" name="down_arrow" />
					</td>
					<td class="command">
						<a href="'.$this->GetLink(array('edit_image'=>$curr_image->getImageID())).'">
							<img alt="'.$messages['TableActionsEdit'].'" src="'.DotCoreConfig::$GLOBAL_ADMIN_URL.'images/edit.gif" />
						</a>
					</td>
					<td class="command">
						<a href="'.$this->GetLink(array('delete_image'=>$curr_image->getImageID(),'manage_images'=>$this->gallery->getGalleryID())).'" onclick="return confirm(\''.$messages['MessageConfirmImageDeletion'].'\');">
							<img alt="'.$messages['TableActionsDelete'].'" src="'.DotCoreConfig::$GLOBAL_ADMIN_URL.'images/delete.gif" />
						</a>
					</td>
			   </tr>
			';
		}

		$result .= '</table>';
		$result .= '
			<div class="sub_menu">
			<a href="'.$this->GetLink(array('add_image'=>$this->gallery->getGalleryID())).'">' . $messages['TitleAddGalleryImage'] . '</a>
			</div>';

		return $result;
		
	}
	
}

?>
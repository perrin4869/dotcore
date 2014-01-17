<?php

/**
 * DotCoreGalleryDAL - MySQL DAL for the galleries
 *
 * @author perrin
 */
class DotCoreGalleryDAL extends DotCoreDAL
{
	
	public function __construct()
	{
		parent::__construct(self::GALLERY_TABLE);

		$gallery_name_field = new DotCorePlainStringField(self::GALLERY_NAME, $this, FALSE);
		$gallery_folder_field = new DotCorePlainStringField(self::GALLERY_FOLDER, $this, FALSE);

		$this->AddField(new DotCoreAutoIncrementingKey(self::GALLERY_ID, $this));
		$this->AddField($gallery_name_field);
		$this->AddField($gallery_folder_field);

		$this->AddUniqueKey(self::GALLERY_UNIQUE_NAME, $gallery_name_field);
		$this->AddUniqueKey(self::GALLERY_UNIQUE_FOLDER, $gallery_folder_field);

		$this->SetPrimaryField(self::GALLERY_ID);
	}

	/**
	 *
	 * @return DotCoreGalleryDAL
	 */
	public static function GetInstance()
	{
		return parent::GetDALInstance(__CLASS__);
	}

	const GALLERY_TABLE = 'dotcore_galleries';

	const GALLERY_ID = 'gallery_id';
	const GALLERY_NAME = 'gallery_name';
	const GALLERY_FOLDER = 'gallery_folder';

	const GALLERY_UNIQUE_NAME = 'gallery_unique_name';
	const GALLERY_UNIQUE_FOLDER = 'gallery_unique_folder';

	const GALLERY_FOLDER_PATH = '/images/galleries/';

	/**
	 * Returns a record of DotCoreGalleryDAL
	 * @return DotCoreGalleryRecord
	 */
	public function GetRecord()
	{
		return new DotCoreGalleryRecord($this);
	}

	// Overriding

	public function Insert($record)
	{
		$this->CreateFolderIfNotExisting($record);
		parent::Insert($record);
	}

	public function Update(DotCoreGalleryRecord $record)
	{
		// Check if the folder needs renaming
		if($record->FieldChanged(DotCoreGalleryDAL::GALLERY_FOLDER))
		{
			$needs_rename = TRUE;
			$original_folder = $record->GetOriginalValue(DotCoreGalleryDAL::GALLERY_FOLDER);
		}
		else
		{
			$needs_rename = FALSE;
		}

		parent::Update($record);

		if($needs_rename)
		{
			$this->RenameFolder($original_folder, $record);
		}
	}

	public function Delete(DotCoreGalleryRecord $record = NULL)
	{
		if($record != NULL)
		{
			$dir_path = $_SERVER['DOCUMENT_ROOT'] . self::GALLERY_FOLDER_PATH . $record->getGalleryFolder();
		}
		
		parent::Delete($record);

		if($record != NULL)
		{
			// Delete the gallery folder if it exists
			if(file_exists($dir_path))
			{
				remove_dir($dir_path);
			}
		}
	}

	protected function CreateFolderIfNotExisting(DotCoreGalleryRecord $record)
	{
		$dir_path = $_SERVER['DOCUMENT_ROOT'] . self::GALLERY_FOLDER_PATH . $record->getGalleryFolder();
		if(!file_exists($dir_path))
		{
			mkdir($dir_path, 0777, TRUE);
			// In case mkdir ignores the permissions specified
			chmod($dir_path, 0777);
		}
	}

	protected function RenameFolder($old_filename, DotCoreGalleryRecord $record)
	{
		$base_gallery_dir = $_SERVER['DOCUMENT_ROOT'] . self::GALLERY_FOLDER_PATH;
		$old_path = $base_gallery_dir . $old_filename;
		$dir_path = $base_gallery_dir . $record->getGalleryFolder();
		if(file_exists($old_path))
		{
			rename($old_path, $dir_path);
		}

		// Make sure there's a directory for the gallery
		$this->CreateFolderIfNotExisting($record);
	}

}

?>
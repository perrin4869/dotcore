<?php

/**
 * Class used to represent the path to an image in a gallery
 * @author perrin
 *
 */
class DotCoreGalleryImageField extends DotCoreImageField
{
	/**
	 * Constructor of DotCoreImageField
	 * @param string $field_name
	 * @param DotCoreDAL $dal
	 * @param $rename If true, the name will be renamed to a unique name
	 * @param $is_nullable
	 */
	public function __construct(
			$field_name,
			DotCoreDAL $dal,
			$rename = FALSE,
			$is_nullable = TRUE,
			$max_file_size = 0 // Any size by default
			)
	{
		parent::__construct($field_name, $dal, NULL, $rename, $is_nullable, $max_file_size);
	}

	private static $galleries_folders = array();

	public function getDestinationFolder(DotCoreGalleryImageRecord $image) {
		return $this->LoadDestinationFolderByGallery($image->getGalleryID()) . '/';
	}

	public function getOriginalDestinationFolder(DotCoreGalleryImageRecord $image) {
		if(
			$image->IsEmpty() ||
			!$image->FieldChanged(DotCoreGalleryImageDAL::GALLERY_IMAGE_GALLERY_ID)) {
			$gallery_id = $image->getGalleryID();
		}
		else {
			$gallery_id = $image->GetOriginalValue(DotCoreGalleryImageDAL::GALLERY_IMAGE_GALLERY_ID);
		}
		return $this->LoadDestinationFolderByGallery($gallery_id) . '/';
	}

	public function setDestinationFolder(DotCoreGalleryImageRecord $image) {
		throw new Exception('DotCoreGalleryImageField::setDestinationFolder is not allowed');
	}

	protected function LoadDestinationFolderByGallery($gallery_id) {
		if(self::$galleries_folders[$gallery_id] == NULL) {
			$gallery_dal = DotCoreGalleryDAL::GetInstance();
			$restraint = new DotCoreFieldRestraint($gallery_dal->GetField(DotCoreGalleryDAL::GALLERY_ID), $gallery_id);
			$restraints = new DotCoreDALRestraint();
			$restraints->AddRestraint($restraint);
			$folder = $gallery_dal
				->Fields(array($gallery_dal->GetField(DotCoreGalleryDAL::GALLERY_FOLDER)))
				->Restraints($restraints)
				->SelectScalar();
			self::$galleries_folders[$gallery_id] = DotCoreGalleryDAL::GALLERY_FOLDER_PATH . $folder;
		}
		return self::$galleries_folders[$gallery_id];
	}
	
}

?>
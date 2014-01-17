<?php

/**
 * DotCoreGalleryRecord - Defines a single record of a gallery obtained by DotCoreGalleryDAL
 *
 * @author perrin
 */
class DotCoreGalleryRecord extends DotCoreDataRecord {

	/**
	 * Constructor for Gallery record
	 *
	 * @param DotCoreDAL $dal
	 */
	public function  __construct(DotCoreDAL $dal) {
		parent::__construct($dal);
	}

	/*
	 *
	 * Accessors:
	 *
	 */

	/*
	 * Getters:
	 */

	/**
	 * Gets the ID of this gallery
	 * @return int
	 */
	public function getGalleryID()
	{
		return $this->GetField(DotCoreGalleryDAL::GALLERY_ID);
	}

	/**
	 * Gets the name of this gallery
	 * @return string
	 */
	public function getGalleryName()
	{
		return $this->GetField(DotCoreGalleryDAL::GALLERY_NAME);
	}

	/**
	 * Gets the name of the folder under which the gallery is stored
	 * @return string
	 */
	public function getGalleryFolder()
	{
		return $this->GetField(DotCoreGalleryDAL::GALLERY_FOLDER);
	}

	/*
	 * Setters:
	 */

	/**
	 * Sets the ID of this gallery
	 * @param $id
	 * @return void
	 */
	private function setGalleryID($id)
	{
		$this->SetField(DotCoreGalleryDAL::GALLERY_ID, $id);
	}

	/**
	 * Sets the name of the gallery
	 * @param string $name
	 * @return void
	 */
	public function setGalleryName($name)
	{
		$this->SetField(DotCoreGalleryDAL::GALLERY_NAME, $name);
	}

	/**
	 * Sets the folder of this gallery
	 * @param $folder
	 * @return void
	 */
	public function setGalleryFolder($folder)
	{
		$this->SetField(DotCoreGalleryDAL::GALLERY_FOLDER, $folder);
	}

}
?>

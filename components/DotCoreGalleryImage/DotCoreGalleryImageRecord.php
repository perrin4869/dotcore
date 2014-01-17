<?php

/**
 * DotCoreGalleryImageRecord - Defines a single record of an image of a gallery obtained by DotCoreGalleryDAL
 *
 * @author perrin
 */
class DotCoreGalleryImageRecord extends DotCoreDataRecord {

	/**
	 * Constructor for Gallery Image record
	 *
	 * @param DotCoreDAL $dal
	 */
	public function  __construct(DotCoreDAL $dal) {
		parent::__construct($dal);
	}

	public function  __toString() {
		return $this->getImagePath();
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
	 *
	 * @return int
	 */
	public function getImageID() {
		return $this->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_ID);
	}

	/**
	 * 
	 * @return int
	 */
	public function getGalleryID()
	{
		return $this->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_GALLERY_ID);
	}

	/**
	 * 
	 * @return string
	 */
	public function getImagePath()
	{
		return $this->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_PATH);
	}

	/**
	 *
	 * @return string
	 */
	public function getImageTitle()
	{
		return $this->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_TITLE);
	}

	/**
	 *
	 * @return string
	 */
	public function getImageDescription()
	{
		return $this->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_DESC);
	}

	/**
	 *
	 * @return int
	 */
	public function getImageOrder()
	{
		return $this->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_ORDER);
	}

	/*
	 * Setters:
	 */

	/**
	 *
	 * @param int $id
	 */
	private function setImageID($id) {
		$this->SetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_ID, $id);
	}

	/**
	 * 
	 * @param $id
	 */
	public function setGalleryID($id)
	{
		$this->SetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_GALLERY_ID, $id);
	}

	/**
	 * 
	 * @param string $path
	 */
	public function setImagePath($path)
	{
		$this->SetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_PATH, $path);
	}

	/**
	 * 
	 * @param $title
	 */
	public function setImageTitle($title)
	{
		$this->SetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_TITLE, $title);
	}

	/**
	 *
	 * @param $desc
	 */
	public function setImageDescription($desc)
	{
		$this->SetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_DESC, $desc);
	}

	/**
	 *
	 * @param $order
	 */
	public function setImageOrder($order)
	{
		$this->SetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_ORDER, $order);
	}

}
?>

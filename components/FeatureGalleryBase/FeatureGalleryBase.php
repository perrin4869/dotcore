<?php
// +------------------------------------------------------------------------+
// | DotCoreExtension.php												   |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2003-2008. All rights reserved.		  |
// | Version	   0.01													 |
// | Last modified 17/03/2009											   |
// | Email		 juliangrinblat@gmail.com								 |
// | Web		   http://www.dotcore.co.il								 |
// +------------------------------------------------------------------------+

/**
 * Class FeatureGalleryBase
 * Provides basic functionality for Gallery features
 *
 * @version   0.01
 * @author	Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
abstract class FeatureGalleryBase extends DotCoreFeature {

	public function  __construct(DotCoreFeatureRecord $record, $parameters, $content = NULL) {

		parent::__construct($record, $parameters, $content);
		
		if(($id = $this->GetParameter(array('gallery_id', 'זהות_הגלריה'))) != NULL)
		{
			$this->LoadGalleryByID($id);
		}
		elseif(($name = $this->GetParameter(array('name', 'שם'))) != NULL)
		{
			$this->LoadGalleryByName($name);
		}
	}

	/**
	 *
	 * @var DotCoreGalleryRecord
	 */
	protected $gallery = NULL;
	protected $images = NULL;

	/**
	 * Gets all the images in this gallery
	 * @return array
	 */
	public function GetImages()
	{
		if($this->images == NULL)
		{
			$gallery_image_bll = new DotCoreGalleryImageBLL();
			$this->images = $gallery_image_bll
				->Fields(
					array(
						$gallery_image_bll->getFieldImagePath(),
						$gallery_image_bll->getFieldImageTitle(),
						$gallery_image_bll->getFieldImageDescription()
					)
				)
				->ByGalleryID($this->gallery->getGalleryID())
				->Ordered()
				->Select();
		}
		return $this->images;
	}

	/**
	 * Gets the images in the page in offset $page_num (starting in 1), the size of $page_size
	 * @param int $page_length
	 * @param int $page_num starting from 1
	 * @return array of DotCoreGalleryImage
	 */
	public function GetImagesPage($page_length, $page_num)
	{
		if($this->images == NULL) {
			$gallery_image_bll = new DotCoreGalleryImageBLL();
			$this->images = $gallery_image_bll
				->Fields(
					array(
						$gallery_image_bll->getFieldImagePath(),
						$gallery_image_bll->getFieldImageTitle(),
						$gallery_image_bll->getFieldImageDescription()
					)
				)
				->Ordered()
				->ByGalleryID($this->gallery->getGalleryID())
				->Page($page_length, $page_num)
				->Select();
		}
		return $this->images;
	}

	/**
	 * Gets the count of images in the gallery of this feature
	 * @return int
	 */
	public function GetImagesCount()
	{
		return count($this->GetImages());
	}

	/**
	 * Gets the number of pages for a given size of page. If given a count of pages,
	 * it'll be used, instead of counting again (can be used to improve performance)
	 * @param int $page_size
	 * @param int $pages_count
	 * @return int
	 */
	public function GetPagesCount($page_size, $pages_count = NULL)
	{
		$gallery_image_bll = new DotCoreGalleryImageBLL();
		$count = $gallery_image_bll
			->Fields(new DotCoreCount($gallery_image_bll->GetDAL()))
			->ByGalleryID($this->gallery->getGalleryID())
			->SelectScalar();
		return ceil($count / $page_size);
	}

	public function GalleryExists()
	{
		return $this->gallery != NULL;
	}

	public function LoadGalleryByID($id)
	{
		$gallery_bll = new DotCoreGalleryBLL();
		$this->gallery = $gallery_bll
			->Fields(
				array(
					$gallery_bll->getFieldGalleryFolder(),
					$gallery_bll->getFieldGalleryName()
				)
			)
			->ByGalleryID($id)
			->SelectFirstOrNull();
	}

	public function LoadGalleryByName($name)
	{
		$gallery_bll = new DotCoreGalleryBLL();
		$this->gallery = $gallery_bll
			->Fields(
				array(
					$gallery_bll->getFieldGalleryFolder(),
					$gallery_bll->getFieldGalleryName()
				)
			)
			->ByGalleryName($name)
			->SelectFirstOrNull();
	}
}
?>

<?php
// +------------------------------------------------------------------------+
// | FeatureFadeInGallery.php											   |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2003-2008. All rights reserved.		  |
// | Version	   0.03													 |
// | Last modified 12/09/2009											   |
// | Email		 juliangrinblat@gmail.com								 |
// | Web		   http://www.dotcore.co.il								 |
// +------------------------------------------------------------------------+

/**
 * Class FeatureFadeInGallery
 * Implements a feature in which the thumbnails of the gallery images are shown
 * in a table, and a lightbox opens them up fullsize
 *
 * @version   0.03
 * @author	Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
class FeatureFadeInGallery extends FeatureGalleryBase {
	public function __construct(DotCoreFeatureRecord $record, $parameters = array())
	{
		parent::__construct($record, $parameters);

		self::$gallery_count++;

		if($this->GalleryExists())
		{

			// Parse the current page
			$configuration = $this->GetConfiguration();
			$this->page_size = $configuration->GetValue('page_size');
			$count_pages = $this->GetPagesCount($this->page_size);
			if(
				!isset($_REQUEST['page']) ||
				!is_numeric($_REQUEST['page']) ||
				$_REQUEST['page'] < 1 ||
				$_REQUEST['page'] > $count_pages)
			{
				$this->page = 1;
			}
			else
			{
				$this->page = $_REQUEST['page'];
			}

			if($this->page == $count_pages)
			{
				$this->last_page = TRUE;
			}



			// Parse the parameters
			$this->fullsize_resize_options['w'] = $this->GetParameter(
				array(
					'fullsized_image_width',
					'אורך_התמונה_המוגדלת'
				),
				$configuration->GetValue('default_fullsized_image_width'));
			
			$this->fullsize_resize_options['h'] = $this->GetParameter(
				array(
					'fullsized_image_height',
					'גובה_התמונה_המוגדלת'
				),
				$configuration->GetValue('default_fullsized_image_height'));

			$this->thumbnails_menu_width = $this->GetParameter(
				array(
					'thumbnails_menu_width',
					'אורך_תפריט_התמונות'
				),
				$configuration->GetValue('default_thumbnails_menu_width'));
		}

		// Initilize remaining properties
		$this->gallery_wrapper_id = 'gallery-wrapper-'.self::$gallery_count;

		// Register the header content
		$curr_page = DotCorePageRenderer::GetCurrent();
		$header_content = $this->GetHeaderContent();
		$curr_page->RegisterHeaderContent($header_content);

		DotCorePageRenderer::GetCurrent()->StoreValue('page', $this->page);
	}

	/*
	 *
	 *
	 * Feature methods:
	 *
	 *
	 */

	protected $page;
	protected $page_images;
	protected $page_size;
	protected $last_page = FALSE;
	protected $gallery_wrapper_id = NULL;
	protected $thumbnails_menu_width = NULL;

	protected $thumb_resize_options = array('w'=>85, 'h'=>65, 'crop'=>TRUE);
	protected $fullsize_resize_options = array('crop'=>TRUE, 'cache'=>FALSE);

	/*
	 *
	 * Accessors:
	 *
	 */

	public function getFullsizedImageWidth() {
		return $this->fullsize_resize_options['w'];
	}

	public function getFullsizedImageHeight() {
		return $this->fullsize_resize_options['h'];
	}

	public function getThumbnailsMenuWidth() {
		return $this->thumbnails_menu_width;
	}

	/**
	 * Holds the count of galleries created by this feature
	 * @var int
	 */
	protected static $gallery_count = 0;

	public function GetPageImages()
	{
		if($this->page_images == NULL)
		{
			$this->page_images = $this->GetImagesPage($this->page_size, $this->page);
		}
		return $this->page_images;
	}

	/**
	 * Shows the contact form to the user
	 *
	 */
	public function GetFeatureContent()
	{
		$messages = $this->GetMessages();

		if($this->GalleryExists())
		{
			$images = $this->GetPageImages();
			$count = count($images);

			if($count > 0)
			{
				$curr_renderer = DotCorePageRenderer::GetCurrent();
				$page_direction = $curr_renderer->GetLanguage()->getLanguageDirection();
				$style_images_path = $curr_renderer->GetImagesFolderUrl();
				$gallery_folder_path = DotCoreGalleryBLL::GetGalleryRootPath($this->gallery);

				$html .= '
				<div id="'.$this->gallery_wrapper_id.'" class="gallery-wrapper requires-loading-wrapper">
					<div class="fade-in-gallery-loading loading-message"><table><tr><td><img alt="'.$messages['MessageLoadingImages'].'" src="'.$style_images_path.'loader.gif" /></td></tr></table></div>

					<div class="gallery-content requires-loading-content">
						<div class="gallery-images-div">
							<div class="gallery-thumbnails-div">
							';
								for($printed_count = 0; $printed_count < $this->page_size && isset($images[$printed_count]); $printed_count++)
								{
									$html .= '
									<a href="'.ResizeMethods::resize($gallery_folder_path.$images[$printed_count], $this->fullsize_resize_options).'">
										<img alt="'.$images[$printed_count]->getImageTitle().'" src="'.ResizeMethods::resize($gallery_folder_path.$images[$printed_count], $this->thumb_resize_options).'" />
									</a>';
								}

							$html .= '
							</div>';

							$html .= '<div class="gallery-nav">';

							$right_arrow = '<img src="'.$this->GetFeatureUrl().'arrow_right.gif" alt="" />';
							$left_arrow = '<img src="'.$this->GetFeatureUrl().'arrow_left.gif" alt="" />';

							$prev_text = ' ';
							$prev_text .= ($page_direction == DotCoreLanguageDAL::LANGUAGES_DIRECTION_RTL) ?  $right_arrow : $left_arrow;
							$prev_text .= ' ' . $messages['MessagePrevPage'];

							if($this->page > 1)
							{
								$url = $curr_renderer->GetPostbackUrl(array('page'=>$this->page-1));
								$html .= '<a href="'.$url.'">'.$prev_text.'</a> ';
							}
							else
							{
								$html .= $prev_text;
							}

							$html .= ' | ';

							$next_text = ' ' . $messages['MessageNextPage'] . ' ';
							$next_text .= ($page_direction == DotCoreLanguageDAL::LANGUAGES_DIRECTION_RTL) ? $left_arrow : $right_arrow;

							if(!$this->last_page) {
								$url = $curr_renderer->GetPostbackUrl(array('page'=>$this->page+1));
								$html .= '<a href="'.$url.'">'.$next_text.'</a>';
							}
							else
							{
								$html .= $next_text;
							}

							$html .= '
							</div>
						</div>

						<div class="gallery-fullsize"></div>
					</div>
				</div>';

				return $html;
			}
			else
			{
				$html .= '
				<div id="'.$this->gallery_wrapper_id.'">'.$messages['MessageNoImagesInGallery'].'</div>';
			}
		}
		else
		{
			$html .= '
			<div id="'.$this->gallery_wrapper_id.'">'.$messages['MessageGalleryNotFound'].'</div>';
		}


		return $html;
	}

	public function GetHeaderContent()
	{
		$header = '';

		if(self::$gallery_count == 1)
		{
			$page_renderer = DotCorePageRenderer::GetCurrent();
			$page_renderer->LoadComponent('loading');

			$header = '
				<link rel="stylesheet" type="text/css" href="'.$this->GetFeatureUrl().'fade_in_gallery.css" />
				<script type="text/javascript" src="' . $this->GetFeatureUrl(). 'fade_in_gallery.js"></script>';

			$page_direction = DotCorePageRenderer::GetCurrent()->GetLanguage()->getLanguageDirection();
			if($page_direction == DotCoreLanguageDAL::LANGUAGES_DIRECTION_LTR)
			{
				$header .= '
					<link rel="stylesheet" type="text/css" href="' . $this->GetFeatureUrl(). 'fade_in_gallery_ltr.css" />';
			}
		}

		if($this->GalleryExists())
		{
			$js_gallery_name = 'fade_in_gallery_'.self::$gallery_count;

			$header .= '
			<script type="text/javascript">
			//<![CDATA[

				var '.$js_gallery_name.';
				addEvent(window, "load", function()
				{
					'.$js_gallery_name.' = new FadeInGallery
					(
						"'.$this->gallery_wrapper_id.'",
						{
							imagesMenuWidth: "'.$this->getThumbnailsMenuWidth().'px"
						}
					);
					'.$js_gallery_name.'.Initilize();
				});

			//]]>
			</script>';
		}

		return $header;
	}
}
?>

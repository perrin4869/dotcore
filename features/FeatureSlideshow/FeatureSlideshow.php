<?php
// +------------------------------------------------------------------------+
// | FeatureSlideshow.php												   |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2003-2008. All rights reserved.		  |
// | Version	   0.01													 |
// | Last modified 07/08/2009											   |
// | Email		 juliangrinblat@gmail.com								 |
// | Web		   http://www.dotcore.co.il								 |
// +------------------------------------------------------------------------+

/**
 * Class FeatureSlideshow
 * Implements a feature in which the thumbnails of the gallery images are shown
 * in a table, and a lightbox opens them up fullsize
 *
 * @version   0.01
 * @author	Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
class FeatureSlideshow extends FeatureGalleryBase {
	public function __construct(DotCoreFeatureRecord $record, $parameters = array())
	{
		parent::__construct($record, $parameters);

		self::$gallery_count++;

		$configuration = $this->GetConfiguration();

		// Initilize remaining properties
		$this->gallery_wrapper_id = $this->GetParameter(array('ID', 'זהות'), 'feature-slideshow-wrapper-'.self::$gallery_count);
		$this->resize_options['cache'] = intval($this->GetParameter(array('cache', 'מטמון'), 1));
		$this->resize_options['w'] = intval($this->GetParameter(array('width', 'אורך'), $configuration->GetValue('default_slideshow_width')));
		$this->resize_options['h'] = intval($this->GetParameter(array('height', 'גובה'), $configuration->GetValue('default_slideshow_height')));
		$this->effect = $this->GetParameter(array('effect', 'אפקט'), NULL);

		// Register the header content
		$curr_page = DotCorePageRenderer::GetCurrent();
		$header_content = $this->GetHeaderContent();
		$curr_page->RegisterHeaderContent($header_content);
	}

	/*
	 *
	 *
	 * Feature methods:
	 *
	 *
	 */

	 protected $gallery_wrapper_id = NULL;
	 protected $resize_options = array('crop'=>TRUE);
	 protected $effect = NULL;

	 /**
	  * Holds the count of galleries created by this feature
	  * @var int
	  */
	 protected static $gallery_count = 0;

	/**
	 * Shows the contact form to the user
	 *
	 */
	public function GetFeatureContent()
	{
		$messages = $this->GetMessages();
		$html = '';

		if($this->GalleryExists())
		{
			$images = $this->GetImages();
			$count = count($images);

			if($count > 0)
			{
				$style_images_path = DotCorePageRenderer::GetCurrent()->GetImagesFolderUrl();
				$gallery_folder_path = DotCoreGalleryBLL::GetGalleryRootPath($this->gallery);

				$images_list = '';
				for($i = 0; $i < $count; $i++)
				{
					$images_list .= '
						<img alt="" width="'.$this->resize_options['w'].'" height="'.$this->resize_options['h'].'" src="'.ResizeMethods::resize($gallery_folder_path.$images[$i], $this->resize_options).'" />';
				}

				$html .= '
				<div id="'.$this->gallery_wrapper_id.'" class="feature-slideshow-wrapper requires-loading-wrapper">
					<div class="feature-slideshow-loading loading-message"><table><tr><td><img alt="'.$messages['MessageLoadingImages'].'" src="'.$style_images_path.'/loader.gif" /></td></tr></table></div>
					<div class="feature-slideshow-images requires-loading-content">
						'.$images_list.'
					</div>
				</div>';

				return $html;
			}
			else
			{
				$html .= '
				<div id="'.$this->gallery_wrapper_id.'" class="feature-slideshow-wrapper">'.$messages['MessageNoImagesInGallery'].'</div>';
			}
		}
		else
		{
			$html .= '
			<div id="'.$this->gallery_wrapper_id.'" class="feature-slideshow-wrapper">'.$messages['MessageGalleryNotFound'].'</div>';
		}

		return $html;
	}

	public function GetHeaderContent()
	{
		$header = '';
		$feature_path = $this->GetFeatureUrl();
		$current_renderer = DotCorePageRenderer::GetCurrent();
		if(self::$gallery_count == 1)
		{
			$current_renderer->LoadComponent('loading');
			$header .= '
			<link rel="stylesheet" type="text/css" href="' . $feature_path . 'feature_slideshow.css" />
			<script type="text/javascript" src="' . $feature_path . 'feature_slideshow.js"></script>';
		}

		if($this->GalleryExists() && $this->GetImagesCount() > 0)
		{
			$current_renderer->LoadComponent('jquery_cycle');

			$load_function_name = 'OnFeatureSlideshow'.self::$gallery_count.'Load';
			$js_gallery_name = 'feature_slideshow'.self::$gallery_count;
			$fx_js = '';
			if($this->effect != NULL)
			{
				$fx_js = 'fx: "'.$this->effect.'",';
			}

			$messages = $this->GetMessages();

			$header .= '
			<script type="text/javascript">
			//<![CDATA[

				function '.$load_function_name.'()
				{
					'.$js_gallery_name.' = new FeatureSlideshow(
						"'.$this->gallery_wrapper_id.'",
						{
							'.$fx_js.'
							width: "'.$this->resize_options['w'].'px",
							height: "'.$this->resize_options['h'].'px"
						});

					'.$js_gallery_name.'.Initilize();
				}

				var '.$js_gallery_name.';
				addEvent(window, "load", '.$load_function_name . ');

			//]]>
			</script>';
		}

		return $header;
	}
}
?>

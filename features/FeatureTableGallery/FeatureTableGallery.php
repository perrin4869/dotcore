<?php
// +------------------------------------------------------------------------+
// | FeatureTableGallery.php												|
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2003-2008. All rights reserved.		  |
// | Version	   0.01													 |
// | Last modified 12/09/2009											   |
// | Email		 juliangrinblat@gmail.com								 |
// | Web		   http://www.dotcore.co.il								 |
// +------------------------------------------------------------------------+

/**
 * Class FeatureTableGallery
 *
 * @version   0.01
 * @author	Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
class FeatureTableGallery extends FeatureGalleryBase {
	public function __construct(DotCoreFeatureRecord $record, $parameters = array())
	{
		parent::__construct($record, $parameters);

		self::$gallery_count++;

		if($this->GalleryExists())
		{
			// Parse the current page
			$configuration = $this->GetConfiguration();
			$this->rows = $configuration->GetValue('table_rows');
			$this->cols = $configuration->GetValue('table_cols');
			$page_size = $this->GetPageSize();
			$count_pages = $this->GetPagesCount($page_size);
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
	protected $rows;
	protected $cols;
	protected $last_page = FALSE;
	protected $gallery_wrapper_id = NULL;

	protected $thumb_resize_options = array('w'=>85, 'h'=>65, 'crop'=>TRUE);

	public function GetPageSize() {
		return $this->rows * $this->cols;
	}

	/*
	 *
	 * Accessors:
	 *
	 */

	/**
	 * Holds the count of galleries created by this feature
	 * @var int
	 */
	protected static $gallery_count = 0;

	public function GetPageImages()
	{
		if($this->page_images == NULL)
		{
			$this->page_images = $this->GetImagesPage($this->GetPageSize(), $this->page);
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
				$gallery_folder_path = DotCoreGalleryBLL::GetGalleryFolderUrl($this->gallery);

				$html .= '
				<div id="'.$this->gallery_wrapper_id.'" class="gallery-wrapper">
					<div class="gallery-thumbnails-div">
					<table>
					';
						$curr_offset = 0;
						for($i = 0; $i < $this->rows; $i++) {
							$html .= '<tr>';
							for($j = 0; $j < $this->cols; $j++) {
								if(key_exists($curr_offset, $images)) {
									$html .= '
									<td>
										<a rel="lightbox[gallery'.self::$gallery_count.']" title="'.$images[$curr_offset]->getImageDescription().'" href="'.$gallery_folder_path.$images[$curr_offset].'">
											<img alt="'.$images[$curr_offset]->getImageTitle().'" src="'.ResizeMethods::resize($gallery_folder_path.$images[$curr_offset], $this->thumb_resize_options).'" />
										</a>
									</td>';
								}
								else {
									while($j < $this->cols) {
										$html .= '<td></td>';
										$j++;
									}
								}
								$curr_offset++;
							}
							$html .= '</tr>';
						}

					$html .= '
					</table>
					</div>
					<div class="gallery-nav">';

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
			$page_renderer->LoadComponent('jquery');
			$page_renderer->LoadComponent('pretty_photo');

			$header = '
				<link rel="stylesheet" type="text/css" href="'.$this->GetFeatureUrl().'table_gallery.css" />';
		}

		return $header;
	}
}
?>

<?php
// +------------------------------------------------------------------------+
// | FeatureLightBoxSlideGallery.php                                        |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2003-2008. All rights reserved.          |
// | Version       0.02                                                     |
// | Last modified 26/07/2009                                               |
// | Email         juliangrinblat@gmail.com                                 |
// | Web           http://www.dotcore.co.il                                 |
// +------------------------------------------------------------------------+

class FeatureLightBoxSlideGallery extends FeatureGalleryBase
{
    public function __construct(DotCoreFeatureRecord $record, $parameters = array())
    {
        parent::__construct($record, $parameters);

        self::$gallery_count++;

        // Initilize remaining properties
        $this->gallery_wrapper_id = 'gallery-wrapper-'.self::$gallery_count;

        // Register the header content
        $curr_page = DotCorePageRenderer::GetCurrent();
        $header_content = $this->GetHeaderContent();
        $curr_page->RegisterHeaderContent($header_content);
    }

    /**
      * Holds the count of galleries created by this feature
      * @var int
      */
    protected static $gallery_count = 0;

    protected $gallery_wrapper_id = NULL;

    /*
     *
     *
     * Feature methods:
     *
     *
     */
	
    /**
     * Shows the contact form to the user
     *
     */
    public function GetFeatureContent()
    {
        $messages = $this->GetMessages(); // Needed to get all the correct messages in the correct language
        $html = '';

        if($this->GalleryExists())
        {
            $images = $this->GetImages();
            $count = count($images);

            if($count > 0)
            {
                $resize_options = array('w'=>100, 'h'=>75, 'crop'=>TRUE);
                $style_images_path = DotCorePageRenderer::GetCurrent()->GetImagesFolderUrl();
                $gallery_folder_path = DotCoreGalleryBLL::GetGalleryRootPath($this->gallery);

                $html .= '
                <div class="lb_slide_gallery_wrapper requires-loading-wrapper" id="'.$this->gallery_wrapper_id.'">
                    <div class="lb_slide_loading_div loading-message"><table><tr><td><img alt="'.$messages['MessageLoadingImages'].'" src="'.$style_images_path.'/loader.gif" /></td></tr></table></div>';
                $html .= '
                    <div class="lb_slide_gallery_content requires-loading-content">
                        <img class="lb_slide_prev_pic_anchor" alt="" src="'.$this->GetFeatureUrl().'gallL.gif" />
                        <div class="lb_slide_gallery_images_thumbnails">';

                for($i = 0; $i < $count; $i++)
                {
                    $html .= '
                        <a rel="lightbox[gallery'.self::$gallery_count.']" href="'.DotCoreGalleryDAL::GALLERY_FOLDER_PATH.$this->gallery->getGalleryFolder().'/' . $images[$i].'">
                            <img alt="" src="'.ResizeMethods::resize($gallery_folder_path . $images[$i], $resize_options).'" />
                        </a>';
                }

                $html .= '
                        </div>
                        <img class="lb_slide_next_pic_anchor" alt="" src="'.$this->GetFeatureUrl().'gallR.gif" />
                    </div>
                </div>';
            }
            else
            {
                $html .= '
                    <div class="lb_slide_gallery_wrapper" id="'.$this->gallery_wrapper_id.'">'.$messages["MessageNoImagesInGallery"].'</div>';
            }
        }
        else
        {
            $html .= '
                <div class="lb_slide_gallery_wrapper" id="'.$this->gallery_wrapper_id.'">'.$messages["MessageGalleryNotFound"].'</div>';
        }

        return $html;
    }

    public function GetHeaderContent()
    {
        $header = '';
        $current_renderer = DotCorePageRenderer::GetCurrent();

        if(self::$gallery_count == 1)
        {
            $current_renderer->LoadComponent('loading');
            $header .= '
            <link type="text/css" rel="stylesheet" href="'.$this->GetFeatureUrl().'gallery.css" />
            <script type="text/javascript" src="'.$this->GetFeatureUrl().'gallery_slide.js"></script>';
        }

        if($this->GalleryExists() && $this->GetImagesCount() > 0)
        {
            $current_renderer->LoadComponent('div_scroller');
            $current_renderer->LoadComponent('jquery');
            $current_renderer->LoadComponent('pretty_photo');

            $load_function_name = 'OnGallery'.self::$gallery_count.'Load';
            $js_gallery_name = 'lightbox_slide_gallery_'.self::$gallery_count;
            $images_js_array = '';

            $header .= '
                <script type="text/javascript">
                //<![CDATA[

                function '.$load_function_name.'()
                {
                    '.$js_gallery_name.' = new LightboxSlideGallery("'.$this->gallery_wrapper_id.'");
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
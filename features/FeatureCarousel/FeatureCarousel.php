<?php
// +------------------------------------------------------------------------+
// | FeatureCarousel.php                                                    |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2009. All rights reserved.               |
// | Version       0.01                                                     |
// | Last modified 31/08/2009                                               |
// | Email         juliangrinblat@gmail.com                                 |
// | Web           http://www.dotcore.co.il                                 |
// +------------------------------------------------------------------------+

/**
 * Class FeatureCarousel
 * Implements a feature in which a gallery is shown in the form of a 2D carousel
 *
 * @version   0.01
 * @author    Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
class FeatureCarousel extends FeatureGalleryBase {
    public function __construct(DotCoreFeatureRecord $record, $parameters = array())
    {
        parent::__construct($record, $parameters);

        $this->resize_options['h'] = intval($this->GetParameter(array('height', 'גובה'), 75));

        if(isset($parameters['carousel_id']))
        {
            $this->carousel_wrapper_id = $parameters['carousel_id'];
        }

        self::$gallery_count++;

        // Initilize remaining properties
        if($this->carousel_wrapper_id == NULL)
        {
            $this->carousel_wrapper_id = 'feature-carousel-wrapper-'.self::$gallery_count;
        }

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

    protected $carousel_wrapper_id = NULL;
    protected $resize_options = array('crop'=>TRUE);

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
                    $images_list .= '<img alt="" src="'.ResizeMethods::resize($gallery_folder_path.$images[$i], $this->resize_options).'" />';
                }

                $html .= '
                <div id="'.$this->carousel_wrapper_id.'" class="feature-carousel-wrapper requires-loading-wrapper">
                    <div class="feature-carousel-loading loading-message"><table><tr><td><img alt="'.$messages['Loading'].'" src="'.$style_images_path.'/loader.gif" /></td></tr></table></div>
                    <div class="feature-carousel-images requires-loading-content">'.$images_list.'</div>
                </div>';

                return $html;
            }
            else
            {
                $html .= '
                <div id="'.$this->carousel_wrapper_id.'" class="feature-carousel-wrapper">'.$messages['MessageNoImagesInGallery'].'</div>';
            }
        }
        else
        {
            $html .= '
            <div id="'.$this->carousel_wrapper_id.'" class="feature-carousel-wrapper">'.$messages['MessageGalleryNotFound'].'</div>';
        }
        

        return $html;
    }

    public function GetHeaderContent()
    {
        $header = '';
        
        if(self::$gallery_count == 1)
        {
            $feature_path = $this->GetFeatureUrl();

            $header .= '
            <link rel="stylesheet" type="text/css" href="' . $feature_path . 'feature_carousel.css" />
            <script type="text/javascript" src="' . $feature_path . 'feature_carousel.js"></script>';
        }

        if($this->GalleryExists() && $this->GetImagesCount() > 0)
        {
            $current_renderer = DotCorePageRenderer::GetCurrent();
            $current_renderer->LoadComponent('loading');
            $current_renderer->LoadComponent('marquee');

            $js_gallery_name = 'feature_carousel'.self::$gallery_count;
            $images_js_array = '';

            $messages = $this->GetMessages();

            $header .= '
            <script type="text/javascript">
            //<![CDATA[

                var '.$js_gallery_name.';
                addEvent(window, "load", function()
                {
                    '.$js_gallery_name.' = new FeatureCarousel("'.$this->carousel_wrapper_id.'");
                    '.$js_gallery_name.'.Initilize();
                });

            //]]>
            </script>';
        }

        return $header;
    }
}
?>

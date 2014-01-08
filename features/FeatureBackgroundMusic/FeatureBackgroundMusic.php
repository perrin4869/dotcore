<?php

/**
 * Feature used to embed background music inside a website
 * Requires the soundmanager component
 * @author perrin
 *
 */
class FeatureBackgroundMusic extends DotCoreFeature
{
    public function __construct(DotCoreFeatureRecord $record, $parameters = array())
    {
        parent::__construct($record, $parameters);

        $this->music_url = $this->GetParameter('music_url');

        // Register the header content
        $curr_page = DotCorePageRenderer::GetCurrent();
        $header_content = $this->GetHeaderContent();
        $curr_page->RegisterHeaderContent($header_content);

        self::$count_instances++;
    }

    private $music_url;

    private static $count_instances = 0;
	
    /**
     * Shows the contact form to the user
     *
     */
    public function GetFeatureContent()
    {
        $result = '';

        $result .= '
        <div id="bg-music-controller-container"></div>';

        return $result;
    }

    public function GetHeaderContent()
    {
        $header = '';
        $feature_path = $this->GetFeatureUrl();

        if(self::$count_instances == 0)
        {
            $header .= '
            <script type="text/javascript" src="' . $feature_path . '/feature_background_music.js"></script>';
        }

        $current_renderer = DotCorePageRenderer::GetCurrent();
        $current_renderer->LoadComponent('soundmanager');

        $header .= '
        <script type="text/javascript">
        //<![CDATA[
            addEvent(window, "load", function()
            {
                var backgroundMusic = new BackgroundMusic("'.$this->music_url.'", {
                    baseUrl: "'.$feature_path.'"
                });
            });
        //]]>
        </script>';

        return $header;
    }
}

?>
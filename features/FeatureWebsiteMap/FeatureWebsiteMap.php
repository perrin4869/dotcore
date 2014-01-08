<?php

/**
 * Feature used to print a map of the website
 * @author perrin
 *
 */
class FeatureWebsiteMap extends DotCoreFeature
{
    public function __construct(DotCoreFeatureRecord $record, $parameters = array())
    {
        parent::__construct($record, $parameters);
    }

    private function RecursiveMapWritter($pages, $pages_bll, $lang, &$result)
    {
        $result .= '<ul>';
        $count = count($pages);
        $lang_code = $lang->getLanguageCode();
        for($i = 0; $i < $count; $i++)
        {
            $url = '/' . $pages[$i]->getUrl() . '.'.DotCoreConfig::$DEFAULT_EXTENSION;
            
            if(!DotCorePageRenderer::IsDefaultLanguage($lang_code)) {
                $url = '/'.$lang->getLanguageCode().$url;
            }

            $result .= '<li'.$li_class.'>';
                $result .= '<a href="'.$url.'">'.$pages[$i]->getName().'</a>';
                $childs = $pages_bll->ByParentPageID($pages[$i]->getPageID())->Select();
                $count_childs = count($childs);
                if($count_childs > 0)
                {
                    $this->RecursiveMapWritter($childs, $pages_bll, $lang, $result, $current_page_id);
                }
            $result .= '</li>';
        }
        $result .= '</ul>';

    }

    /**
     * Shows the contact form to the user
     *
     */
    public function GetFeatureContent()
    {
        $result = '';

        $lang = DotCorePageRenderer::GetCurrent()->GetLanguage();
        $pages_bll = new DotCorePageBLL();
        $pages = $pages_bll
            ->Ordered()
            ->Fields(
                array(
                    $pages_bll->getFieldName(),
                    $pages_bll->getFieldTitle(),
                    $pages_bll->getFieldUrl()
                )
            )
            ->ByRootPages()
            ->AndBy()
            ->ByLanguageID($lang->getLanguageID())
            ->Select();

        $this->RecursiveMapWritter($pages, $pages_bll, $lang, $result);

        return $result;
    }
}

?>
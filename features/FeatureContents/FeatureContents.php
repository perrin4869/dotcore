<?php

/**
 * Feature used to get the contents of the current page
 * @author perrin
 *
 */
class FeatureContents extends DotCoreFeature
{
    public function __construct(DotCoreFeatureRecord $record, $parameters = array())
    {
        parent::__construct($record, $parameters);
    }
	
    /**
     * Shows the contact form to the user
     *
     */
    public function GetFeatureContent()
    {
        $result = '';

        $page_renderer = DotCorePageRenderer::GetCurrent();
        $page = $page_renderer->GetPageRecord();
        if($page != NULL) {
            $contents_bll = new DotCoreContentBLL();
            // TODO: Proper template
            $contents_records = $contents_bll
                ->Fields(array($contents_bll->getFieldText()))
                ->ByPageID($page->getPageID())
                ->Select();
            $count_contents_records = count($contents_records);
            for($i = 0; $i < $count_contents_records; $i++) {
                $result .= $contents_records[$i]->getContentText();
            }
        }
        else {
            $result .= '<div id="error-div">'.$page_renderer->GetErrorMessage().'</div>';
        }

        return $result;
    }
}

?>
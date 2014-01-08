<?php

/**
 * Feature used to search contents
 * @author perrin
 *
 */
class FeatureContentsSearch extends DotCoreFeature
{
    public function __construct(DotCoreFeatureRecord $record, $parameters = array())
    {
        parent::__construct($record, $parameters);

        $curr_page = DotCorePageRenderer::GetCurrent();
        if($curr_page && key_exists('search-input', $_REQUEST)) {
            $input = $_REQUEST['search-input'];

            $this->contents_bll = new DotCoreContentBLL();
            $pages_link = $this->contents_bll->LinkPages();
            $page_bll = new DotCorePageBLL();
            $page_path = new DotCoreDALPath(
                                array(
                                    DotCoreContentDAL::PAGE_CONTENTS_LINK
                                )
                            );

            $restraints = new DotCoreDALRestraint();
            $restraints
                ->AddRestraint(
                    new DotCoreFieldRestraint(
                        new DotCoreDALFieldPath(
                            $page_bll->getFieldPageLanguageID(),
                            $page_path
                        ),
                        $curr_page->GetPageRecord()->getPageLanguageID())
                )
                ->AddRestraint(
                    new DotCoreFulltextRestraint($this->contents_bll->getContentFulltext(), $input)
                );

            $this->results = $this->contents_bll
                ->Fields(
                    array(
                        $this->contents_bll->getFieldContentPageID(),
                        $this->contents_bll->getFieldText(),
                        new DotCoreDALFieldPath($page_bll->getFieldName(), $page_path),
                        new DotCoreDALFieldPath($page_bll->getFieldUrl(), $page_path),
                        new DotCoreDALFieldPath($page_bll->getFieldPageLanguageID(), $page_path)
                    )
                )
                ->Restraints($restraints)
                ->GroupBy(
                    array(
                        $this->contents_bll->getFieldContentPageID()
                    )
                )
                ->Select();

            DotCorePageRenderer::GetCurrent()->RegisterHeaderContent($this->GetHeaderContent());
        }
        
    }
    
    /**
     * Holds the results of the search
     * @var array
     */
    private $results = NULL;
	
    /**
     * Shows the contact form to the user
     *
     */
    public function GetFeatureContent()
    {
        $messages = $this->GetMessages();
        
        $html = '';
        $html .= '<h2>'.$messages['ResultsLabel'].'</h2>';
        if($this->results != NULL && !empty($this->results)) {
            $count_results = count($this->results);
            for($i = 0; $i < $count_results; $i++) {
                $curr_content = $this->results[$i];
                $curr_page = DotCoreContentBLL::GetPage($curr_content);
                
                $content_text = strip_tags($curr_content->getContentText());
                $content_length = strlen($content_text);
                if($content_length > 400) {
                    for($j = 400; $j < $content_length && $content_text[$j] != ' '; $j++) {
                    }
                    $content_text = substr($content_text, 0, $j).'...';

                }
                $keywords = explode(' ', str_replace(',',  ' ', $_REQUEST['search-input']));
                $count_keywords = count($keywords);
                for($j = 0; $j < $count_keywords; $j++)
                {
                    $content_text = preg_replace("/(".$keywords[$j].")/i", '<strong>${1}</strong>', $content_text);
                }
                $classes = array('search-result');
                if($i == 0) {
                    array_push($classes, 'first-result');
                }
                $html .= '
                    <div class="'.join(' ', $classes).'">
                        <h3>
                            <a href='.DotCorePageBLL::GetPagePath($curr_page).'>'.$curr_page->getName().'</a>
                        </h3>
                        <p>'.$content_text.'</p>
                    </div>';
            }
        }
        else {
            $html .= '<p>'.$messages['NoResults'].'</p>';
        }
        return $html;
    }

    private function GetHeaderContent() {
        return '
        <link type="text/css" rel="stylesheet" href="'.$this->GetFeatureUrl().'contents_search.css" />
        ';
    }

}

?>
<?php

/**
 * Feature used to print the menu of the website
 * @author perrin
 *
 */
class FeatureWebsiteMenu extends DotCoreFeature
{
	public function __construct(DotCoreFeatureRecord $record, $parameters = array())
	{
		parent::__construct($record, $parameters);
	}

	private function RecursiveNavWritter($pages, $pages_bll, $lang, &$result, $current_page_id)
	{
		$result .= '<ul>';
		$count = count($pages);
		$lang_code = $lang->getLanguageCode();
		for($i = 0; $i < $count; $i++)
		{
			$li_classes = array();
			$li_class = '';
			if($i == 0)
			{
				array_push($li_classes, 'first');
			}
			if($i == $count - 1) {
				array_push($li_classes, 'last');
			}
			if($pages[$i]->getPageID() == $current_page_id)
			{
				array_push($li_classes, 'current');
			}
			if(count($li_classes) > 0)
			{
				$li_class = ' class="'.join(' ', $li_classes).'"';
			}

			$url = '/' . $pages[$i]->getUrl() . '.'.DotCoreConfig::$DEFAULT_EXTENSION;

			if(!DotCorePageRenderer::IsDefaultLanguage($lang_code)) {
				$url = '/'.$lang->getLanguageCode().$url;
			}

			$result .= '<li'.$li_class.'>';
			$result .= '<a href="'.$url.'" accesskey="'.$i.'" title="'.$pages[$i]->getTitle().'">'.$pages[$i]->getName().'</a>';
			$childs = $pages_bll->ByParentPageThatAppearInNav($pages[$i]->getPageID())->Select();
			$count_childs = count($childs);
			if($count_childs > 0)
			{
				$this->RecursiveNavWritter($childs, $pages_bll, $lang, $result, $current_page_id);
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

		// Find the active parent ID
		$page_record = DotCorePageRenderer::GetCurrent()->GetPageRecord();
		$page_bll = new DotCorePageBLL();
		if($page_record != NULL)
		{
			$active_parent_id = $page_record->getPageID();
			$tmp_page = $page_record;
			$page_bll->Fields(
				array(
					$page_bll->getFieldPageParentID()
				)
			);

			while($tmp_page->getPageParentID() != NULL)
			{
				$tmp_page = $page_bll->GetParentPage($tmp_page);
				$active_parent_id = $tmp_page->getPageID();
			}
		}

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
			->ByRootPagesThatAppearInNavByLanguage($lang->getLanguageID())
			->Select();

		$this->RecursiveNavWritter($pages, $pages_bll, $lang, $result, $active_parent_id);

		return $result;
	}
}

?>

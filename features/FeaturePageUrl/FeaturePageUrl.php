<?php

/**
 * Feature used to get the url of the page described by the parameters
 * @author perrin
 *
 */
class FeaturePageUrl extends DotCoreFeature
{
	public function __construct(DotCoreFeatureRecord $record, $parameters = array())
	{
		parent::__construct($record, $parameters);

		if(isset($parameters['page_id']))
		{
			$page_bll = new DotCorePageBLL();
			$page = $page_bll
				->ByPageID($parameters['page_id'])
				->Fields(
					array(
						$page_bll->getFieldUrl(),
						$page_bll->getFieldPageLanguageID()
					)
				)
				->SelectFirstOrNull();
			
		}
		elseif(key_exists('current', $parameters)) {
			$page = DotCorePageRenderer::GetCurrent()->GetPageRecord();
		}

		if($page != NULL)
		{
			$languages = DotCoreLanguageBLL::GetLanguagesIDDictionary();
			$lang_code = $languages[$page->getPageLanguageID()]->getLanguageCode();
			if(!DotCorePageRenderer::IsDefaultLanguage($lang_code)) {
				$this->url = '/' . $lang_code . '/' . $page->getUrl() . '.' . DotCoreConfig::$DEFAULT_EXTENSION;
			}
			else {
				$this->url = '/' . $page->getUrl() . '.' . DotCoreConfig::$DEFAULT_EXTENSION;
			}
		}
		elseif(isset($parameters['page_url']) && isset($parameters['language_code']))
		{
			$this->url = '/' . $parameters['language_code'] . '/' . $parameters['page_url'] . '.' . DotCoreConfig::$DEFAULT_EXTENSION;
		}
		else
		{
			// By default it points to the root
			$this->url = '/';
			$lang_code = DotCorePageRenderer::GetCurrent()->GetLanguage()->getLanguageCode();
			if(!DotCorePageRenderer::IsDefaultLanguage($lang_code)) {
				$this->url .= $lang_code;
			}
		}
	}

	private $url = NULL;

	/**
	 * Shows the contact form to the user
	 *
	 */
	public function GetFeatureContent()
	{
		return $this->url;
	}
}

?>
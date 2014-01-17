<?php

/**
 * Feature used to get the markup for the meta tags of the webpage
 * @author perrin
 *
 */
class FeatureMetaTags extends DotCoreFeature
{
	public function __construct(DotCoreFeatureRecord $record, $parameters = array())
	{
		parent::__construct($record, $parameters);
	}
	
	/**
	 * Returns the meta tags for the webpage
	 *
	 */
	public function GetFeatureContent()
	{
		$configure = DotCorePageRenderer::GetConfiguration();
		$keywords_field = $configure->GetField('keywords');
		$description_field = $configure->GetField('website_description');
		$lang_code = DotCorePageRenderer::GetCurrent()->GetLanguage()->getLanguageCode();

		$result = '';
		$result .= '
		<meta name="ROBOTS" content="INDEX, FOLLOW" />
		<meta name="keywords" content= "'.$keywords_field->GetValue($lang_code).'" />
		<meta name="description" content="'.$description_field->GetValue($lang_code).'" />';
		return $result;
	}
}

?>
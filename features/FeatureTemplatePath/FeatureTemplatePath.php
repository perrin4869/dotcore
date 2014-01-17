<?php

/**
 * Feature used to get the url of the templates of the project
 * @author perrin
 *
 */
class FeatureTemplatePath extends DotCoreFeature
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
		return DotCorePageRenderer::GetCurrent()->GetTemplateFolderUrl();
	}
}

?>
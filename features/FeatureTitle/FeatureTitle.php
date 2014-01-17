<?php

/**
 * Feature used to get the title of the current page
 * @author perrin
 *
 */
class FeatureTitle extends DotCoreFeature
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
		return DotCorePageRenderer::GetCurrent()->GetTitle();
	}
}

?>
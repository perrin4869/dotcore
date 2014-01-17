<?php

/**
 * Feature used to embed PHP code
 * @author perrin
 *
 */
class FeaturePHPContent extends DotCoreFeature
{
	public function __construct(DotCoreFeatureRecord $record, $parameters = array(), $content = NULL)
	{
		parent::__construct($record, $parameters, $content);
	}
	
	/**
	 * Shows the contact form to the user
	 *
	 */
	public function GetFeatureContent()
	{
		return eval($this->getCreatingMarkupContent());
	}
}

?>
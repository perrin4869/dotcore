<?php

/**
 * Feature used to get the general content denoted by the name passed in the parameter
 * @author perrin
 *
 */
class FeatureGeneralContent extends DotCoreFeature
{
    public function __construct(DotCoreFeatureRecord $record, $parameters = array())
    {
        parent::__construct($record, $parameters);

        if(isset($parameters['name']))
        {
            $this->content = DotCorePageRenderer::GetCurrent()->GetGeneralContent($parameters['name']);
        }
    }

    private $content = NULL;
	
    /**
     * Shows the contact form to the user
     *
     */
    public function GetFeatureContent()
    {
        return $this->content;
    }
}

?>
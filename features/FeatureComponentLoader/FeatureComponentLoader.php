<?php

/**
 * Feature used to load components into the currently rendering webpage
 * @author perrin
 *
 */
class FeatureComponentLoader extends DotCoreFeature
{
    public function __construct(DotCoreFeatureRecord $record, $parameters = array())
    {
        parent::__construct($record, $parameters);

        if(isset($parameters['component_name']))
        {
            $page_renderer = DotCorePageRenderer::GetCurrent();
            $page_renderer->LoadComponent($parameters['component_name']);
        }
        else
        {
            throw new Exception('FeatureComponentLoader - Missing parameter - component name');
        }
    }
	
    /**
     * Shows the contact form to the user
     *
     */
    public function GetFeatureContent()
    {
        return '';
    }
}

?>
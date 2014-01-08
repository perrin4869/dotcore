<?php

/**
 * Feature used to get the code of the current language
 * @author perrin
 *
 */
class FeatureLanguageCode extends DotCoreFeature
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
        return DotCorePageRenderer::GetCurrent()->GetLanguage()->getLanguageCode();
    }
}

?>
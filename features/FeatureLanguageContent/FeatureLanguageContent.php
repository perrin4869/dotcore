<?php

/**
 * Feature used to embed content that will appear for a certain language only
 * @author perrin
 *
 */
class FeatureLanguageContent extends DotCoreFeature
{
    public function __construct(DotCoreFeatureRecord $record, $parameters = array(), $content = NULL)
    {
        parent::__construct($record, $parameters, $content);

        $lang = DotCorePageRenderer::GetCurrent()->GetLanguage();

        if(isset($parameters['language_code']))
        {
            $this->embed = $lang->getLanguageCode() == $parameters['language_code'];
        }
        elseif(isset($parameters['language_id']))
        {
            $this->embed = $lang->getLanguageID() == $parameters['language_id'];
        }
        elseif(isset($parameters['dir']))
        {
            $dir = ($parameters['dir'] == 'ltr') ? DotCoreLanguageDAL::LANGUAGES_DIRECTION_LTR : DotCoreLanguageDAL::LANGUAGES_DIRECTION_RTL;
            $this->embed = $lang->getLanguageDirection() == $dir;
        }
    }

    private $embed = FALSE;
	
    /**
     * Shows the contact form to the user
     *
     */
    public function GetFeatureContent()
    {
        if($this->embed)
        {
            return $this->getCreatingMarkupContent();
        }
    }
}

?>
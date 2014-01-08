<?php

/**
 * Feature used to get a configuration value from the template configuration
 * @author perrin
 *
 */
class FeatureTemplateConfiguration extends DotCoreFeature
{
    public function __construct(DotCoreFeatureRecord $record, $parameters = array())
    {
        parent::__construct($record, $parameters);
    }

    /**
     *
     * @var DotCoreConfiguration
     */
    private static $template_configuration = NULL;

    public static function GetTemplateConfiguration() {
        if(self::$template_configuration == NULL) {
            self::$template_configuration = new DotCoreConfiguration(DotCorePageRenderer::GetCurrent()->GetTemplateFolderPath().'configuration.php');
        }
        return self::$template_configuration;
    }

    /**
     * Shows the contact form to the user
     *
     */
    public function GetFeatureContent()
    {
        $field = self::GetTemplateConfiguration()->GetField($this->GetParameter('field'));
        if($field) {
            if($field->GetAttribute('multilang')) {
                $result = $field->GetValue(DotCorePageRenderer::GetCurrent()->GetLanguage()->getLanguageCode());
            }
            else {
                $result = $field->GetValue();
            }
        }
        else {
            $result = 'Invalid field.';
        }
        return $result;
    }
}

?>
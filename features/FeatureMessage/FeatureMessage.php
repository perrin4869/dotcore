<?php

/**
 * Feature used to get the message denoted by the name passed in the parameter
 * @author perrin
 *
 */
class FeatureMessage extends DotCoreFeature
{
    public function __construct(DotCoreFeatureRecord $record, $parameters = array())
    {
        parent::__construct($record, $parameters);

        if(isset($parameters['key']))
        {
            $messages = self::GetTemplateMessages();
            $this->message = $messages[$parameters['key']];
        }
    }

    private static $messages = NULL;
    private $message = NULL;

    public static function GetTemplateMessages()
    {
        if(self::$messages === NULL)
        {
            $page_renderer = DotCorePageRenderer::GetCurrent();
            $path = DotCorePageRenderer::GetCurrent()->GetTemplateFolderPath().'lang.php';
            if(is_file($path)) {
                $lang_code = $page_renderer->GetLanguage()->getLanguageCode();
                self::$messages = new DotCoreMessages($path, array($lang_code));
            }
            else {
                self::$messages = new DotCoreMessages();
            }
            
        }
        return self::$messages;
    }
	
    /**
     * Shows the contact form to the user
     *
     */
    public function GetFeatureContent()
    {
        return htmlspecialchars($this->message);
    }
}

?>
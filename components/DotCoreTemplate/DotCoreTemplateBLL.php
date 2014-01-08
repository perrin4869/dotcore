<?php

/**
 * DotCoreTemplateBLL - Contains the business logic behind the templates
 *
 * @author perrin
 */
class DotCoreTemplateBLL extends DotCoreBLL {

    /*
     *
     * Abstract Methods Implementation:
     *
     */

    private static $templates_configurations = array();
    private static $templates_messages = array();

    /**
     *
     * @return DotCoreTemplateDAL
     */
    public static function GetDAL() {
        return self::GetDALHelper('DotCoreTemplateDAL');
    }

    /*
     *
     * Fields accessors:
     *
     */

    /**
     * Gets the auto incrementing ID of this DAL
     * @return DotCoreAutoIncrementingKey
     */
    public function getFieldTemplateID()
    {
        return $this->GetDAL()->GetField(DotCoreTemplateDAL::TEMPLATE_ID);
    }

    /**
     * 
     * @return DotCorePlainStringField
     */
    public function getFieldFolder()
    {
        return $this->GetDAL()->GetField(DotCoreTemplateDAL::TEMPLATE_FOLDER);
    }

    /**
     * 
     * @return DotCorePlainStringField
     */
    public function getFieldName()
    {
        return $this->GetDAL()->GetField(DotCoreTemplateDAL::TEMPLATE_NAME);
    }

    /*
     *
     * Helper methods:
     *
     */

    /**
     *
     * @param int $id
     * @return DotCoreTemplateBLL
     */
    public function ByTemplateID($id) {
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldTemplateID(), $id));

        return $this->Restraints($restraints);
    }

    public static function GetTemplateFolderPath(DotCoreTemplateRecord $template) {
        return DotCoreConfig::$LOCAL_TEMPLATES_PATH.$template->getTemplateFolder().'/';
    }

    /**
     *
     * @param DotCoreTemplateRecord $template
     * @return DotCoreConfiguration
     */
    public static function GetTemplateConfiguration(DotCoreTemplateRecord $template) {
        $template_folder = $template->getTemplateFolder();
        if(!key_exists($template_folder, self::$templates_configurations)) {
            $config_path = self::GetTemplateFolderPath($template).'configuration.php';
            if(is_file($config_path)) {
                self::$templates_configurations[$template_folder] = new DotCoreConfiguration($config_path);
            }
            else {
                self::$templates_configurations[$template_folder] = NULL;
            }
        }
        return self::$templates_configurations[$template_folder];
    }

    /**
     *
     * @param DotCoreTemplateRecord $template
     * @param array $languages
     * @return DotCoreMessages
     */
    public static function GetTemplateMessages(DotCoreTemplateRecord $template, $languages = NULL) {
        $template_folder = $template->getTemplateFolder();
        if(!key_exists($template_folder, self::$templates_messages)) {
            $msg_path = self::GetTemplateFolderPath($template).'lang.php';
            if(is_file($msg_path)) {
                self::$templates_messages[$template_folder] = new DotCoreMessages($msg_path, $languages);
            }
            else {
                self::$templates_messages[$template_folder] = NULL;
            }
        }
        return self::$templates_messages[$template_folder];
    }

}
?>

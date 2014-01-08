<?php

/**
 * DotCoreTemplateRecord represents one record of template from a DAL
 *
 * @author perrin
 */
class DotCoreTemplateRecord extends DotCoreDataRecord {

    /**
     * Constructor for Template record
     *
     * @param DotCoreDAL $dal
     */
    public function  __construct(DotCoreDAL $dal) {
        parent::__construct($dal);
    }

    /*
     *
     * Accessors:
     *
     */
    
    /*
     * Getters:
     */

    public function getTemplateID() {
        return $this->GetField(DotCoreTemplateDAL::TEMPLATE_ID);
    }

    public function getTemplateFolder() {
        return $this->GetField(DotCoreTemplateDAL::TEMPLATE_FOLDER);
    }

    public function getTemplateName() {
        return $this->GetField(DotCoreTemplateDAL::TEMPLATE_NAME);
    }

    /*
     * Setters:
     */


    private function setTemplateID($val) {
        $this->SetField(DotCoreTemplateDAL::TEMPLATE_ID, $val);
    }

    public function setTemplateFolder($folder) {
        $this->SetField(DotCoreTemplateDAL::TEMPLATE_FOLDER, $folder);
    }

    public function setTemplateName($name) {
        $this->SetField(DotCoreTemplateDAL::TEMPLATE_NAME, $name);
    }

}
?>

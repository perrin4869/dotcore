<?php

/**
 * DotCoreGeneralContentRecord represents one record of general contents from a DAL
 *
 * @author perrin
 */
class DotCoreGeneralContentRecord extends DotCoreDataRecord {

    /**
     * Constructor for General Content records
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

    public function getGeneralContentID() {
        return $this->GetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_ID);
    }

    public function getName() {
        return $this->GetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_NAME);
    }

    public function getDescription() {
        return $this->GetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_DESCRIPTION);
    }

    public function getContentType() {
        return $this->GetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_CONTENT_TYPE);
    }

    public function getContentOrder() {
        return $this->GetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_ORDER);
    }

    /*
     * Setters:
     */


    private function setGeneralContentID($val) {
        $this->SetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_ID, $val);
    }

    public function setName($name) {
        $this->SetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_NAME, $name);
    }

    public function setDescription($desc) {
        $this->SetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_DESCRIPTION, $desc);
    }

    public function setContentType($content_type) {
        $this->SetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_CONTENT_TYPE, $content_type);
    }

    public function setContentOrder($order) {
        $this->SetField(DotCoreGeneralContentDAL::GENERAL_CONTENTS_ORDER, $order);
    }

}
?>

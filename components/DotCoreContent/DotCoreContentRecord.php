<?php

/**
 * DotCoreContentRecord represents one record of contents from a DAL
 *
 * @author perrin
 */
class DotCoreContentRecord extends DotCoreDataRecord {

    /**
     * Constructor for Contents records
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

    public function getContentTemplateContentID() {
        return $this->GetField(DotCoreContentDAL::CONTENT_TEMPLATE_CONTENT_ID);
    }

    public function getContentPageID() {
            return $this->GetField(DotCoreContentDAL::CONTENT_PAGE_ID);
    }

    public function getContentText() {
        return $this->GetField(DotCoreContentDAL::CONTENT_TEXT);
    }


	/*
	* Setters:
	*/


    public function setContentTemplateContentID($val) {
        $this->SetField(DotCoreContentDAL::CONTENT_TEMPLATE_CONTENT_ID, $val);
    }

    public function setContentPageID($page_id) {
        $this->SetField(DotCoreContentDAL::CONTENT_PAGE_ID, $page_id);
    }

    public function setContentText($text) {
        $this->SetField(DotCoreContentDAL::CONTENT_TEXT, $text);
    }

}
?>

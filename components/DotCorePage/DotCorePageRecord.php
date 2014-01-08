<?php

/**
 * DotCorePageRecord represents one record of pages from a DotCorePageDAL
 *
 * @author perrin
 */
class DotCorePageRecord extends DotCoreDataRecord {

    /**
     * Constructor for Page record
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

    public function getPageID() {
        return $this->GetField(DotCorePageDAL::PAGE_ID);
    }

    public function getName() {
        return $this->GetField(DotCorePageDAL::PAGE_NAME);
    }

    public function getUrl() {
        return $this->GetField(DotCorePageDAL::PAGE_URL);
    }

    public function getTitle() {
        return $this->GetField(DotCorePageDAL::PAGE_TITLE);
    }

    public function getHeaderContent() {
        return $this->GetField(DotCorePageDAL::PAGE_HEADER_CONTENT);
    }

    public function getOrder() {
        return $this->GetField(DotCorePageDAL::PAGE_ORDER);
    }

    public function getPageParentID() {
        return $this->GetField(DotCorePageDAL::PAGE_PARENT_ID);
    }

    public function getPageAppearsInNav() {
        return $this->GetField(DotCorePageDAL::PAGE_APPEARS_IN_NAV);
    }

    public function getPageLanguageID() {
        return $this->GetField(DotCorePageDAL::PAGE_LANGUAGE);
    }

	/*
	* Setters:
	*/

    private function setPageID($val) {
        $this->SetField(DotCorePageDAL::PAGE_ID, $val);
    }

    public function setName($name) {
        $this->SetField(DotCorePageDAL::PAGE_NAME, $name);
    }

    public function setUrl($url) {
        $this->SetField(DotCorePageDAL::PAGE_URL, $url);
    }

    public function setTitle($title) {
        $this->SetField(DotCorePageDAL::PAGE_TITLE, $title);
    }

    public function setHeaderContent($content) {
        $this->SetField(DotCorePageDAL::PAGE_HEADER_CONTENT, $content);
    }

    public function setOrder($order) {
        $this->SetField(DotCorePageDAL::PAGE_ORDER, $order);
    }

    public function setPageParentID($parent_id) {
        $this->SetField(DotCorePageDAL::PAGE_PARENT_ID, $parent_id);
    }

    public function setPageAppearsInNav($appears_in_nav) {
        $this->SetField(DotCorePageDAL::PAGE_APPEARS_IN_NAV, $appears_in_nav);
    }

    public function setPageLanguageID($lang_id) {
        $this->SetField(DotCorePageDAL::PAGE_LANGUAGE, $lang_id);
    }

}
?>

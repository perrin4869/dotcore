<?php

/**
 * DotCoreLinkRecord represents one record of links from a DAL
 *
 * @author perrin
 */
class DotCoreLinkRecord extends DotCoreDataRecord {

    /**
     * Constructor for Links record
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

    public function getLinkID() {
        return $this->GetField(DotCoreLinkDAL::LINK_ID);
    }

    public function getLinkTitle() {
        return $this->GetField(DotCoreLinkDAL::LINK_TITLE);
    }

    public function getLinkUrl() {
        return $this->GetField(DotCoreLinkDAL::LINK_URL);
    }

    public function getLinkDescription() {
        return $this->GetField(DotCoreLinkDAL::LINK_DESCRIPTION);
    }

    public function getLinkLogo() {
        return $this->GetField(DotCoreLinkDAL::LINK_LOGO);
    }

    public function getLinkLogoPath() {
        return $this->GetDAL()->GetField(DotCoreLinkDAL::LINK_LOGO)->getDestinationFolder() . $this->getLinkLogo();
    }

    public function getLinkLanguageID() {
        return $this->GetField(DotCoreLinkDAL::LINK_LANGUAGE_ID);
    }

    public function getLinkOrder() {
        return $this->GetField(DotCoreLinkDAL::LINK_ORDER);
    }

    /*
     * Setters:
     */


    private function setLinkID($val) {
        $this->SetField(DotCoreLinkDAL::LINK_ID, $val);
    }

    public function setLinkTitle($title) {
        $this->SetField(DotCoreLinkDAL::LINK_TITLE, $title);
    }

    public function setLinkUrl($url) {
        $this->SetField(DotCoreLinkDAL::LINK_URL, $url);
    }

    public function setLinkLogo($logo) {
        $this->SetField(DotCoreLinkDAL::LINK_LOGO, $logo);
    }

    public function setLinkDescription($desc) {
        $this->SetField(DotCoreLinkDAL::LINK_DESCRIPTION, $desc);
    }

    public function setLinkLanguageID($language_id) {
        $this->SetField(DotCoreLinkDAL::LINK_LANGUAGE_ID, $language_id);
    }

    public function setLinkOrder($order) {
        $this->SetField(DotCoreLinkDAL::LINK_ORDER, $order);
    }

}
?>

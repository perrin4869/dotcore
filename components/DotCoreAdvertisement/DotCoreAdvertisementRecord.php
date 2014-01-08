<?php

/**
 * DotCoreAdvertisementRecord represents one record of ads from a DAL
 *
 * @author perrin
 */
class DotCoreAdvertisementRecord extends DotCoreDataRecord {

    /**
     * Constructor for Advertisements record
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

    public function getAdvertisementID() {
        return $this->GetField(DotCoreAdvertisementDAL::ADVERTISEMENT_ID);
    }

    public function getAdvertisementText() {
        return $this->GetField(DotCoreAdvertisementDAL::ADVERTISEMENT_TEXT);
    }

    public function getAdvertisementMediaName() {
        return $this->GetField(DotCoreAdvertisementDAL::ADVERTISEMENT_MEDIA_NAME);
    }

    public function getAdvertisementUrl() {
        return $this->GetField(DotCoreAdvertisementDAL::ADVERTISEMENT_URL);
    }

    public function getAdvertisementIsActive() {
        return $this->GetField(DotCoreAdvertisementDAL::ADVERTISEMENT_IS_ACTIVE);
    }


	/*
	* Setters:
	*/


    private function setID($val) {
        $this->SetField(DotCoreAdvertisementDAL::ADVERTISEMENT_ID, $val);
    }

    public function setAdvertisementText($text) {
        $this->SetField(DotCoreAdvertisementDAL::ADVERTISEMENT_TEXT, $title);
    }

    public function setAdvertisementMediaName($media_name) {
        $this->SetField(DotCoreAdvertisementDAL::ADVERTISEMENT_MEDIA_NAME, $media_name);
    }

    public function setAdvertisementUrl($url) {
        $this->SetField(DotCoreAdvertisementDAL::ADVERTISEMENT_URL, $url);
    }

    public function setAdvertisementIsActive($is_active) {
        $this->SetField(DotCoreAdvertisementDAL::ADVERTISEMENT_IS_ACTIVE, $is_active);
    }

}
?>

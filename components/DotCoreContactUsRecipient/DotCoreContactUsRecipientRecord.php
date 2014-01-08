<?php

/**
 * DotCoreContactUsRecipientRecord represents one record of contact us recipients from a DAL
 *
 * @author perrin
 */
class DotCoreContactUsRecipientRecord extends DotCoreDataRecord {

    /**
     * Constructor for Contact Us Recipient record
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

    public function getContactUsRecipientID() {
        return $this->GetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_ID);
    }

    public function getContactUsRecipientName() {
        return $this->GetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_NAME);
    }

    public function getContactUsRecipientEmail() {
        return $this->GetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_EMAIL);
    }

    public function getContactUsRecipientLanguageID() {
        return $this->GetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_LANGUAGE_ID);
    }

    /*
     * Setters:
     */


    private function setContactUsRecipientID($val) {
        $this->SetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_ID, $val);
    }

    public function setContactUsRecipientName($name) {
        $this->SetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_NAME, $name);
    }

    public function setContactUsRecipientEmail($email) {
        $this->SetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_EMAIL, $email);
    }

    public function setContactUsRecipientLanguageID($lang_id) {
        $this->SetField(DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_LANGUAGE_ID, $lang_id);
    }

}
?>

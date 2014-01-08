<?php

/**
 * DotCoreContactMemberRecord represents one record of contact members from a DAL
 *
 * @author perrin
 */
class DotCoreContactMemberRecord extends DotCoreDataRecord {

    /**
     * Constructor for Contact Members record
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

    public function getContactMemberID() {
        return $this->GetField(DotCoreContactMemberDAL::CONTACT_MEMBER_ID);
    }

    public function getContactMemberEmail() {
        return $this->GetField(DotCoreContactMemberDAL::CONTACT_MEMBER_EMAIL);
    }

    public function getContactMemberDateAdded() {
        return $this->GetField(DotCoreContactMemberDAL::CONTACT_MEMBER_DATE_ADDED);
    }

    public function getContactMemberLanguageID() {
        return $this->GetField(DotCoreContactMemberDAL::CONTACT_MEMBER_LANGUAGE_ID);
    }


    /*
     * Setters:
     */


    private function setContactMemberID($val) {
        $this->SetField(DotCoreContactMemberDAL::CONTACT_MEMBER_ID, $val);
    }

    public function setContactMemberEmail($email) {
        $this->SetField(DotCoreContactMemberDAL::CONTACT_MEMBER_EMAIL, $email);
    }

    public function setContactMemberDateAdded($date_added) {
        $this->SetField(DotCoreContactMemberDAL::CONTACT_MEMBER_DATE_ADDED, $date_added);
    }

    public function setContactMemberLanguageID($lang_id) {
        $this->SetField(DotCoreContactMemberDAL::CONTACT_MEMBER_LANGUAGE_ID, $lang_id);
    }

}
?>

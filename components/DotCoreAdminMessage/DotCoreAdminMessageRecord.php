<?php

/**
 * DotCoreAdminMessageRecord
 *
 * @author perrin
 */
class DotCoreAdminMessageRecord extends DotCoreDataRecord {

    /**
     * Constructor for Admin Message
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

    public function getAdminMessageID() {
        return $this->GetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_ID);
    }

    public function getAdminMessageText() {
        return $this->GetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_TEXT);
    }

    public function getAdminMessageAdminID() {
        return $this->GetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_ADMIN_ID);
    }

    public function getAdminMessageDateTime() {
        return $this->GetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_DATETIME);
    }

    /*
     * Setters:
     */

    public function setAdminMessageID($id) {
        $this->SetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_ID, $id);
    }

    public function setAdminMessageText($text) {
        $this->SetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_TEXT, $text);
    }

    public function setAdminMessageAdminID($admin_id) {
        $this->SetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_ADMIN_ID, $admin_id);
    }

    public function setAdminMessageDateTime($datetime) {
        $this->SetField(DotCoreAdminMessageDAL::ADMIN_MESSAGE_DATETIME, $datetime);
    }

}
?>

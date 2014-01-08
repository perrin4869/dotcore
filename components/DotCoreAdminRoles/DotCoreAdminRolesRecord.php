<?php

/**
 * DotCoreAdminRolesRecord - Defines a single record of an admin's role obtained by DotCoreAdminRolesDAL
 *
 * @author perrin
 */
class DotCoreAdminRolesRecord extends DotCoreDataRecord {

    /**
     * Constructor for Admin's Role records
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

    public function getRoleID() {
        return $this->GetField(DotCoreAdminRolesDAL::ADMIN_ROLES_ROLE_ID);
    }

    public function getAdminID() {
        return $this->GetField(DotCoreAdminRolesDAL::ADMIN_ROLES_ADMIN_ID);
    }

    /*
     * Setters:
     */

    private function setRoleID($val) {
        $this->SetField(DotCoreAdminRolesDAL::ADMIN_ROLES_ROLE_ID, $val);
    }

    private function setAdminID($val) {
        $this->SetField(DotCoreAdminRolesDAL::ADMIN_ROLES_ADMIN_ID, $val);
    }

}
?>

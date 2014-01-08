<?php

/**
 * DotCoreDALSelectionOrderUnit represents a unit inside a DotCoreDALSelectionOrder object
 * Many of these can be added into a DotCoreDALSelectionOrder in order to create multiple ordering levels
 *
 * @author perrin
 */
abstract class DotCoreDALSelectionOrderUnit {

    /**
     * Constructor for DotCoreDALSelectionOrderUnit
     */
    public function  __construct() {
        
    }

    /**
     * Returns the SQL statement that is used to achieve the order described by this unit
     */
    abstract public function GetStatement();

}
?>

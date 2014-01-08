<?php

/**
 * DotCoreFeaturesCommandsRecord represents one record of feature command from a DAL
 *
 * @author perrin
 */
class DotCoreFeaturesCommandsRecord extends DotCoreDataRecord {

    /**
     * Constructor for Feature Commands records
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

    public function getFeatureID() {
        return $this->GetField(DotCoreFeaturesCommandsDAL::FEATURES_COMMANDS_FEATURE_ID);
    }

    public function getFeatureCommand() {
        return $this->GetField(DotCoreFeaturesCommandsDAL::FEATURES_COMMANDS_COMMAND);
    }

    /*
     * Setters:
     */


    private function setFeatureID($val) {
        $this->SetField(DotCoreFeaturesCommandsDAL::FEATURES_COMMANDS_FEATURE_ID, $val);
    }

    public function setFeatureCommand($command) {
        $this->SetField(DotCoreFeaturesCommandsDAL::FEATURES_COMMANDS_COMMAND, $command);
    }

}
?>

<?php

/**
 * DotCoreFeatureRecord represents one record of feature from a DAL
 *
 * @author perrin
 */
class DotCoreFeatureRecord extends DotCoreDataRecord {

    /**
     * Constructor for Feature records
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
        return $this->GetField(DotCoreFeatureDAL::FEATURE_ID);
    }

    public function getFeatureName() {
        return $this->GetField(DotCoreFeatureDAL::FEATURE_NAME);
    }

    public function getFeatureClass() {
        return $this->GetField(DotCoreFeatureDAL::FEATURE_CLASS);
    }

    public function getFeatureServerPath() {
        return $this->GetField(DotCoreFeatureDAL::FEATURE_SERVER_PATH);
    }

    public function getFeatureDomainPath() {
        return $this->GetField(DotCoreFeatureDAL::FEATURE_DOMAIN_PATH);
    }

    /*
     * Setters:
     */


    private function setFeatureID($val) {
        $this->SetField(DotCoreFeatureDAL::FEATURE_ID, $val);
    }

    public function setFeatureName($name) {
        $this->SetField(DotCoreFeatureDAL::FEATURE_NAME, $name);
    }

    public function setFeatureClass($class) {
        $this->SetField(DotCoreFeatureDAL::FEATURE_CLASS, $class);
    }

    public function setFeatureServerPath($path) {
        $this->SetField(DotCoreFeatureDAL::FEATURE_SERVER_PATH, $path);
    }

    public function setFeatureDomainPath($path) {
        $this->SetField(DotCoreFeatureDAL::FEATURE_DOMAIN_PATH, $path);
    }
    

}
?>

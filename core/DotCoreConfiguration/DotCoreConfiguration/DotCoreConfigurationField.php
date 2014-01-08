<?php
// +------------------------------------------------------------------------+
// | DotCoreConfigurationField.php                                          |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2009. All rights reserved.               |
// | Version       0.01                                                     |
// | Last modified 01/03/2010                                               |
// | Email         juliangrinblat@gmail.com                                 |
// | Web           http://www.dotcore.co.il                                 |
// +------------------------------------------------------------------------+

/**
 * Class DotCoreConfigurationField
 * Defines basic functionality for DotCoreConfiguration fields
 *
 * @version   0.01
 * @author    Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
abstract class DotCoreConfigurationField extends DotCoreObject {

    /**
     *
     * @param string $name Name of the field
     * @param array $config_arr The array as declared in the configuration file
     */
    public function __construct($name, $config_arr) {
        $this->name = $name;
        $this->array = $config_arr;

        if($this->array[DotCoreConfiguration::FIELD_ATTRIBUTES] === NULL) {
            $this->array[DotCoreConfiguration::FIELD_ATTRIBUTES] = array();
        }
    }

    private $name = NULL;
    private $array = NULL;

    protected static $default_attribute_values = array(
        'editable' => TRUE
    );

    protected function GetDefaultAttributeValue($attribute) {
        return self::$default_attribute_values[$attribute];
    }

    public function GetName() {
        return $this->name;
    }

    public function GetValue() {
        return $this->array[DotCoreConfiguration::FIELD_VALUE];
    }

    /**
     *
     * @param mixed $val
     */
    public function SetValue($val) {
        if($this->Validate($val)) { 
            $this->array[DotCoreConfiguration::FIELD_VALUE] = $val;
        }
    }

    /**
     *
     * @return array
     */
    public function GetAttributes() {
        return $this->array[DotCoreConfiguration::FIELD_ATTRIBUTES];
    }

    public function GetAttribute($attr) {
        if(key_exists($attr, $this->array[DotCoreConfiguration::FIELD_ATTRIBUTES])){
            return $this->array[DotCoreConfiguration::FIELD_ATTRIBUTES][$attr];
        }
        else {
            return $this->GetDefaultAttributeValue($attr);
        }
    }
    
    public function GetType() {
        return $this->array[DotCoreConfiguration::FIELD_TYPE];
    }

    public function SerializeToArray() {
        $config_var = '$config[\''.$this->GetName().'\']';
        $result = '';
        $result .= $config_var.' = array();
'.$config_var.'[DotCoreConfiguration::FIELD_VALUE] = '.$this->SerializeValue().';
'.$config_var.'[DotCoreConfiguration::FIELD_TYPE] = \''.$this->GetType().'\';
';

        $attributes = $this->array[DotCoreConfiguration::FIELD_ATTRIBUTES];
        if(!empty($attributes)) {
            $attr_serialize_arr = array();
            $result .= $config_var.'[DotCoreConfiguration::FIELD_ATTRIBUTES] = array(';
            foreach($attributes as $key => $attribute) {
                if(is_string($attribute)) {
                    $val = '\''.$attribute.'\'';
                }
                elseif(is_bool($attribute)) {
                    $val = $attribute ? 'TRUE' : 'FALSE';
                }
                else {
                    $val = $attribute;
                }
                array_push($attr_serialize_arr, '\''.$key.'\'=>'.$val);
            }
            $result .= join(',', $attr_serialize_arr) . ');
';
        }
        $result .= '
';
        return $result;
    }

    public function Validate(&$val) {
        return TRUE;
    }

    abstract protected function SerializeValue();

}

?>
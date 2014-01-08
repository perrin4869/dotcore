<?php
// +------------------------------------------------------------------------+
// | DotCoreStringConfigurationField.php                                    |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2009. All rights reserved.               |
// | Version       0.01                                                     |
// | Last modified 01/03/2010                                               |
// | Email         juliangrinblat@gmail.com                                 |
// | Web           http://www.dotcore.co.il                                 |
// +------------------------------------------------------------------------+

/**
 * Class DotCoreStringConfigurationField
 *
 * @version   0.01
 * @author    Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
class DotCoreStringConfigurationField extends DotCoreConfigurationField {

    public function GetValue($lang = NULL) {
        if($lang != NULL && $this->GetAttribute('multilang') == TRUE) {
            $values = parent::GetValue();
            return $values[$lang];
        }
        return parent::GetValue();
    }

    protected static $default_attribute_values = array(
        'multilang' => FALSE,
        'multiline' => FALSE
    );

    protected function GetDefaultAttributeValue($attribute) {
        if(key_exists($attribute, self::$default_attribute_values)) {
            return self::$default_attribute_values[$attribute];
        }
        else {
            return parent::GetDefaultAttributeValue($attribute);
        }
    }

    protected function GetSerializedString($str) {
        return '\''.str_replace('\'', '\\\'', $str).'\'';
    }

    protected function SerializeValue() {
        if(!$this->GetAttribute('multilang')) {
            return $this->GetSerializedString($this->GetValue()); // No fancy serialization here
        }
        else {
            $result = 'array(';
            $val = $this->GetValue();
            $multilang_val = array();
            foreach($val as $lang_code => $lang_val) {
                array_push(
                    $multilang_val,
                        $this->GetSerializedString($lang_code).
                        '=>'.
                        $this->GetSerializedString($lang_val));
            }
            $result .= join(",", $multilang_val);
            $result .= ')';
            return $result;
        }
    }

}

?>
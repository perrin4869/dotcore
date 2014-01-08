<?php
// +------------------------------------------------------------------------+
// | DotCoreIntConfigurationField.php                                       |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2009. All rights reserved.               |
// | Version       0.01                                                     |
// | Last modified 01/03/2010                                               |
// | Email         juliangrinblat@gmail.com                                 |
// | Web           http://www.dotcore.co.il                                 |
// +------------------------------------------------------------------------+

/**
 * Class DotCoreIntConfigurationField
 *
 * @version   0.01
 * @author    Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
class DotCoreIntConfigurationField extends DotCoreConfigurationField {

    public function Validate(&$val) {
        $parent_result = parent::Validate($val);
        
        if(!(empty($val) && $val !== 0 && $val !== '0')) {
            if(!is_numeric($val)) {
                throw new IntParseException();
            }
            else {
                $val = intval($val);
            }
        }

        return TRUE && $parent_result;
    }

    protected function SerializeValue() {
        return $this->GetValue(); // No fancy serialization here
    }

}

?>
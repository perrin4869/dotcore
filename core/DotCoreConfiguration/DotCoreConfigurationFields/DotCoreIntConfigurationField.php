<?php

/**
 * Class DotCoreIntConfigurationField
 *
 * @version   0.01
 * @author	Julian Grinblat <juliangrinblat@gmail.com>
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

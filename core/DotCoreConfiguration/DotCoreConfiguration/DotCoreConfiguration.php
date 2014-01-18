<?php

/**
 * Class DotCoreConfiguration
 * Implements an API for the management of configuration files
 *
 * @version   0.01
 * @author	Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
class DotCoreConfiguration extends DotCoreObject {

	public function __construct($filename) {
		$this->filename = $filename;
		$this->Parse();
	}

	protected function Parse() {
		include($this->filename);

		foreach($config as $name=>$field) {
			$type = $field[self::FIELD_TYPE];
			if(key_exists($type, self::$types_classes_dictionary)) {
				$type_class = self::$types_classes_dictionary[$type];
				$field = new $type_class($name, $field);
				$this->AddField($field);
			}
			else {
				throw new InvalidConfigurationTypeException();
			}
		}
	}

	// Field declaration
	private $filename = NULL;
	private $fields = array();
	
	private static $types_classes_dictionary = array();

	// Constant values for configuration files
	const FIELD_TYPE = 'type';
	const FIELD_ATTRIBUTES = 'attributes';
	const FIELD_VALUE = 'value';

	public static function AddType($type, $classname) {
		self::$types_classes_dictionary[$type] = $classname;
	}

	public static function RemoveType($type) {
		unset(self::$type_classes_dictionary[$type]);
	}

	public static function HasType($type) {
		return key_exists($type, self::$type_classes_dictionary);
	}

	public static function GetTypes() {
		return self::$type_classes_dictionary;
	}

	public function AddField(DotCoreConfigurationField $field) {
		$this->fields[$field->GetName()] = $field;
	}

	public function GetField($field_name) {
		return $this->fields[$field_name];
	}

	public function GetFields() {
		return $this->fields;
	}

	public function GetValue($field_name) {
		return $this->fields[$field_name]->GetValue();
	}

	public function SaveChanges($filename = NULL) {
		if($filename === NULL) {
			$filename = $this->filename;
		}

		$result = '';
		$result .= '<?php

$config = array();

';

		foreach($this->fields as $field) {
			$result .= $field->SerializeToArray();
		}

		$result .= '?>';

		file_put_contents($filename, $result);
	}

}

class InvalidConfigurationTypeException extends DotCoreException {}

?>

<?php

/**
 * DotCoreMultipleCheckBoxFormElement - Defines a form element used for mutichoice input
 *
 * @author perrin
 */
class DotCoreMultipleCheckBoxFormElement extends DotCoreInputFormElement {

	/**
	 * Constructor for multiple checkboxes form element
	 * @param string $name
	 * @param string $label
	 * @param array $dictionary - An array where the keys are the values of the elements, and the values are the labels
	 */
	public function  __construct($name, $dictionary) {
		parent::__construct($name);

		$this->dictionary = $dictionary;
		$this->AddClass('input-checkbox-wrapper');
	}

	/**
	 * Holds a dictionary in which the keys are the values of the checkboxes,
	 * and the values are the labels of the checkboxes
	 * @var array
	 */
	private $dictionary = NULL;

	public function GetDictionary() {
		return $this->dictionary;
	}

	public function SetDictionary($dictionary) {
		$this->dictionary = $dictionary;
	}

	public function __toString() {
		$result = '';
		$values = $this->GetSavedValue();
		foreach($this->dictionary as $key=>$label) {
			$checked = key_exists($key, $values) ? 'checked="checked"' : '';
			$id = $this->GetID().$key;
			$result .= '
			<input type="hidden" name="'.$this->GetName().'_hidden" id="'.$this->GetID().'_hidden" />
			<div class="'.$this->GetClass().'">
				<input
					type="checkbox"
					name="'.$this->GetName().'['.$key.']"
					id="'.$id.'"'.$checked.' />
				<label for="'.$id.'">'.$label.'</label>
			</div>';
		}
		
		return $result;
	}

	/**
	 * Checks whether the value for this element was submitted
	 * @return boolean
	 */
	public function IsValueSet() {
		return isset($_REQUEST[$this->GetName().'_hidden']);
	}

	public function GetSubmittedValue() {
		$result = array();
		foreach($this->dictionary as $key=>$value)
		{
			if($_REQUEST[$this->GetName()][$key] == TRUE)
			{
				$result[$key] = TRUE;
			}
		}
		return $result;
	}
	
}
?>

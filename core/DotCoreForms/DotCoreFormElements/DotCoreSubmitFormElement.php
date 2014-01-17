<?php

/**
 * DotCoreSubmitFormElement - Defines a form element used to submit the form
 *
 * @author perrin
 */
class DotCoreSubmitFormElement extends DotCoreFormElement {

	public function  __construct($name, $label) {
		parent::__construct($name);
		$this->SetLabel($label);
	}

	private $label = NULL;

	public function GetLabel() {
		return $this->label;
	}

	public function SetLabel($label) {
		$this->label = $label;
	}

	public function __toString() {
		return '<button type="submit" class="submit" name="'.$this->GetName().'">'.$this->label.'</button>';
	}

}
?>

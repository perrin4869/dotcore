<?php

/**
 * DotCoreDateTimeFormElement - Defines a form element used for date time input
 *
 * @author perrin
 */
class DotCoreDateTimeFormElement extends DotCoreInputFormElement {

	public function  __construct($name) {
		parent::__construct($name);

		$this->AddClass('date-time-input');
	}

	/**
	 * Sets the default value of this form element as a timestamp
	 * @param int $timestamp
	 */
	public function SetDefaultTimestampValue($timestamp) {
		$date = date('Y-m-d H:i:s', $timestamp); // Example 2009-06-19 00:45:00
		$this->SetDefaultValue($date);
	}

	public function __toString() {
		return '
			<input
				class="'.$this->GetClass().'"
				type="textbox"
				name="'.$this->GetName().'"
				id="'.$this->GetID().'"
				value="'.$this->GetSavedValue().'"/>';
	}
	
}
?>

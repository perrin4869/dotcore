<?php

/**
 * DotCoreFormElement - Abstract base class for Form Elements that are embedded in forms
 *
 * @author perrin
 */
abstract class DotCoreFormElement extends DotCoreObject {

	public function  __construct($name) {
		$this->name = $name; // Store the name of this element
	}

	/**
	 * Holds the form to which this element belongs, or NULL if it doesn't belong to a form
	 * @var DotCoreForm
	 */
	private $form = NULL;
	
	/**
	 * Gets the form holding this element, or NULL if no form is holding it
	 * @return DotCoreForm
	 */
	public function GetForm() {
		return $this->form;
	}
	
	public function SetForm($form) {
		$this->form = $form;
	}
	
	/**
	 * May be overriden by child classes to associate extra content to this form element
	 * @return string
	 */
	public function GetExtraContent() {
		return NULL;
	}
	
	/**
	 * Stores the name given to this element
	 * @var string
	 */
	private $name = NULL;

	public function GetName() {
		return $this->name;
	}

	public function SetName($name) {
		$this->name = $name;
	}

	abstract function  __toString();
	

}
?>

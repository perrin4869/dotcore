<?php

/**
 * DotCoreRichTextFormElement - Defines an element that has multiline input
 *
 * @author perrin
 */
class DotCoreRichTextFormElement extends DotCoreInputFormElement {

	public function  __construct($name) {
		parent::__construct($name);
	}

	private $rich_editor_class = 'rich-editor';

	public function GetRichEditorClass() {
		return $this->rich_editor_class;
	}

	public function SetRichEditorClass($rich_editor_class) {
		$this->rich_editor_class = $rich_editor_class;
	}

	public function __toString() {
		// We're setting the class here, because we must allow the user to set a new class if they so desire
		$this->AddClass($this->rich_editor_class);

		return '
			<textarea 
				class="'.$this->GetClass().'"
				rows="20" cols="50"
				name="'.$this->GetName().'"
				id="'.$this->GetID().'">'.$this->GetSavedValue().'</textarea>';
	}

}
?>

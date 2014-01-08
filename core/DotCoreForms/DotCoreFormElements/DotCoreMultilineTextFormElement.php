<?php

/**
 * DotCoreMultilineTextFormElement - Defines an element that has multiline input
 *
 * @author perrin
 */
class DotCoreMultilineTextFormElement extends DotCoreInputFormElement {

    public function  __construct($name) {
        parent::__construct($name);

        $this->SetAcceptsHTML(TRUE);
    }

    public function __toString() {
        return '
            <textarea 
                class="'.$this->GetClass().'"
                rows="20" cols="50"
                name="'.$this->GetName().'"
                id="'.$this->GetID().'">'.$this->GetValue().'</textarea>';
    }

}
?>

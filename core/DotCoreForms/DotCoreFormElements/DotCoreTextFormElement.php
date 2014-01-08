<?php

/**
 * DotCoreTextFormElement - Defines a form element used for simple input
 *
 * @author perrin
 */
class DotCoreTextFormElement extends DotCoreInputFormElement {

    public function  __construct($name) {
        parent::__construct($name);
    }

    public function __toString() {
        return '
            <input
                class="'.$this->GetClass().'"
                type="text"
                name="'.$this->GetName().'"
                id="'.$this->GetID().'"
                value="'.$this->GetSavedValue().'"/>';
    }
    
}
?>

<?php

/**
 * DotCorePasswordFormElement - Defines a form element used for password input
 *
 * @author perrin
 */
class DotCorePasswordFormElement extends DotCoreInputFormElement {

    public function  __construct($name) {
        parent::__construct($name);
        
        $this->ValueIsSaved(FALSE); // By default we don't save the values of submitted passwords
    }

    public function __toString() {
        $value_property = 'value="'.$this->GetSavedValue().'"';
        return '
            <input
                class="'.$this->GetClass().'"
                type="password"
                name="'.$this->GetName().'"
                id="'.$this->GetID().'"
                '.$value_property.'/>';
    }
    
}
?>

<?php

/**
 * DotCoreCheckBoxFormElement - Defines a form element used for boolean input
 *
 * @author perrin
 */
class DotCoreCheckBoxFormElement extends DotCoreInputFormElement {

    public function  __construct($name) {
        parent::__construct($name);

        $this->AddClass('checkbox');
    }

    public function __toString() {
        $input = '';
        $value = $this->GetSavedValue();

        $checked = $value ? 'checked="checked"' : '';
        $input .= '
            <input type="hidden" name="'.$this->GetName().'_hidden" id="'.$this->GetID().'_hidden" />
            <input class="'.$this->GetClass().'" name="'.$this->GetName().'" id="'.$this->GetID().'" type="checkbox"'.$checked.' />';
        return $input;
    }
    
    /**
     * Checks whether the value for this element was submitted
     * @return boolean
     */
    public function IsValueSet() {
        return isset($_REQUEST[$this->GetName().'_hidden']);
    }

    public function GetSubmittedValue() {
        return $_REQUEST[$this->GetName()] == TRUE;
    }
    
}
?>

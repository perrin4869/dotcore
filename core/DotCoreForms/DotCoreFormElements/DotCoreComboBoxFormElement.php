<?php

/**
 * DotCoreComboBoxFormElement - Defines a form element used for mutichoice input
 *
 * @author perrin
 */
class DotCoreComboBoxFormElement extends DotCoreInputFormElement {

    public function  __construct($name, $dictionary) {
        parent::__construct($name);

        $this->dictionary = $dictionary;
    }

    private $dictionary = NULL;

    public function GetDictionary() {
        return $this->dictionary;
    }

    public function SetDictionary($dictionary) {
        $this->dictionary = $dictionary;
    }

    public function __toString() {
        $input = '';
        $value = $this->GetSavedValue();
        $input .= '
            <select
                class="'.$this->GetClass().'"
                name="'.$this->GetName().'"
                id="'.$this->GetID().'">';
                foreach($this->dictionary as $key => $item)
                {
                    $selected = ($key == $value) ? 'selected="selected"' : '';
                    $input .= '<option value="'.$key.'" '.$selected.'>'.$item.'</option>';
                }
        $input .= '
            </select>';
        return $input;
    }
    
}
?>

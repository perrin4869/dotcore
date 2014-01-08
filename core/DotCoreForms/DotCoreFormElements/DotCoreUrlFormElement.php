<?php

/**
 * DotCoreUrlFormElement - Defines a form element used for url input
 *
 * @author perrin
 */
class DotCoreUrlFormElement extends DotCoreTextFormElement {

    public function  __construct($name) {
        parent::__construct($name);

        $this->SetDefaultValue('http://');
        $this->AddClass('url-field');
    }
    
}
?>

<?php

/**
 * DotCoreStaticFormElement - Defines a form element used for simple input
 *
 * @author perrin
 */
class DotCoreStaticFormElement extends DotCoreFormElement {

    public function  __construct($name, $content = '') {
        parent::__construct($name);
        $this->SetStaticContent($content);
    }

    /**
     * Stores the content shown in this form element
     * @var string
     */
    private $static_content = '';

    /*
     * Getter
     */

    public function GetStaticContent() {
        return $this->static_content;
    }

    /*
     * Setter
     */

    public function SetStaticContent($content) {
        $this->static_content = $content;
    }

    /*
     * Override
     */

    protected function __toString() {
        return $this->static_content;
    }
    
}
?>

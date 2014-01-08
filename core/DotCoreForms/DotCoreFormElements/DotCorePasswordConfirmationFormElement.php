<?php

/**
 * DotCorePasswordConfirmationFormElement - Defines a form element used for the confirmation of password inputs
 *
 * @author perrin
 */
class DotCorePasswordConfirmationFormElement extends DotCorePasswordFormElement {

    public function  __construct($name, DotCorePasswordFormElement $password_to_confirm) {
        parent::__construct($name);
        
        $this->password_to_confirm = $password_to_confirm;
    }

    /**
     * Holds the DotCorePasswordFormElement to confirm
     * @var DotCorePasswordFormElement
     */
    private $password_to_confirm = NULL;

    /**
     * Holds the password element that this password confirmation element needs to confirm
     * @return DotCorePasswordFormElement
     */
    public function GetPasswordElementToConfirm() {
        return $this->password_to_confirm;
    }

    public function IsValid() {
        return $this->password_to_confirm->GetValue() == $this->GetValue();
    }
    
}
?>

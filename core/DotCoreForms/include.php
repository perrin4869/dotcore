<?php

/*
 *
 * Includes the files needed to run the DotCoreForm library
 *
 */

// Form Elements
$forms_folder = 'DotCoreForms/';

function register_core_form_element($element) {
    global $forms_folder;
    register_core_component($element, $forms_folder . 'DotCoreFormElements/' . $element . '.php');
}

// Register form components
register_core_component('DotCoreForm', $forms_folder . 'DotCoreForm/include.php');
register_core_component('DotCoreFormElement', $forms_folder . 'DotCoreForm/include.php');
register_core_component('DotCoreInputFormElement', $forms_folder . 'DotCoreForm/include.php');

register_core_form_element('DotCoreExplanationFormElement');
register_core_form_element('DotCoreTextFormElement');
register_core_form_element('DotCoreMultilineTextFormElement');
register_core_form_element('DotCoreRichTextFormElement');
register_core_form_element('DotCorePasswordFormElement');
register_core_form_element('DotCorePasswordConfirmationFormElement');
register_core_form_element('DotCoreStaticFormElement');
register_core_form_element('DotCoreDateFormElement');
register_core_form_element('DotCoreDateTimeFormElement');
register_core_form_element('DotCoreComboBoxFormElement');
register_core_form_element('DotCoreCheckBoxFormElement');
register_core_form_element('DotCoreMultipleCheckBoxFormElement');
register_core_form_element('DotCoreSubmitFormElement');
register_core_form_element('DotCoreUrlFormElement');
register_core_form_element('DotCoreUploadFormElement');

?>

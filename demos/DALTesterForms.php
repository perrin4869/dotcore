<?php

include($_SERVER['DOCUMENT_ROOT'].'/core/DotCore/DotCoreObject.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCore/DotCoreMySql.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCore/DotCoreMessages.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCore/DotCoreConfiguration.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCore/DotCoreTreeNode.php');

include($_SERVER['DOCUMENT_ROOT'].'/custom/configuration.php');

include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreDAL.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreDALField.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreDALRelationship.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreDALFulltext.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreDataRecord.php');

include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreDALRestraint.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreDALRestraintUnit.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreFieldRestraint.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreFulltextRestraint.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreLinkRestraint.php');

include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreOneToOneRelationship.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreOneToManyRelationship.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreManyToManyRelationship.php');

include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreDALSelectionOrder.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreDALSelectionOrderUnit.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDAL/DotCoreFieldSelectionOrder.php');

include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDALFields/DotCoreIntField.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDALFields/DotCoreAutoIncrementingKey.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDALFields/DotCoreStringField.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDALFields/DotCoreEmailField.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDALFields/DotCorePasswordField.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDALFields/DotCoreBooleanField.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDALFields/DotCoreDateField.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDALFields/DotCoreDateTimeField.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDALFields/DotCoreImageField.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDALFields/DotCoreRecursiveIntField.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreDALFields/DotCoreTimestampField.php');

// Form Elements
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreExplanationFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreLabeledFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreInputFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreTextFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreMultilineTextFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreRichTextFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCorePasswordFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCorePasswordConfirmationFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreStaticFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreDateFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreDateTimeFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreComboBoxFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreCheckBoxFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreMultipleCheckBoxFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreSubmitFormElement.php');

include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreForm.php');

include($_SERVER['DOCUMENT_ROOT'].'/custom/components.php');

include ($_SERVER['DOCUMENT_ROOT'] . '/custom/classes/profiling-2006-02-28/class_profiling.inc.php');
$profiler = new profiling();
    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DAL Tester</title>

<style type="text/css">

    body{
        margin: 0px;
        padding: 0px;
    }

    .RightToLeftInput {
        direction: rtl;
    }

    .LeftToRightInput {
        direction: ltr;
    }

    textarea {
        width: 300px;
        height: 200px;
    }

    .richtext {
        width: 99%;
    }

</style>

<!-- TinyMCE -->
<script type="text/javascript" src="/scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="/admin/scripts/load_tinymce.js"></script>

<!-- File Manager -->
<script type="text/javascript" src="/scripts/tinymce/jscripts/tiny_mce/plugins/tinyupload/tinyupload.js"></script>

<!-- Calendar -->
<link rel="stylesheet" href="/scripts/jscalendar-1.0/calendar-white.css" type="text/css" media="screen" />
<script type="text/javascript" src="/scripts/jscalendar-1.0/calendar.js"></script>
<script type="text/javascript" src="/scripts/jscalendar-1.0/lang/calendar-he-utf8.js"></script>
<script type="text/javascript" src="/scripts/jscalendar-1.0/calendar-setup.js"></script>

</head>
<body>

<?php

$profiler->add('Start<br/><br/>');

// Forms
$_REQUEST['text1'] = 'Hello';
$_REQUEST['richEditor'] = 'Rich Editor';

$rolesBLL = new DotCoreRoleBLL();
$multilangRolesLink = $rolesBLL->LinkRolesMultilangDAL();
$multilangRolesBLL = $multilangRolesLink->GetLinkedDAL();
$multilangRolesLink->SetKeyField($multilangRolesBLL->getFieldLanguageID());

$roles =
    $rolesBLL
        ->Fields(array(
            $rolesBLL->getFieldRoleID(),
            $multilangRolesBLL->getFieldRoleName()))
        ->Select();

$languagesBLL = new DotCoreLanguageBLL();
$languages =
    $languagesBLL
        ->Fields(array($languagesBLL->getFieldLanguageCode()))
        ->GetLanguagesCodeDictionary();

$rolesDictionary = array();
$countRoles = count($roles);
for($i = 0; $i < $countRoles; $i++)
{
    $multilangRoles = $roles[$i]->GetRolesMultilanguageProperties();
    $rolesDictionary[$roles[$i]->getRoleID()] = $multilangRoles[$languages['he']->getLanguageID()]->getRoleName();
}

$text_input = new DotCoreTextFormElement('text1', 'Label 1');
$text_input->SetDefaultValue('Default');

$multiline_input = new DotCoreMultilineTextFormElement('multilineElement', 'Label 2');
$multiline_input->SetDefaultValue('Text area default');
$multiline_input->SetDirection(DotCoreLanguageDAL::LANGUAGES_DIRECTION_RTL);

$rich_input = new DotCoreRichTextFormElement('richEditor', 'Rich Editor');

$password_input = new DotCorePasswordFormElement('passwordElement', 'Password');

$password_validation = 
    new DotCorePasswordConfirmationFormElement(
        'passwordElementValidation',
        'Password Confirmation',
        $password_input);

$static_element = new DotCoreStaticFormElement('staticElement', 'Static Label', 'Static Content');

$explanation_element = new DotCoreExplanationFormElement('explanationElement', 'Explanation Element');

$date_element = new DotCoreDateFormElement('dateField', 'Date Field');
$date_element->SetDefaultValue('1990-10-18');

$combo_element = new DotCoreComboBoxFormElement('comboField', 'ComboBox', $rolesDictionary);

$checkbox_element = new DotCoreCheckBoxFormElement('checkboxField', 'True/False');

$roles_selector = new DotCoreMultipleCheckBoxFormElement('rolesCheckboxes', 'Roles', $rolesDictionary);

$submit_element = new DotCoreSubmitFormElement('submitButton', 'Send');

$form = new DotCoreForm('test_form', $_SERVER['PHP_SELF']);
$form->AddFormElement($text_input);
$form->AddFormElement($multiline_input);
$form->AddFormElement($rich_input);
$form->AddFormElement($password_input);
$form->AddFormElement($password_validation);
$form->AddFormElement($static_element);
$form->AddFormElement($explanation_element);
$form->AddFormElement($date_element);
$form->AddFormElement($combo_element);
$form->AddFormElement($checkbox_element);
$form->AddFormElement($roles_selector);
$form->AddFormElement($submit_element);

if($form->WasSubmitted())
{
    if(!$password_validation->IsValid())
    {
        $password_input->SetError('The passwords don\'t match!');
    }

    if($checkbox_element->GetValue())
    {
        $checkbox_element->SetError('CHECKED');
    }
    else
    {
        $checkbox_element->SetError('Not Checked');
    }
}

echo $form;

$profiler->end();
// echo $profiler->get_result();

?>
</body>
</html>
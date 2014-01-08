<?php

include ($_SERVER['DOCUMENT_ROOT'] . '/custom/components/profiling-2006-02-28/class_profiling.inc.php');
$profiler = new profiling();

include($_SERVER['DOCUMENT_ROOT'].'/core/DotCore/DotCoreObject.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCore/DotCoreMySql.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCore/DotCoreMessages.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCore/DotCoreConfiguration.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCore/DotCoreTreeNode.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCore/DotCoreDateTime.php');

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

// Form Elements
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreInputFormElement.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreForm.php');
include($_SERVER['DOCUMENT_ROOT'].'/core/DotCoreForm/DotCoreFormGenerator.php');

include($_SERVER['DOCUMENT_ROOT'].'/custom/components.php');

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

<script type="text/javascript" src="/scripts/helper.js"></script>
<script type="text/javascript" src="/scripts/events.js"></script>

<script type="text/javascript" src="/admin/scripts/admin.js"></script>

<link rel="stylesheet" href="/admin/styles/master.css" type="text/css" media="screen" />

</head>
<body>

<?php

$profiler->add('Start<br/><br/>');

// Forms
$users_bll =  new DotCoreUserBLL();
$user = $users_bll
    ->Fields(
        array(
            $users_bll->getFieldUserName(),
            $users_bll->getFieldPassword(),
            $users_bll->getFieldEmail(),
            $users_bll->getFieldFirstName(),
            $users_bll->getFieldLastName(),
            $users_bll->getFieldLastLogin(),
            $users_bll->getFieldPhone()))
    ->GetByUsername('perrin4869');

/* @var $form_element DotCoreNewsRecord */
$form_element = $blabla;

$messages = array();
$messages[DotCoreUserDAL::USER_NAME.DotCoreFormGenerator::MESSAGE_LABEL] = 'Username';
$messages[DotCoreUserDAL::USER_NAME.DotCoreFormGenerator::MESSAGE_EXPLANATION] = 'This is the name that will be used for logging into the website';
$messages[DotCoreUserDAL::USER_PASSWORD.DotCoreFormGenerator::MESSAGE_LABEL] = 'Password';
$messages[DotCoreUserDAL::USER_PASSWORD.DotCoreFormGenerator::MESSAGE_VALIDATION] = 'Password Validation';
$messages[DotCoreUserDAL::USER_EMAIL.DotCoreFormGenerator::MESSAGE_LABEL] = 'Email';
$messages[DotCoreUserDAL::USER_FIRST_NAME.DotCoreFormGenerator::MESSAGE_LABEL] = 'First Name';
$messages[DotCoreUserDAL::USER_LAST_NAME.DotCoreFormGenerator::MESSAGE_LABEL] = 'Last Name';
$messages[DotCoreUserDAL::USER_LAST_LOGIN.DotCoreFormGenerator::MESSAGE_LABEL] = 'Last Login';
$messages[DotCoreUserDAL::USER_PHONE.DotCoreFormGenerator::MESSAGE_LABEL] = 'Phone';
$messages[DotCoreUserDAL::USER_UNIQUE_USERNAME.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION] = 'The username is already in use, please choose a new one.';

/*$form_generator = new DotCoreFormGenerator('test_form', $_SERVER['PHP_SELF']);
$form_generator->GenerateFrom($user, $messages, array());
$form_generator->GenerateSubmitButton('Update User');
$form = $form_generator->GetForm();

if($form->WasSubmitted())
{
    if($form_generator->ProcessForm($user, $messages)) {
        $users_bll->Save($user);
    }
}*/

// echo $form;

/**
 *
// Events form
$events_bll = new ChamberEilatEventBLL();

$event =
    $events_bll
        ->Fields(array(
                $events_bll->getFieldTitle(),
                $events_bll->getFieldDescription(),
                $events_bll->getFieldDetails(),
                $events_bll->getFieldEventTypeID(),
                $events_bll->getFieldStartDate(),
                $events_bll->getFieldPlace()))
        ->GetEventByID(4);

$event_types_bll = new ChamberEilatEventTypeBLL();
$event_types = $event_types_bll->GetEventTypesByLanguageID(1); // 1 = hebrew
$event_types_dictionary = array();
$count_event_types = count($event_types);
for($i = 0; $i < $count_event_types; $i++) {
    $event_type = $event_types[$i];
    $event_types_dictionary[$event_type->getEventTypeID()] = $event_type->getEventTypeName();
}

$messages = array();
$messages[ChamberEilatEventDAL::EVENTS_TITLE.DotCoreDALFormGenerator::MESSAGE_LABEL] = 'Title';
$messages[ChamberEilatEventDAL::EVENTS_TITLE.DotCoreDALFormGenerator::MESSAGE_EMPTY_EXCEPTION] = 'The event\'s title cannot be empty, you idiot!';

$messages[ChamberEilatEventDAL::EVENTS_DESCRIPTION.DotCoreDALFormGenerator::MESSAGE_LABEL] = 'Description';
$messages[ChamberEilatEventDAL::EVENTS_DESCRIPTION.DotCoreDALFormGenerator::MESSAGE_EMPTY_EXCEPTION] = 'The description can\'t be empty! Idiot!';
$messages[ChamberEilatEventDAL::EVENTS_DETAILS.DotCoreDALFormGenerator::MESSAGE_LABEL] = 'Details';
$messages[ChamberEilatEventDAL::EVENTS_TYPE_ID.DotCoreDALFormGenerator::MESSAGE_LABEL] = 'Event Type';
$messages[ChamberEilatEventDAL::EVENTS_START.DotCoreDALFormGenerator::MESSAGE_LABEL] = 'Start Date';
$messages[ChamberEilatEventDAL::EVENTS_START.DotCoreDALFormGenerator::MESSAGE_INVALID_DATETIME_EXCEPTION] = 'The Date submitted is invalid you idiot!';
$messages[ChamberEilatEventDAL::EVENTS_PLACE.DotCoreDALFormGenerator::MESSAGE_LABEL] = 'Place';

$hints = array();
$hints[DotCoreDALFormGenerator::HINT_MULTICHOICE] = array();
$hints[DotCoreDALFormGenerator::HINT_MULTICHOICE][$events_bll->getFieldEventTypeID()->GetFieldName()] = $event_types_dictionary;

$form_generator = new DotCoreDALFormGenerator('test_form', $_SERVER['PHP_SELF']);
$form_generator->GenerateFrom($event, $messages, $hints);
$form_generator->GenerateSubmitButton('Update Event');
$form = $form_generator->GetForm();

if($form->WasSubmitted())
{
    $form_generator->ProcessForm($event, $messages);
}

echo $form;
 *
 */

echo urlencode('http://psgels.blogsome.com/2009/07/02/igano-kabamaru-review-80100/?bla=nla&safasf=fsaf');

$profiler->end();
echo $profiler->get_result();

?>

<textarea rows="" cols=""><p>Blablabla <br /> blablabla</p></textarea>

</body>
</html>
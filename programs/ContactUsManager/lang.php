<?php

// Messages for ContactUsManager

$messages = array();

$messages['AdminTitleContactUsManager'] = array();
$messages['AdminTitleContactUsManager'][DotCoreMessages::VALUES] = array(
    'he' => 'ניהול יצירת קשר',
    'en' => 'Contact Recipients'
);

/************************************************* Contact Us Titles ********************************************************************/
$messages['TitleAddContactUsRecipient'] = array();
$messages['TitleAddContactUsRecipient'][DotCoreMessages::VALUES] = array(
    'he' => 'הוספת נמען יצירת קשר',
    'en' => 'Recipient Addition'
);

$messages['TitleEditContactUsRecipient'] = array();
$messages['TitleEditContactUsRecipient'][DotCoreMessages::VALUES] = array(
    'he' => 'עריכת נמען יצירת קשר',
    'en' => 'Recipient Administration'
);

$messages['TableHeaderContactUsRecipientName'] = array();
$messages['TableHeaderContactUsRecipientName'][DotCoreMessages::VALUES] = array(
    'he' => 'שם הנמען',
    'en' => 'Recipient Name'
);

$messages['TableHeaderContactUsRecipientEmail'] = array();
$messages['TableHeaderContactUsRecipientEmail'][DotCoreMessages::VALUES] = array(
    'he' => 'אימייל הנמען',
    'en' => 'Recipient Email'
);

/************************************************* Contact Us Messages ********************************************************************/

$messages['MessageSuccessfulAddition'] = array();
$messages['MessageSuccessfulAddition'][DotCoreMessages::VALUES] = array(
    'he' => 'הנמען נוסף בהצלחה.',
    'en' => 'The recipient was added successfully'
);

$messages['MessageSuccessfulDeletion'] = array();
$messages['MessageSuccessfulDeletion'][DotCoreMessages::VALUES] = array(
    'he' => 'הנמען נמחק בהצלחה.',
    'en' => 'The recipient was deleted successfully'
);

$messages['MessageContactUsRecipientNotFound'] = array();
$messages['MessageContactUsRecipientNotFound'][DotCoreMessages::VALUES] = array(
    'he' => 'הנמען המבוקש לא נמצא',
    'en' => 'The requested recipient was not found'
);

$messages['MessageContactUsRecipientDeletionConfirm'] = array();
$messages['MessageContactUsRecipientDeletionConfirm'][DotCoreMessages::VALUES] = array(
    'he' => 'האם הינך בטוח כי ברצונך למחוק נמען זה?',
    'en' => 'Are you sure you want to delete this recipient?'
);


$messages[DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'שם המקבל אימייל יצירת קשר אינו יכול להיות ריק.',
    'en' => 'The recipient name cannot be empty'
);

$messages[DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_EMAIL.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_EMAIL.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'האימייל אינו יכול להיות ריק.',
    'en' => 'The recipient email cannot be empty'
);

$messages[DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_EMAIL.DotCoreFormGenerator::MESSAGE_INVALID_EMAIL_EXCEPTION] = array();
$messages[DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_EMAIL.DotCoreFormGenerator::MESSAGE_INVALID_EMAIL_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'האימייל אינו חוקי.',
    'en' => 'The email is invalid'
);

$messages[DotCoreContactUsRecipientDAL::CONTACT_US_UNIQUE_EMAIL_KEY.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION] = array();
$messages[DotCoreContactUsRecipientDAL::CONTACT_US_UNIQUE_EMAIL_KEY.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'האימייל שהוכנס כבר בשימוש, אנא בחר אימייל אחר.',
    'en' => 'The email submitted is being used already'
);

/************************************************* Contact Us Folder ********************************************************************/

$messages[DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_NAME.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_NAME.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
    'he' => 'שם הנמען',
    'en' => 'Recipient name'
);

$messages[DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_EMAIL.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreContactUsRecipientDAL::CONTACT_US_RECIPIENT_EMAIL.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
    'he' => 'אימייל הנמען',
    'en' => 'Recipient email'
);

?>

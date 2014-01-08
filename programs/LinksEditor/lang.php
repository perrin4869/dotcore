<?php

// Messages for LinksEditor

$messages = array();

$messages['AdminTitleLinkEditor'] = array();
$messages['AdminTitleLinkEditor'][DotCoreMessages::VALUES] = array(
    'he' => 'קישורים',
    'en' => 'Links'
);

/************************************************* Links Titles ********************************************************************/

$messages['TitleLinksEditor'] = array();
$messages['TitleLinksEditor'][DotCoreMessages::VALUES] = array(
    'he' => 'ניהול קישורים',
    'en' => 'Links Administration'
);

$messages['TitleAddLink'] = array();
$messages['TitleAddLink'][DotCoreMessages::VALUES] = array(
    'he' => 'הוספת קישור',
    'en' => 'Links addition'
);

$messages['TitleEditLink'] = array();
$messages['TitleEditLink'][DotCoreMessages::VALUES] = array(
    'he' => 'עריכת קישור',
    'en' => 'Links editing'
);



$messages['TableHeaderLinkTitle'] = array();
$messages['TableHeaderLinkTitle'][DotCoreMessages::VALUES] = array(
    'he' => 'כותרת הקישור',
    'en' => 'Title'
);

$messages['TableHeaderLinkUrl'] = array();
$messages['TableHeaderLinkUrl'][DotCoreMessages::VALUES] = array(
    'he' => 'כתובת',
    'en' => 'Address'
);

$messages['TableHeaderLinkDescription'] = array();
$messages['TableHeaderLinkDescription'][DotCoreMessages::VALUES] = array(
    'he' => 'תיאור קצר',
    'en' => 'Description'
);

$messages['TableHeaderLinkLogo'] = array();
$messages['TableHeaderLinkLogo'][DotCoreMessages::VALUES] = array(
    'he' => 'לוגו הקישור',
    'en' => 'Logo'
);

/************************************************* Links Messages ********************************************************************/

$messages['MessageSuccessfulAddition'] = array();
$messages['MessageSuccessfulAddition'][DotCoreMessages::VALUES] = array(
    'he' => 'הקישור נוסף בהצלחה.',
    'en' => 'The link was added successfully'
);

$messages['MessageSuccessfulDeletion'] = array();
$messages['MessageSuccessfulDeletion'][DotCoreMessages::VALUES] = array(
    'he' => 'הקישור נמחק בהצלחה.',
    'en' => 'The link was deleted successfully'
);

$messages['MessageLinkNotFound'] = array();
$messages['MessageLinkNotFound'][DotCoreMessages::VALUES] = array(
    'he' => 'הקישור המבוקש לא נמצא',
    'en' => 'The requested link was not found'
);

$messages['MessageLinkDeletionConfirm'] = array();
$messages['MessageLinkDeletionConfirm'][DotCoreMessages::VALUES] = array(
    'he' => 'האם הינך בטוח כי ברצונך למחוק קישור זה?',
    'en' => 'Are you sure you want to delete this link?'
);



$messages[DotCoreLinkDAL::LINK_TITLE.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreLinkDAL::LINK_TITLE.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'כותרת הקישור אינה יכולה להיות ריקה',
    'en' => 'The link title cannot be empty'
);

$messages[DotCoreLinkDAL::LINK_URL.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreLinkDAL::LINK_URL.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'כתובת הקישור אינה יכולה להיות ריקה',
    'en' => 'The address of the link cannot be empty'
);

$messages[DotCoreLinkDAL::LINK_URL.DotCoreFormGenerator::MESSAGE_INVALID_URL_EXCEPTION] = array();
$messages[DotCoreLinkDAL::LINK_URL.DotCoreFormGenerator::MESSAGE_INVALID_URL_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'כתובת הקישור אינה תקינה',
    'en' => 'The address is invalid'
);

$messages[DotCoreLinkDAL::LINK_DESCRIPTION.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreLinkDAL::LINK_DESCRIPTION.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'תיאור הקישור אינו יכול להיות ריק',
    'en' => 'The link description cannot be empty'
);

$messages[DotCoreLinkDAL::LINK_LOGO.DotCoreFormGenerator::MESSAGE_INVALID_EXTENSION_EXCEPTION] = array();
$messages[DotCoreLinkDAL::LINK_LOGO.DotCoreFormGenerator::MESSAGE_INVALID_EXTENSION_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'הקובץ אינו חוקים. מותר להעלות תמונת בלבד.',
    'en' => 'The file is not valid'
);

$messages[DotCoreLinkDAL::LINK_LOGO.DotCoreFormGenerator::MESSAGE_FILE_UPLOAD_EXCEPTION] = array();
$messages[DotCoreLinkDAL::LINK_LOGO.DotCoreFormGenerator::MESSAGE_FILE_UPLOAD_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'העלת הקובץ נכשלה, אנא נסה/י שנית.',
    'en' => 'The file uploading failed, please try again'
);

/************************************************* Links Folder ********************************************************************/

$messages[DotCoreLinkDAL::LINK_TITLE.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreLinkDAL::LINK_TITLE.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
    'he' => 'כותרת הקישור',
    'en' => 'Title'
);

$messages[DotCoreLinkDAL::LINK_URL.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreLinkDAL::LINK_URL.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
    'he' => 'כתובת',
    'en' => 'Address'
);

$messages[DotCoreLinkDAL::LINK_DESCRIPTION.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreLinkDAL::LINK_DESCRIPTION.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
    'he' => 'תיאור קצר',
    'en' => 'Description'
);

$messages[DotCoreLinkDAL::LINK_LOGO.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreLinkDAL::LINK_LOGO.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
    'he' => 'לוגו',
    'en' => 'Logo'
);

?>

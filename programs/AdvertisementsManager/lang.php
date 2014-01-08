<?php

$messages = array();

$messages['AdminTitleMainAdvertisement'] = array();
$messages['AdminTitleMainAdvertisement'][DotCoreMessages::VALUES] = array(
    'he' => 'פרסומות'
);

$messages['AdminTitleManageAdvertisements'] = array();
$messages['AdminTitleManageAdvertisements'][DotCoreMessages::VALUES] = array(
    'he' => 'ניהול פרסומות'
);

$messages['AdTitleManage'] = array();
$messages['AdTitleManage'][DotCoreMessages::VALUES] = array(
    'he' => 'ניהול פרסומות'
);
$messages['AdTitleInsert'] = array();
$messages['AdTitleInsert'][DotCoreMessages::VALUES] = array(
    'he' => 'הוספת פרסומות'
);
$messages['AdTitleEdit'] = array();
$messages['AdTitleEdit'][DotCoreMessages::VALUES] = array(
    'he' => 'עריכת פרסומות'
);

/************************************************* Advertisements Manager Errors *********************************************************/

$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_MEDIA_NAME.DotCoreFormGenerator::MESSAGE_INVALID_EXTENSION_EXCEPTION] = array();
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_MEDIA_NAME.DotCoreFormGenerator::MESSAGE_INVALID_EXTENSION_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'הקובץ שהועלה אינו חוקי. מותר להעלות קבצי פלאש ותמונות בלבד.'
);
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_MEDIA_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_MEDIA_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'התמונה של הפרסומת אינה יכולה להיות ריקה.'
);
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_URL.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_URL.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'כתובת יעד הפרסומת אינה יכולה להיות ריקה'
);
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_URL.DotCoreFormGenerator::MESSAGE_INVALID_URL_EXCEPTION] = array();
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_URL.DotCoreFormGenerator::MESSAGE_INVALID_URL_EXCEPTION][DotCoreMessages::VALUES] = array(
    'he' => 'הכתובת אינה חוקית.'
);
$messages['ErrorAdNotFound'] = array();
$messages['ErrorAdNotFound'][DotCoreMessages::VALUES] = array(
    'he' => 'הפרסומת המבוקשת לא נמצאה.'
);

/************************************************* Advertisements Manager Labels *********************************************************/

$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_TEXT.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_TEXT.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
    'he' => 'טקסט הפרסומת'
);
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_URL.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_URL.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
    'he' => 'כתובת יעד'
);
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_IS_ACTIVE.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_IS_ACTIVE.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
    'he' => 'נמצא בשימוש'
);
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_MEDIA_NAME.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreAdvertisementDAL::ADVERTISEMENT_MEDIA_NAME.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
    'he' => 'התמונה'
);

/************************************************* Advertisements Manager Messages *******************************************************/

$messages['MessageAdAddedSuccessfully'] = array();
$messages['MessageAdAddedSuccessfully'][DotCoreMessages::VALUES] = array(
    'he' => 'הפרסומת נוספה בהצלחה'
);
$messages['MessageAdEditedSuccessfully'] = array();
$messages['MessageAdEditedSuccessfully'][DotCoreMessages::VALUES] = array(
    'he' => 'השינויים בוצעו בהצלחה'
);
$messages['MessageAdDeletedSuccessfully'] = array();
$messages['MessageAdDeletedSuccessfully'][DotCoreMessages::VALUES] = array(
    'he' => 'הפרסומת נמחקה בהצלחה'
);
$messages['MessageAdConfirmDeletion'] = array();
$messages['MessageAdConfirmDeletion'][DotCoreMessages::VALUES] = array(
    'he' => 'האם הינך בטוח כי ברצונך למחוק פרסומת זו?'
);

?>

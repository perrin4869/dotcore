<?php

$messages = array();

$messages['AdminTitleMainNews'] = array();
$messages['AdminTitleMainNews'][DotCoreMessages::VALUES] = array(
	'he' => 'חדשות'
);

$messages['AdminTitleAddNews'] = array();
$messages['AdminTitleAddNews'][DotCoreMessages::VALUES] = array(
	'he' => 'הוספת חדשות'
);
$messages['AdminTitleEditNews'] = array();
$messages['AdminTitleEditNews'][DotCoreMessages::VALUES] = array(
	'he' => 'ניהול חדשות'
);

/************************************************* News Errors **************************************************************/

$messages[DotCoreNewsDAL::NEWS_TITLE.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreNewsDAL::NEWS_TITLE.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'הכותרת של החדשות אינו יכול להיות ריק'
);
$messages[DotCoreNewsDAL::NEWS_SHORT_CONTENT.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreNewsDAL::NEWS_SHORT_CONTENT.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'התיאור הקצר של החדשות אינו יכול להיות ריק.'
);
$messages[DotCoreNewsDAL::NEWS_DATE.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreNewsDAL::NEWS_DATE.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'תאריך הפרסום אינו יכול להיות ריק.'
);
$messages['ErrorNotFound'] = array();
$messages['ErrorNotFound'][DotCoreMessages::VALUES] = array(
	'he' => 'החדשות המבוקשות לא נמצאו.'
);

/************************************************* News Labels **************************************************************/

$messages[DotCoreNewsDAL::NEWS_TITLE.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreNewsDAL::NEWS_TITLE.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'כותרת החדשות'
);
$messages[DotCoreNewsDAL::NEWS_SHORT_CONTENT.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreNewsDAL::NEWS_SHORT_CONTENT.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'תיאור קצר'
);
$messages[DotCoreNewsDAL::NEWS_CONTENT.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreNewsDAL::NEWS_CONTENT.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'תוכן החדשות'
);
$messages[DotCoreNewsDAL::NEWS_DATE.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreNewsDAL::NEWS_DATE.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'תאריך פרסום'
);

/************************************************* News Messages **************************************************************/

$messages['MessageNewsAddedSuccessfully'] = array();
$messages['MessageNewsAddedSuccessfully'][DotCoreMessages::VALUES] = array(
	'he' => 'החדשות נוספו בהצלחה'
);
$messages['MessageNewsEditedSuccessfully'] = array();
$messages['MessageNewsEditedSuccessfully'][DotCoreMessages::VALUES] = array(
	'he' => 'השינויים בוצעו בהצלחה'
);
$messages['MessageNewsDeletedSuccessfully'] = array();
$messages['MessageNewsDeletedSuccessfully'][DotCoreMessages::VALUES] = array(
	'he' => 'החדשות נמחקו בהצלחה'
);
$messages['MessageNewsConfirmDeletion'] = array();
$messages['MessageNewsConfirmDeletion'][DotCoreMessages::VALUES] = array(
	'he' => 'האם הינך בטוח כי ברצונך למחוק חדשות אלו?'
);

?>
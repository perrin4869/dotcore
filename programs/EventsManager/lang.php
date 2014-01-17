<?php

$messages = array();

$messages['AdminTitleMainEvents'] = array();
$messages['AdminTitleMainEvents'][DotCoreMessages::VALUES] = array(
	'he' => 'אירועים'
);

$messages['AdminTitleManageEvents'] = array();
$messages['AdminTitleManageEvents'][DotCoreMessages::VALUES] = array(
	'he' => 'ניהול אירועים'
);
$messages['AdminTitleAddEvents'] = array();
$messages['AdminTitleAddEvents'][DotCoreMessages::VALUES] = array(
	'he' => 'הוספת אירועים'
);
$messages['AdminTitleEditEvents'] = array();
$messages['AdminTitleEditEvents'][DotCoreMessages::VALUES] = array(
	'he' => 'עריכת אירועים'
);

/************************************************* Events Errors **************************************************************/

$messages[DotCoreEventDAL::EVENTS_TITLE.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreEventDAL::EVENTS_TITLE.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'שם האירוע אינו יכול להיות ריק.'
);

$messages[DotCoreEventDAL::EVENTS_DESCRIPTION.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreEventDAL::EVENTS_DESCRIPTION.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'תיאור האירוע אינו יכול להיות ריק.'
);
$messages[DotCoreEventDAL::EVENTS_DATE.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreEventDAL::EVENTS_DATE.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'התאריך אינו יכול להיות ריק'
);
$messages[DotCoreEventDAL::EVENTS_DATE.DotCoreFormGenerator::MESSAGE_INVALID_DATETIME_EXCEPTION] = array();
$messages[DotCoreEventDAL::EVENTS_DATE.DotCoreFormGenerator::MESSAGE_INVALID_DATETIME_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'התאריך אינו חוקי'
);

/************************************************* Events Labels **************************************************************/

$messages[DotCoreEventDAL::EVENTS_TITLE.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreEventDAL::EVENTS_TITLE.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'שם האירוע'
);
$messages[DotCoreEventDAL::EVENTS_DESCRIPTION.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreEventDAL::EVENTS_DESCRIPTION.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'תיאור קצר'
);
$messages[DotCoreEventDAL::EVENTS_DETAILS.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreEventDAL::EVENTS_DETAILS.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'פרטים'
);
$messages[DotCoreEventDAL::EVENTS_DATE.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreEventDAL::EVENTS_DATE.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'תאריך תחילת האירוע'
);

/************************************************* Events Messages **************************************************************/

$messages['MessageEventAddedSuccessfully'] = array();
$messages['MessageEventAddedSuccessfully'][DotCoreMessages::VALUES] = array(
	'he' => 'אירוע נוסף בהצלחה'
);
$messages['MessageEventEditedSuccessfully'] = array();
$messages['MessageEventEditedSuccessfully'][DotCoreMessages::VALUES] = array(
	'he' => 'השינויים בוצעו בהצלחה'
);
$messages['MessageEventDeletedSuccessfully'] = array();
$messages['MessageEventDeletedSuccessfully'][DotCoreMessages::VALUES] = array(
	'he' => 'האירוע נמחק בהצלחה'
);
$messages['MessageEventConfirmDeletion'] = array();
$messages['MessageEventConfirmDeletion'][DotCoreMessages::VALUES] = array(
	'he' => 'האם הינך בטוח כי ברצונך למחוק אירוע זה?'
);

?>

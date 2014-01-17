<?php

// Messages for PagesManager
$messages = array();

$messages['AdminTitleMainPages'] = array();
$messages['AdminTitleMainPages'][DotCoreMessages::VALUES] = array(
	'en' => 'Pages Management',
	'he' => 'ניהול דפים'
);

$messages['AdminTitleAddPages'] = array();
$messages['AdminTitleAddPages'][DotCoreMessages::VALUES] = array(
	'en' => 'Add new page',
	'he' => 'הוספת דף חדש'
);
$messages['AdminTitleEditPages'] = array();
$messages['AdminTitleEditPages'][DotCoreMessages::VALUES] = array(
	'en' => 'Manage pages',
	'he' => 'ניהול דפים'
);
$messages['AdminTitleEditSharedContents'] = array();
$messages['AdminTitleEditSharedContents'][DotCoreMessages::VALUES] = array(
	'en' => 'Edit general contents',
	'he' => 'עריכת תכנים גלובלים'
);
$messages['AdminTitleUploadImages'] = array();
$messages['AdminTitleUploadImages'][DotCoreMessages::VALUES] = array(
	'en' => 'Manage images',
	'he' => 'ניהול תמונות'
);
$messages['AdminTitleTemplatesEditor'] = array();
$messages['AdminTitleTemplatesEditor'][DotCoreMessages::VALUES] = array(
	'en' => 'Templates editor',
	'he' => 'ניהול תבניות'
);
$messages['AdminTitleTemplatesMessagesEditor'] = array();
$messages['AdminTitleTemplatesMessagesEditor'][DotCoreMessages::VALUES] = array(
	'en' => 'Template messages editor',
	'he' => 'עריכת תכני תבנית'
);
$messages['AdminTitleTemplatesConfigurationEditor'] = array();
$messages['AdminTitleTemplatesConfigurationEditor'][DotCoreMessages::VALUES] = array(
	'en' => 'Templates configuration editor',
	'he' => 'עריכת הגדרות תבנית'
);

/************************************************* Page Messages ********************************************************************/

$messages[DotCorePageDAL::PAGE_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCorePageDAL::PAGE_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'The page name cannot be empty.',
	'he' => 'שם הדף אינו יכול להיות ריק.'
);
$messages[DotCorePageDAL::PAGE_URL.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCorePageDAL::PAGE_URL.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'The page address cannot be empty.',
	'he' => 'כתובת הדף אינה יכולה להיות ריקה.'
);
$messages[DotCorePageDAL::PAGE_UNIQUE_URL.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION] = array();
$messages[DotCorePageDAL::PAGE_UNIQUE_URL.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'This address is already in use, choose another one.',
	'he' => 'הכתובת הזאת כבר בשימוש, אנא בחר כתובת אחרת.'
);
$messages[DotCorePageDAL::PAGE_PARENT_ID.DotCoreFormGenerator::MESSAGE_CHILD_IS_OWN_PARENT_EXCEPTION] = array();
$messages[DotCorePageDAL::PAGE_PARENT_ID.DotCoreFormGenerator::MESSAGE_CHILD_IS_OWN_PARENT_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'A page cannot be its own parent.',
	'he' => 'דף אינו יכול להיות האב של עצמו.'
);
$messages[DotCorePageDAL::PAGE_PARENT_ID.DotCoreFormGenerator::MESSAGE_INVALID_RECURSIVE_FIELD_EXCEPTION] = array();
$messages[DotCorePageDAL::PAGE_PARENT_ID.DotCoreFormGenerator::MESSAGE_INVALID_RECURSIVE_FIELD_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'The parent page is invalid.',
	'he' => 'דף האב אינו חוקי.'
);
$messages['MessagePageEditedSuccessfully'] = array();
$messages['MessagePageEditedSuccessfully'][DotCoreMessages::VALUES] = array(
	'en' => 'The changes to the page were made successfully.',
	'he' => 'הדף נשמר בהצלחה.'
);
$messages['MessagePageAddedSuccessfully'] = array();
$messages['MessagePageAddedSuccessfully'][DotCoreMessages::VALUES] = array(
	'en' => 'The page was added successfully.',
	'he' => 'הדף נוסף בהצלחה.'
);
$messages['MessagePageConfirmDelete'] = array();
$messages['MessagePageConfirmDelete'][DotCoreMessages::VALUES] = array(
	'en' => 'Are you sure you want to delete this page?',
	'he' => 'האם הינך בטוח שברצונך למחוק דף זה?'
);
$messages['MessagePageDeleteSuccess'] = array();
$messages['MessagePageDeleteSuccess'][DotCoreMessages::VALUES] = array(
	'en' => 'The page was deleted successfully.',
	'he' => 'הדף נמחק בהצלחה.'
);
$messages['MessagePageDeleteFailure'] = array();
$messages['MessagePageDeleteFailure'][DotCoreMessages::VALUES] = array(
	'en' => 'The page deletion failed, please try again.',
	'he' => 'מחיקת הדף נכשל, אנא נסה\י שנית.'
);
$messages['ErrorPageNotFound'] = array();
$messages['ErrorPageNotFound'][DotCoreMessages::VALUES] = array(
	'en' => 'The requested page was not found.',
	'he' => 'הדף המבוקש לא נמצא.'
);

/************************************************* Page Labels **********************************************************************/

$messages[DotCorePageDAL::PAGE_NAME.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCorePageDAL::PAGE_NAME.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Page name',
	'he' => 'שם הדף'
);
$messages[DotCorePageDAL::PAGE_URL.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCorePageDAL::PAGE_URL.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Page address',
	'he' => 'כתובת הדף'
);
$messages[DotCorePageDAL::PAGE_TITLE.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCorePageDAL::PAGE_TITLE.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Page title',
	'he' => 'כותרת הדף'
);
$messages[DotCorePageDAL::PAGE_APPEARS_IN_NAV.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCorePageDAL::PAGE_APPEARS_IN_NAV.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Appears in navigation',
	'he' => 'מופיע בניווט'
);
$messages[DotCorePageDAL::PAGE_HEADER_CONTENT.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCorePageDAL::PAGE_HEADER_CONTENT.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Header content',
	'he' => 'תוכן ה-header'
);
$messages[DotCorePageDAL::PAGE_PARENT_ID.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCorePageDAL::PAGE_PARENT_ID.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Page parent',
	'he' => 'אב הדף'
);
$messages[DotCorePageDAL::PAGE_ORDER.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCorePageDAL::PAGE_ORDER.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Page order',
	'he' => 'סדר הדף'
);
$messages[DotCoreContentDAL::CONTENT_TEXT.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreContentDAL::CONTENT_TEXT.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Page content',
	'he' => 'תוכן הדף'
);
$messages['LabelPageName'] = array();
$messages['LabelPageName'][DotCoreMessages::VALUES] = array(
	'en' => 'Page name',
	'he' => 'שם הדף'
);
$messages['LabelPageUrl'] = array();
$messages['LabelPageUrl'][DotCoreMessages::VALUES] = array(
	'en' => 'Page address',
	'he' => 'כתובת הדף'
);
$messages['LabelPageAppearsInNav'] = array();
$messages['LabelPageAppearsInNav'][DotCoreMessages::VALUES] = array(
	'en' => 'Appears in navigation',
	'he' => 'מופיע בניווט'
);
$messages['LabelPageAdd'] = array();
$messages['LabelPageAdd'][DotCoreMessages::VALUES] = array(
	'en' => 'Add page',
	'he' => 'הוסף דף'
);
$messages['LabelPageEdit'] = array();
$messages['LabelPageEdit'][DotCoreMessages::VALUES] = array(
	'en' => 'Edit page',
	'he' => 'ערוך דף'
);
$messages['LabelParentPage'] = array();
$messages['LabelParentPage'][DotCoreMessages::VALUES] = array(
	'en' => 'Root page',
	'he' => 'דף אב'
);

/************************************************* Page Titles **********************************************************************/

$messages['TitlePagesEdit'] = array();
$messages['TitlePagesEdit'][DotCoreMessages::VALUES] = array(
	'en' => 'Pages editing',
	'he' => 'עריכת דפים'
);
$messages['TitlePageEdit'] = array();
$messages['TitlePageEdit'][DotCoreMessages::VALUES] = array(
	'en' => 'Page editing',
	'he' => 'עריכת דף'
);
$messages['TitlePageInsert'] = array();
$messages['TitlePageInsert'][DotCoreMessages::VALUES] = array(
	'en' => 'Page addition',
	'he' => 'הוספת דף'
);

/************************************************* Table Headers ********************************************************************/

$messages['TableHeaderPreview'] = array();
$messages['TableHeaderPreview'][DotCoreMessages::VALUES] = array(
	'en' => 'Preview',
	'he' => 'צפיה'
);
$messages['TableHeaderDefaultPage'] = array();
$messages['TableHeaderDefaultPage'][DotCoreMessages::VALUES] = array(
	'en' => 'Default Page',
	'he' => 'דף ראשי'
);
$messages['TableHeaderMove'] = array();
$messages['TableHeaderMove'][DotCoreMessages::VALUES] = array(
	'en' => 'Move',
	'he' => 'הזזה'
);

$messages['MoveUp'] = array();
$messages['MoveUp'][DotCoreMessages::VALUES] = array(
	'en' => 'Move Up',
	'he' => 'הזז כלפי מעלה'
);
$messages['MoveDown'] = array();
$messages['MoveDown'][DotCoreMessages::VALUES] = array(
	'en' => 'Move Down',
	'he' => 'הזז כלפי מטה'
);

/************************************************* Page Explanations ****************************************************************/

$messages[DotCorePageDAL::PAGE_URL.DotCoreFormGenerator::MESSAGE_EXPLANATION] = array();
$messages[DotCorePageDAL::PAGE_URL.DotCoreFormGenerator::MESSAGE_EXPLANATION][DotCoreMessages::VALUES] = array(
	'en' => 'This will appear on the address bar, supports any language.',
	'he' => 'מה שיופיע בשורת הכתובת כשיכנסו לדף, ניתן לרשום שמות בעברית'
);
$messages[DotCorePageDAL::PAGE_TITLE.DotCoreFormGenerator::MESSAGE_EXPLANATION] = array();
$messages[DotCorePageDAL::PAGE_TITLE.DotCoreFormGenerator::MESSAGE_EXPLANATION][DotCoreMessages::VALUES] = array(
	'en' => 'Will appear in the title bar besides the name of the website, and in the results on search engines.',
	'he' => 'מה שיופיע ליד שם הדפדפן ובתוצאות מנועי חיפוש'
);
$messages['ExplanationPageGalleryAddition'] = array();
$messages['ExplanationPageGalleryAddition'][DotCoreMessages::VALUES] = array(
	'en' => 'In order to add a gallery into the page, insert the following code into the content: {SlideGallery name="GalleryName"}, where "GalleryName" is substituted with the name of the gallery you want to add.',
	'he' => 'להוספת גלריה לדף, הכניסו את הקוד הבא: {גלריית_דף שם="שם_הגלריה"} במקום בדף בו אתם רוצים את הגלריה. לשנות את "שם_הגלריה" (לשמור על הגרשיים) בשם הגלריה שברצונכם להוסיף'
);

/*********************************************** Template Messages *******************************************/

$messages['TableHeaderTemplateName'] = array();
$messages['TableHeaderTemplateName'][DotCoreMessages::VALUES] = array(
	'he' => 'שם התבנית',
	'en' => 'Template Name'
);

$messages['TableHeaderTemplateFolder'] = array();
$messages['TableHeaderTemplateFolder'][DotCoreMessages::VALUES] = array(
	'he' => 'תקיית התבנית',
	'en' => 'Template Folder'
);

$messages['TableHeaderEditContents'] = array();
$messages['TableHeaderEditContents'][DotCoreMessages::VALUES] = array(
	'he' => 'עריכת תכנים',
	'en' => 'Edit Contents'
);

$messages['TableHeaderEditConfiguration'] = array();
$messages['TableHeaderEditConfiguration'][DotCoreMessages::VALUES] = array(
	'he' => 'עריכת הגדרות',
	'en' => 'Edit Configuration'
);

$messages['ErrorTemplateNotFound'] = array();
$messages['ErrorTemplateNotFound'][DotCoreMessages::VALUES] = array(
	'he' => 'התבנית המבוקשת לא נמצאה',
	'en' => 'The requested template was not found.'
);

$messages['ErrorTemplateConfigurationEditFail'] = array();
$messages['ErrorTemplateConfigurationEditFail'][DotCoreMessages::VALUES] = array(
	'he' => 'עריכת ההגדרות נכשלה, אנא נסה/י שנית.',
	'en' => 'The configuration edit failed, please try again.'
);

$messages['ErrorTemplateMessagesEditFail'] = array();
$messages['ErrorTemplateMessagesEditFail'][DotCoreMessages::VALUES] = array(
	'he' => 'עריכת התכנים נכשלה, אנא נסה/י שנית.',
	'en' => 'The messages edit failed, please try again.'
);

?>

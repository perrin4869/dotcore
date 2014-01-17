<?php

// Messages for GalleryManager

$messages = array();

$messages['AdminTitleMainGallery'] = array();
$messages['AdminTitleMainGallery'][DotCoreMessages::VALUES] = array(
	'he' => 'גלריה',
	'en' => 'Gallery'
);

$messages['AdminTitleManageGallery'] = array();
$messages['AdminTitleManageGallery'][DotCoreMessages::VALUES] = array(
	'he' => 'ניהול גלריות',
	'en' => 'Gallery Management'
);

/************************************************* Gallery Titles ********************************************************************/

$messages['TitleAddGallery'] = array();
$messages['TitleAddGallery'][DotCoreMessages::VALUES] = array(
	'he' => 'הוספת גלריה',
	'en' => 'Gallery Addition'
);

$messages['TitleAddGalleryImage'] = array();
$messages['TitleAddGalleryImage'][DotCoreMessages::VALUES] = array(
	'he' => 'הוספת תמונה לגלריה',
	'en' => 'Image Addition'
);

$messages['TitleEditGallery'] = array();
$messages['TitleEditGallery'][DotCoreMessages::VALUES] = array(
	'he' => 'עריכת גלריות',
	'en' => 'Gallery Editing'
);

$messages['TableHeaderGalleryName'] = array();
$messages['TableHeaderGalleryName'][DotCoreMessages::VALUES] = array(
	'he' => 'שם הגלריה',
	'en' => 'Gallery Name'
);

$messages['TableHeaderGalleryFolder'] = array();
$messages['TableHeaderGalleryFolder'][DotCoreMessages::VALUES] = array(
	'he' => 'תקיית הגלריה',
	'en' => 'Gallery Folder'
);

$messages['TableHeaderGalleryEditImages'] = array();
$messages['TableHeaderGalleryEditImages'][DotCoreMessages::VALUES] = array(
	'he' => 'עריכת תמונות',
	'en' => 'Image Editing'
);

$messages['LabelSyncButton'] = array();
$messages['LabelSyncButton'][DotCoreMessages::VALUES] = array(
	'he' => 'סינכרון תמונות',
	'en' => 'Images synchronizing'
);

$messages['TableHeaderGalleryImageTitle'] = array();
$messages['TableHeaderGalleryImageTitle'][DotCoreMessages::VALUES] = array(
	'he' => 'הכותרת',
	'en' => 'Title'
);

$messages['TableHeaderGalleryImage'] = array();
$messages['TableHeaderGalleryImage'][DotCoreMessages::VALUES] = array(
	'he' => 'התמונה',
	'en' => 'Image'
);

$messages['TableHeaderGalleryImageDescription'] = array();
$messages['TableHeaderGalleryImageDescription'][DotCoreMessages::VALUES] = array(
	'he' => 'התיאור',
	'en' => 'Description'
);

$messages['LabelBack'] = array();
$messages['LabelBack'][DotCoreMessages::VALUES] = array(
	'he' => 'חזרה',
	'en' => 'Back'
);

/************************************************* Gallery Messages ********************************************************************/

$messages['MessageSuccessfulAddition'] = array();
$messages['MessageSuccessfulAddition'][DotCoreMessages::VALUES] = array(
	'he' => 'הגלריה נוספה בהצלחה.',
	'en' => 'The gallery was added successfully'
);

$messages['MessageSuccessfulDeletion'] = array();
$messages['MessageSuccessfulDeletion'][DotCoreMessages::VALUES] = array(
	'he' => 'הגלריה נמחקה בהצלחה',
	'en' => 'The gallery was successfully deleted'
);

$messages['MessageGalleryNotFound'] = array();
$messages['MessageGalleryNotFound'][DotCoreMessages::VALUES] = array(
	'he' => 'הגלריה המבוקשת לא נמצאה',
	'en' => 'The requested gallery was not found'
);

$messages['MessageGalleryDeletionConfirm'] = array();
$messages['MessageGalleryDeletionConfirm'][DotCoreMessages::VALUES] = array(
	'he' => 'האם הינך בטוח כי ברצונך למחוק גלריה זו?\\nהתקייה עם התמונות של הגלריה יימחקו גם',
	'en' => 'Are you sure you want to delete this gallery?\\nAll the images in this gallery will be deleted.'
);

$messages['MessageConfirmImageDeletion'] = array();
$messages['MessageConfirmImageDeletion'][DotCoreMessages::VALUES] = array(
	'he' => 'האם הינך בטוח כי ברצונך למחוק תמונה זאת?',
	'en' => 'Are you sure you want to delete this image?'
);

$messages['MessageSuccessfulImageDeletion'] = array();
$messages['MessageSuccessfulImageDeletion'][DotCoreMessages::VALUES] = array(
	'he' => 'התמונה נמחקה בהצלחה',
	'en' => 'The image was successfully deleted'
);

$messages['MessageSuccessfulImageInsertion'] = array();
$messages['MessageSuccessfulImageInsertion'][DotCoreMessages::VALUES] = array(
	'he' => 'התמונה נוספה בהצלחה',
	'en' => 'The image was successfully added'
);

$messages['MessageGalleryImageNotFound'] = array();
$messages['MessageGalleryImageNotFound'][DotCoreMessages::VALUES] = array(
	'he' => 'התמונה המבוקשת לא נמצאה',
	'en' => 'The requested image was not found'
);


$messages[DotCoreGalleryDAL::GALLERY_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreGalleryDAL::GALLERY_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'שם הגלריה אינו יכול להיות ריק',
	'en' => 'The gallery name cannot be empty'
);

$messages[DotCoreGalleryDAL::GALLERY_UNIQUE_NAME.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION] = array();
$messages[DotCoreGalleryDAL::GALLERY_UNIQUE_NAME.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'שם הגלריה כבר בשימוש, בחר שם אחר',
	'en' => 'The gallery name is already in use, choose another name.'
);

$messages[DotCoreGalleryDAL::GALLERY_FOLDER.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreGalleryDAL::GALLERY_FOLDER.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'שם התקייה בה התמונות מאוחסנות אינו יכול להיות ריק.',
	'en' => 'The folder name cannot be empty'
);

$messages[DotCoreGalleryDAL::GALLERY_UNIQUE_FOLDER.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION] = array();
$messages[DotCoreGalleryDAL::GALLERY_UNIQUE_FOLDER.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'התקייה המבוקשת לגלריה כבר בשימוש, בחר שם אחר',
	'en' => 'The folder name submitted is already taken, please choose another name.'
);

/************************************************* Gallery Folder ********************************************************************/

$messages[DotCoreGalleryDAL::GALLERY_NAME.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreGalleryDAL::GALLERY_NAME.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'שם הגלריה',
	'en' => 'Gallery Name'
);

$messages[DotCoreGalleryDAL::GALLERY_FOLDER.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreGalleryDAL::GALLERY_FOLDER.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'תקיית הגלריה',
	'en' => 'Gallery Folder'
);

/************************************************* Gallery Image Labels ********************************************************************/

$messages[DotCoreGalleryImageDAL::GALLERY_IMAGE_PATH.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreGalleryImageDAL::GALLERY_IMAGE_PATH.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'התמונה',
	'en' => 'Image'
);

$messages[DotCoreGalleryImageDAL::GALLERY_IMAGE_DESC.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreGalleryImageDAL::GALLERY_IMAGE_DESC.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'תיאור',
	'en' => 'Description'
);

$messages[DotCoreGalleryImageDAL::GALLERY_IMAGE_TITLE.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreGalleryImageDAL::GALLERY_IMAGE_TITLE.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'כותרת',
	'en' => 'Title'
);

$messages[DotCoreGalleryImageDAL::GALLERY_IMAGE_GALLERY_ID.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreGalleryImageDAL::GALLERY_IMAGE_GALLERY_ID.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'he' => 'הגלריה',
	'en' => 'Gallery'
);

$messages[DotCoreGalleryImageDAL::GALLERY_IMAGE_PATH.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreGalleryImageDAL::GALLERY_IMAGE_PATH.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'התמונה אינה יכולה להיות ריקה',
	'en' => 'The image cannot be empty'
);

$messages[DotCoreGalleryImageDAL::GALLERY_IMAGE_UNIQUE_PATH.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION] = array();
$messages[DotCoreGalleryImageDAL::GALLERY_IMAGE_UNIQUE_PATH.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'he' => 'קיימת כבר תמונה בגלריה עם השם הנתון. אנא בחר/י שם אחר.',
	'en' => 'An image with the selected name already exists in this gallery, please choose another name.'
);


?>

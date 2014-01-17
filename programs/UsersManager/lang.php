<?php

// Messages for UsersManager

$messages = array();

$messages['AdminTitleMainAdmins'] = array();
$messages['AdminTitleMainAdmins'][DotCoreMessages::VALUES] = array(
	'en' => 'Manage Admins',
	'he' => 'ניהול מנהלים'
);

$messages['AdminTitleMainProfile'] = array();
$messages['AdminTitleMainProfile'][DotCoreMessages::VALUES] = array(
	'en' => 'Profile',
	'he' => 'פרופיל'
);
$messages['AdminTitleEditProfile'] = array();
$messages['AdminTitleEditProfile'][DotCoreMessages::VALUES] = array(
	'en' => 'Edit Profile ',
	'he' => 'עריכת פרופיל'
);
$messages['AdminTitleAddAdmins'] = array();
$messages['AdminTitleAddAdmins'][DotCoreMessages::VALUES] = array(
	'en' => 'Add Admins',
	'he' => 'הוספת מנהלים'
);
$messages['AdminTitleEditAdmins'] = array();
$messages['AdminTitleEditAdmins'][DotCoreMessages::VALUES] = array(
	'en' => 'Edit Admins',
	'he' => 'עריכת מנהלים'
);

/************************************************* User Profile Labels **************************************************************/

$messages[DotCoreUserDAL::USER_NAME.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreUserDAL::USER_NAME.DotCoreFormGenerator::MESSAGE_LABEL ][DotCoreMessages::VALUES] = array(
	'en' => 'Username',
	'he' => 'שם המשתמש'
);
$messages[DotCoreUserDAL::USER_PASSWORD.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreUserDAL::USER_PASSWORD.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Password',
	'he' => 'סיסמה'
);
$messages[DotCoreUserDAL::USER_PASSWORD.DotCoreFormGenerator::MESSAGE_VALIDATION_LABEL] = array();
$messages[DotCoreUserDAL::USER_PASSWORD.DotCoreFormGenerator::MESSAGE_VALIDATION_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Password verification',
	'he' => 'בדיקת סיסמה'
);
$messages[DotCoreUserDAL::USER_FIRST_NAME.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreUserDAL::USER_FIRST_NAME.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'First Name',
	'he' => 'שם פרטי'
);
$messages[DotCoreUserDAL::USER_LAST_NAME.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreUserDAL::USER_LAST_NAME.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Last Name',
	'he' => 'שם משפחה'
);
$messages[DotCoreUserDAL::USER_EMAIL.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreUserDAL::USER_EMAIL.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Email',
	'he' => 'אימייל'
);
$messages[DotCoreUserDAL::USER_PHONE.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreUserDAL::USER_PHONE.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Phone',
	'he' => 'טלפון'
);
$messages[DotCoreUserDAL::USER_LAST_LOGIN.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreUserDAL::USER_LAST_LOGIN.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Last Login',
	'he' => 'התחברות אחרונה'
);
$messages[DotCoreUserDAL::USER_DATE_CREATED.DotCoreFormGenerator::MESSAGE_LABEL] = array();
$messages[DotCoreUserDAL::USER_DATE_CREATED.DotCoreFormGenerator::MESSAGE_LABEL][DotCoreMessages::VALUES] = array(
	'en' => 'Registration Date',
	'he' => 'תאריך הווספות'
);
$messages['LabelRoles'] = array();
$messages['LabelRoles'][DotCoreMessages::VALUES] = array(
	'en' => 'Permissions',
	'he' => 'הרשאות'
);
$messages['LabelChangePassword'] = array();
$messages['LabelChangePassword'][DotCoreMessages::VALUES] = array(
	'en' => 'Change Password',
	'he' => 'שנה סיסמה'
);
$messages['LabelUserInsertUser'] = array();
$messages['LabelUserInsertUser'][DotCoreMessages::VALUES] = array(
	'en' => 'Add Admin',
	'he' => 'הוסף מנהל'
);

/************************************************* User Profile Messages ************************************************************/

$messages[DotCoreUserDAL::USER_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreUserDAL::USER_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'The username is empty.',
	'he' => 'שם המשתמש ריק.'
);
$messages[DotCoreUserDAL::USER_UNIQUE_USERNAME.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION] = array();
$messages[DotCoreUserDAL::USER_UNIQUE_USERNAME.DotCoreFormGenerator::MESSAGE_UNIQUE_KEY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'The username is already in use, choose another username',
	'he' => 'שם המשתמש כבר בשימוש, אנא בחר/י שם משתמש חדש.'
);
$messages[DotCoreUserDAL::USER_PASSWORD.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreUserDAL::USER_PASSWORD.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'The password cannot be empty.',
	'he' => 'הסיסמה לא יכולה להיות ריקה.'
);
$messages[DotCoreUserDAL::USER_PASSWORD.DotCoreFormGenerator::MESSAGE_PASSWORD_VALIDATION_EXCEPTION] = array();
$messages[DotCoreUserDAL::USER_PASSWORD.DotCoreFormGenerator::MESSAGE_PASSWORD_VALIDATION_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'The passwords do not match.',
	'he' => 'הסיסמאות לא תואמות.'
);
$messages[DotCoreUserDAL::USER_EMAIL.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreUserDAL::USER_EMAIL.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'The email is empty.',
	'he' => 'האימייל ריק.'
);
$messages[DotCoreUserDAL::USER_EMAIL.DotCoreFormGenerator::MESSAGE_INVALID_EMAIL_EXCEPTION] = array();
$messages[DotCoreUserDAL::USER_EMAIL.DotCoreFormGenerator::MESSAGE_INVALID_EMAIL_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'The email is not valid.',
	'he' => 'האימייל אינו חוקי.'
);
$messages[DotCoreUserDAL::USER_FIRST_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreUserDAL::USER_FIRST_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'The first name is empty.',
	'he' => 'השם הפרטי ריק.'
);
$messages[DotCoreUserDAL::USER_LAST_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreUserDAL::USER_LAST_NAME.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'The last name is empty.',
	'he' => 'שם המשפחה ריק.'
);
$messages[DotCoreUserDAL::USER_PHONE.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION] = array();
$messages[DotCoreUserDAL::USER_PHONE.DotCoreFormGenerator::MESSAGE_EMPTY_EXCEPTION][DotCoreMessages::VALUES] = array(
	'en' => 'The phone is empty.',
	'he' => 'הטלפון ריק.'
);

/************************************************* User Edit and Delete Messages ****************************************************/

$messages['MessageUserDeletionSucceed'] = array();
$messages['MessageUserDeletionSucceed'][DotCoreMessages::VALUES] = array(
	'en' => 'The user was deleted successfully.',
	'he' => 'המשתמש נמחק בהצלחה.'
);
$messages['MessageUserAdditionSucceed'] = array();
$messages['MessageUserAdditionSucceed'][DotCoreMessages::VALUES] = array(
	'en' => 'The user was added successfully.',
	'he' => 'המשתמש נוסף בהצלחה.'
);
$messages['MessageRegisterUserFailed'] = array();
$messages['MessageRegisterUserFailed'][DotCoreMessages::VALUES] = array(
	'en' => 'User addition failed. Try again.',
	'he' => 'הוספת המשתמש נכשלה.'
);
$messages['MessageUserCantDeleteHimself'] = array();
$messages['MessageUserCantDeleteHimself'][DotCoreMessages::VALUES] = array(
	'en' => 'An admin cannot delete himself',
	'he' => 'מנהל אינו יכול למחוק את עצמו.'
);
$messages['MessageGenericDeletionFailed'] = array();
$messages['MessageGenericDeletionFailed'][DotCoreMessages::VALUES] = array(
	'en' => 'User deletion failed, please try again.',
	'he' => 'מחיקת משתמש נכשלה. אנא נסה שוב.'
);
$messages['MessageConfirmUserDeletion'] = array();
$messages['MessageConfirmUserDeletion'][DotCoreMessages::VALUES] = array(
	'en' => 'Are you sure you want to delete this user?',
	'he' => 'האם הינך בטוח כי ברצונך למחוק משתמש זה?'
);

/************************************************* User Errors ****************************************************/

$messages['ErrorUserNotFound'] = array();
$messages['ErrorUserNotFound'][DotCoreMessages::VALUES] = array(
	'en' => 'The requested user was not found.',
	'he' => 'המשתמש המבוקש אינו נמצא.'
);

/************************************************* Admin Labels **********************************************************************/

$messages['LabelAdminAdvanced'] = array();
$messages['LabelAdminAdvanced'][DotCoreMessages::VALUES] = array(
	'en' => 'Advanced options',
	'he' => 'אפשרויות מתקדמות'
);

/************************************************* Change Password Labels ***********************************************************/

$messages['LabelOldPassword'] = array();
$messages['LabelOldPassword'][DotCoreMessages::VALUES] = array(
	'en' => 'Old password',
	'he' => 'סיסמה ישנה'
);
$messages['LabeNewPassword'] = array();
$messages['LabeNewPassword'][DotCoreMessages::VALUES] = array(
	'en' => 'New password',
	'he' => 'סיסמה חדשה'
);
$messages['LabelLabelNewPasswordConfirm'] = array();
$messages['LabelLabelNewPasswordConfirm'][DotCoreMessages::VALUES] = array(
	'en' => 'New password confirmation',
	'he' => 'בדיקת סיסמה חדשה'
);

?>

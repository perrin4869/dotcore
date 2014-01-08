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

</head>
<body>

<?php

$profiler->add('Start<br/><br/>');

for($j = 0; $j < 1; $j++)
{
    /*
    // Let's try the links!
    // One to many links
    $chamber_contact_bll = new ChamberEilatContactMemberBLL();
    $dotcore_contact_bll = new DotCoreContactMemberBLL();

    $link_restraint = new DotCoreDALRestraint();
    $link_restraint->AddRestraint(
        new DotCoreLinkRestraint(
            $dotcore_contact_bll->getFieldContactID(),
            $chamber_contact_bll->getFieldContactID()));

    $chamber_contact_bll->AddLink(
        new DotCoreOneToOneRelationship($dotcore_contact_bll, $link_restraint));

    $contacts = $chamber_contact_bll
        ->Fields(array(
                $dotcore_contact_bll->getFieldEmail(),
                $chamber_contact_bll->getFieldContactName(),
                $chamber_contact_bll->getFieldContactPhone()))
        ->Select();

    $count_contacts = count($contacts);
    for($i = 0; $i < $count_contacts; $i++){
        $contact = $contacts[$i];
        $dotcore_contact = $contact->GetDotCoreContactMember();

        echo '
            Contact ID:' . $dotcore_contact->getContactMemberID() . '<br />
            Contact Email: ' . $dotcore_contact->getContactMemberEmail() . '<br />
            Contact Name: ' . $contact->getContactMemberName() . '<br />
            Contact Phone: ' . $contact->getContactMemberPhone() .'
            <br /><br />';
    }
    *
     * 
     */

    /*
     *
    $query = '
        SELECT dotcore_contact_list.contact_id, dotcore_contact_list.email, chamber_eilat_contact_list.contact_name
        FROM dotcore_contact_list
        INNER JOIN chamber_eilat_contact_list on chamber_eilat_contact_list.contact_id = dotcore_contact_list.contact_id';
    $mysql = new DotCoreMySql();

    $mysql->PerformQuery($query);
    while($row = $mysql->FetchRow())
    {
        echo '
            Contact ID:' . $row['contact_id'] . '<br />
            Contact Email: ' . $row['email'] . '<br />
            Contact Name: ' . $row['contact_name'] . '
            <br /><br />';
    }
     */
     
}

// Let's try a one to many relation now

for($j = 0; $j < 1; $j++)
{
    // Let's try the links!
    // One to many links

    /*
     * 
    $pages_bll = new DotCorePageBLL();
    $languages_bll = new DotCoreLanguageBLL();

    $link_restraint = new DotCoreDALRestraint();
    $link_restraint->AddRestraint(
        new DotCoreLinkRestraint(
            $languages_bll->getFieldLanguageID(),
            $pages_bll->getFieldPageID()));

    $pages_bll->AddLink(
        new DotCoreOneToManyRelationship($languages_bll, $link_restraint));

    $pages = $pages_bll
        ->Fields(array(
                $pages_bll->getFieldName(),
                $pages_bll->getFieldUrl(),
                $languages_bll->getFieldLanguageCode()))
        ->Select();

    $count_pages = count($pages);
    for($i = 0; $i < $count_pages; $i++){
        $page = $pages[$i];
        $lang = $page->GetPageLanguage();

        echo '
            Page Name:' . $page->getName() . '<br />
            Page Url: ' . $page->getUrl() . '<br />
            Language Code: ' . $lang->GetLanguageCode() .'
            <br /><br />';
    }
     * 
     */
}

// Many to many links
for($j = 0; $j < 1; $j++)
{
    /*
    $admins_bll = new DotCoreAdminBLL();
    $users_bll = $admins_bll->LinkUsersDAL()->GetLinkedDAL();
    $roles_bll = $admins_bll->LinkRolesDAL()->GetLinkedDAL();
    $roles_multilang_bll = $roles_bll->LinkRolesMultilangDAL()->GetLinkedDAL();
    $languages_bll = $roles_multilang_bll->LinkLanguagesDAL()->GetLinkedDAL();

    $admins = $admins_bll
        ->Fields(array(
                $users_bll->getFieldUserName(),
                $users_bll->getFieldEmail(),
                $roles_bll->getFieldDesc(),
                $roles_multilang_bll->getFieldRoleName(),
                $languages_bll->getFieldLanguageCode()))
        ->Select();

    $count_admins = count($admins);
    for($i = 0; $i < $count_admins; $i++){
        $admin = $admins[$i];
        $user = $admin->GetUser();
        $roles = $admin->GetRoles();

        echo '
        <ul><li>
            Username:' . $user->getUserName() . '<br />
            User Email: ' . $user->getUserEmail() . '<br />
            Roles: <ul>';
            foreach($roles as $role)
            {
                echo '<li>Role Desc: '.$role->getRoleDesc().'</li>';
                $multilang_role_properties = $role->GetRolesMultilanguageProperties();
                echo '<ul>';
                foreach($multilang_role_properties as $lang_role_properties)
                {
                    echo '
                        <li>
                            Lang ID: ' . $lang_role_properties->getLanguageID() . '<br />
                            Lang Code: ' . $lang_role_properties->GetLanguage()->getLanguageCode() . '<br />
                            Role Name: ' . $lang_role_properties->getRoleName() .'
                        </li>';
                }
                echo '</ul>';
            }
            echo '</ul>';
        echo '
        </li></ul>';
    }
     * 
     */
}

$profiler->end();
// echo $profiler->get_result();

?>
</body>
</html>
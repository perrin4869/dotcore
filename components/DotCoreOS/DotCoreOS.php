<?php
// +------------------------------------------------------------------------+
// | DotCoreOS.php                                                          |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2003-2009. All rights reserved.          |
// | Version       0.02                                                     |
// | Last modified 05/03/2010                                               |
// | Email         juliangrinblat@gmail.com                                 |
// | Web           http://www.dotcore.co.il                                 |
// +------------------------------------------------------------------------+

/**
 * Class DotCoreOS
 * Class used to manage the resources of the whole system
 *
 * @version   0.01
 * @author    Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
class DotCoreOS extends DotCoreObject
{
    private function __construct()
    {
        
    }

    /**
     * Contains the currently running instance of the OS
     * @var DotCoreOS
     */
    private static $current_instance = NULL;

    /**
     * Holds the instance of the currently running program
     * @var DotCoreProgram
     */
    private $requested_program = NULL;

    /**
     * Holds the current language of the OS
     * @var DotCoreLanguageRecord
     */
    private $language = NULL;

    /**
     * Holds the messages being used by this instance of the OS
     * @var array
     */
    private $messages = NULL;

    /**
     * Holds the configuration for the panel
     * @var DotCoreConfiguration
     */
    private $configuration = NULL;

    /**
     * Holds the admin currently running the OS
     * @var DotCoreAdminRecord
     */
    private $admin = NULL;

    /**
     *
     * @var array
     */
    private $errors = array();

    private static function CreateInstance()
    {
        if(self::$current_instance == NULL)
        {
            self::$current_instance = new DotCoreOS();
            self::$current_instance->Initilize();
        }

        return self::$current_instance;
    }

    /**
     * Gets the current singleton instance of DotCoreOS
     * @return DotCoreOS
     */
    public static function GetInstance()
    {
        if(self::$current_instance == NULL)
        {
            self::CreateInstance();
        }
        return self::$current_instance;
    }

    public function Initilize()
    {
        clean_request();
        // Initilize the database data
        DotCoreMySql::SetConnectionData('localhost', DotCoreConfig::$DATABASE_USERNAME, DotCoreConfig::$DATABASE_PASSWORD, DotCoreConfig::$DATABASE_NAME);
        $messages = $this->GetMessages();

        /*
         *
         * Login related requests:
         *
         */

        if(!empty($_REQUEST['login_submit']))
        {
            $admin = DotCoreAdminBLL::IsCorrectLogin($_REQUEST['login_username'], $_REQUEST['login_password']);
            if($admin == NULL)
            {
                array_push($this->errors, $messages['FailedLogin']);
            }
            else
            {
                DotCoreAdminBLL::Login($admin);
            }
        }

        if(!empty($_REQUEST['logoff']))
        {
            DotCoreAdminBLL::Logoff();
            header('Location: index.php');
            exit;
        }

        $this->admin = &DotCoreAdminBLL::GetCurrent(); // After all the operations (login and logoff) just get the resulting admin
    }

    /**
     *
     * @return DotCoreAdminRecord
     */
    public function GetAdmin() {
        return $this->admin;
    }

    public function IsAdminLoggedIn() {
        return $this->admin->IsEmpty() != TRUE;
    }

    /**
     *
     * @param string $string
     * @return DotCoreProgram
     */
    public function GetProgramInstance($string)
    {
        try {
            return FactoryProgram::GetInstance()->SetProgramClass($string)->Create();
        }
        catch(PermissionDeniedException $ex) {
            $role_bll = new DotCoreRoleBLL();
            $roles_link = $role_bll->LinkRolesMultilang();
            $roles_multilang_bll = new DotCoreRoleMultilangBLL($roles_link->GetLinkedDAL());
            $roles_link->SetKeyField($roles_multilang_bll->getFieldLanguageID());
            $roles_multilang_path = DotCoreRoleMultilangDAL::ROLE_MULTILANG_LINK.'.';
            $role = $role_bll
                ->Fields(
                    array(
                        $roles_multilang_path.DotCoreRoleMultilangDAL::ROLE_NAME
                    )
                )
                ->ByRoleDescription($ex->getRequiredRole())
                ->SelectFirstOrNull();

            $messages = $this->GetMessages();
            if($role == NULL) {
                $message = $messages['UnknownRoleRequired'];
                $message = str_replace('[role]', $ex->getRequiredRole(), $message);
                array_push($this->errors, $message);
            }
            else {
                $message = $messages['RoleRequired'];
                $language = $this->GetLanguage();
                $role_multilang = DotCoreRoleBLL::GetRolesMultilanguageProperties($role);
                $role_multilang = $role_multilang[$language->getLanguageID()];
                if($role_multilang != NULL) {
                    $message = str_replace('[role]', $role_multilang->getRoleName(), $message);
                }
                else {
                    $message = str_replace('[role]', $ex->getRequiredRole(), $message);
                }
                array_push($this->errors, $message);
            }
        }
        catch(Exception $ex) {
            array_push($this->errors, $ex->getMessage());
        }

        return NULL;
    }

    /**
     *
     * @return DotCoreProgram
     */
    public function GetRequestedProgram()
    {
        if($this->requested_program == NULL)
        {
            $this->requested_program = $this->GetProgramInstance($_REQUEST['program']);
        }
        return $this->requested_program;
    }

    /**
     * Gets the language of the OS
     * @return DotCoreLanguageRecord
     */
    public function GetLanguage()
    {
         if($this->language == NULL)
         {
             $lang_code = $this->GetConfiguration()->GetValue('admin_language');
             $lang_bll = new DotCoreLanguageBLL();
             $this->language = $lang_bll
                ->ByLanguageCode($lang_code)
                ->Fields($lang_bll->GetDAL()->GetFieldsDefinitions())
                ->SelectFirstOrNull();

             if($this->language == NULL)
             {
                 throw new Exception('Fatal Error - Could not determine the language.');
             }
         }
         return $this->language;
    }
	
    public function GetMessages()
    {
        if($this->messages == NULL)
        {
            $language = $this->GetLanguage();
            $this->messages = DotCoreMessages::GetMessages(DotCoreConfig::$ADMIN_PATH.'lang.php', $language->getLanguageCode());
        }

        return $this->messages;
    }

    public function GetConfiguration() {
        if($this->configuration == NULL) {
            $this->configuration = new DotCoreConfiguration(DotCoreConfig::$ADMIN_PATH.'/admin_configuration.php');
        }
        return $this->configuration;
    }

    public function GetErrors() {
        return $this->errors;
    }

    /**
     * Gets the directory from which .CORE OS is being run
     * @return string
     */
    public function GetDirectory()
    {
        return dirname($_SERVER['PHP_SELF']);
    }
}

?>
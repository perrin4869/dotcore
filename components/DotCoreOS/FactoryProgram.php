<?php
	
/**
 * A class used for creating the correct DotCoreProgram
 * @author perrin
 *
 */
class FactoryProgram extends FactoryBase
{
    private function __construct() { }

    private static $instance = NULL;
    private static $loaded_programs = array();
    private $program_class;

    /**
     * Gets the singleton instance of this Factory
     * @return FactoryProgram
     */
    public static function GetInstance()
    {
        if(self::$instance == NULL)
        {
            $class = __CLASS__;
            self::$instance = new $class;
        }

        return self::$instance;
    }

    public function GetProgramClass()
    {
        return $this->program_class;
    }

    /**
     * Sets the name of the program which the user wants the FactoryProgram to return an instance of
     * @param string $class
     * @return FactoryProgram
     */
    public function SetProgramClass($class)
    {
        $this->program_class = $class;
        return $this;
    }

    /**
     * Tries to load program by the name $programName
     * @param string $program_class
     * @return True on successful load, false otherwise
     */
    public static function LoadProgram($program_class)
    {
        $program_bll = new DotCoreProgramBLL();
        $program = $program_bll
            ->ByProgramClass($program_class)
            ->Fields(
                array(
                    $program_bll->getFieldProgramClass(),
                    $program_bll->getFieldProgramDomainPath(),
                    $program_bll->getFieldProgramName(),
                    $program_bll->getFieldProgramServerPath()
                )
            )
            ->SelectFirstOrNull();

        if($program == NULL)
        {
            return FALSE;
        }

        $include_path = DotCoreProgramBLL::GetProgramServerFolderPath($program).'/'.$program_class.'.php';
        if(file_exists($include_path))
        {
            require($include_path);
            // Set as loaded
            self::$loaded_programs[$program_class] = $program;
            return TRUE;
        }
        return FALSE;
    }

    public static function ProgramLoaded($program_name)
    {
        return key_exists($program_name, self::$loaded_programs);
    }

    public static function GetProgramRecord($program_class) {
        return self::$loaded_programs[$program_class];
    }

    /**
     *
     * @return DotCoreProgram
     */
    public function Create()
    {
        $program_class = $this->program_class;
        if(!self::ProgramLoaded($program_class))
        {
            $loaded = self::LoadProgram($program_class);
        }
        else
        {
            $loaded = true;
        }

        if($loaded)
        {
            return new $program_class(self::GetProgramRecord($program_class));
        }
        else
        {
            return NULL;
        }
    }
}

?>
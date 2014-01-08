<?php

class FileNotFoundException extends Exception
{
    public function __construct($message = "")
    {
        if(empty($message))
        {
            $message = "FileNotFoundException";
        }
        parent::__construct($message);
    }
}

/**
 * DotCoreFile
 *
 * @author perrin
 */
class DotCoreFile {

    public static function GetExtension($file_name) {
        $extension_place = strrpos($file_name, ".");
        $ex = strtolower(substr($file_name, $extension_place + 1));
        return $ex;
    }

    public static function Rename($original,$destination) {
        if(!file_exists($original)) {
            throw new FileNotFoundException();
        }
        $dst_dir = substr($destination, 0, strrpos($destination, '/')+1);
        if(!is_dir($dst_dir)) {
            mkdir($dst_dir, 0777, TRUE);
        }
        rename($original, $destination);
    }

}
?>

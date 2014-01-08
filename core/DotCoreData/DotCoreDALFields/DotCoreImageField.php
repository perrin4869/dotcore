<?php

class InvalidFileTypeException extends DotCoreException {}
class FileUploadException extends DotCoreException {}
class MaxmimumFileSizeException extends DotCoreException
{

    /**
     * Constructor for the case in which the file submitted to the DotCoreImageField has
     * a size greater than the maximum allowed size
     * @param int $max_size
     * @param int $sent_size
     * @param string $message NULLABLE
     */
    public function  __construct($max_size, $sent_size, $message = NULL) {
        parent::__construct($message);

        $this->max_size = $max_size;
        $this->sent_size = $sent_size;
    }

    private $max_size;
    private $sent_size;

    public function GetMaxSize()
    {
        return $this->max_size;
    }

    public function GetSentSize()
    {
        return $this->sent_size;
    }

}

/**
 * Class used to represent any image database field
 * @author perrin
 *
 */
class DotCoreImageField extends DotCorePlainStringField
{
    /**
     * Constructor of DotCoreImageField
     * @param string $field_name
     * @param DotCoreDAL $dal
     * @param string $dest_folder The destination folder of this DotCoreImageField relative to DOCUMENT_ROOT
     * @param $rename If true, the name will be renamed to a unique name
     * @param $is_nullable
     */
    public function __construct(
            $field_name,
            DotCoreDAL $dal,
            $dest_folder = NULL,
            $rename = FALSE,
            $is_nullable = TRUE,
            $max_file_size = 0 // Any size by default
            )
    {
        parent::__construct($field_name, $dal, $is_nullable);

        $this->rename = $rename;
        $this->dest_folder = $dest_folder;
        $this->max_file_size = $max_file_size;

        $dal->RegisterEvent(
            DotCoreDAL::EVENT_ROLLBACKING,
            new DotCoreEventHandler(
                array($this, 'Rollback')
            )
        );

        $dal->RegisterEvent(
            DotCoreDAL::EVENT_INSERTING,
            new DotCoreEventHandler(
                array($this, 'Inserting')
            )
        );

        $dal->RegisterEvent(
            DotCoreDAL::EVENT_DELETING,
            new DotCoreEventHandler(
                array($this, 'Deleting')
            )
        );

        $dal->RegisterEvent(
            DotCoreDAL::EVENT_DELETED,
            new DotCoreEventHandler(
                array($this, 'Deleted')
            )
        );

        $dal->RegisterEvent(
            DotCoreDAL::EVENT_UPDATING,
            new DotCoreEventHandler(
                array($this, 'Updating')
            )
        );

        $dal->RegisterEvent(
            DotCoreDAL::EVENT_UPDATED,
            new DotCoreEventHandler(
                array($this, 'Updated')
            )
        );
    }
    
    private $dest_folder;
    private $rename;
    private $max_file_size;

    private $allowed_types = array('jpg', 'jpeg', 'gif', 'png');

    const UPLOADED_FILE_STORE_KEY = '_uploaded_image_store_key';
    const ORIGINAL_DESTINATION_STORE_KEY = '_original_destination_store_key';
    const UPLOADED_FILE_CONFIRMATION_STORE_KEY = '_uploaded_image_confirmation_store_key';

    public function AddAllowedType($type)
    {
        $type = strtolower($type);
        if(!array_search($type, $this->allowed_types))
        {
            array_push($this->allowed_types, $type);
        }
    }

    public function RemoveAllowedType($type)
    {
        $type = strtolower($type);
        $key = array_search($type, $this->allowed_types);
        if($key)
        {
            unset($this->allowed_types[$key]);
            $this->allowed_types = array_values($this->allowed_types);
        }
    }

    /**
     * Gets the destination folder of this image relative to DOCUMENT_ROOT
     * @param DotCoreDataRecord $record
     * @return string
     */
    public function getDestinationFolder(DotCoreDataRecord $record = NULL) {
        return $this->dest_folder;
    }

    /**
     * Gets the original destination folder of the image relative to DOCUMENT_ROOT
     * @param DotCoreDataRecord $record
     * @return string
     */
    public function getOriginalDestinationFolder(DotCoreDataRecord $record = NULL) {
        return $this->dest_folder;
    }

    /**
     * Returns true if this image is renamed to a unique name, false otherwise
     * @return bool
     */
    public function isRenamed()
    {
        return $this->rename;
    }

    /**
     * Gets the maximum permitted size of the file that is permitted by this field in bytes (1kb = 1000bytes)
     * @return int
     */
    public function getMaxFileSize() {
        return $this->max_file_size;
    }
	
    /**
     * Sets the destination folder of this image, relative to DOCUMENT_ROOT
     * @param string $dest_folder
     * @return void
     */
    public function setDestinationFolder($dest_folder)
    {
        $this->dest_folder = $dest_folder;
    }

    /**
     * If true, the name of this image will be renamed to a unique name, else it'll keep the original name
     * @param $bool
     * @return void
     */
    public function setRename($bool)
    {
        $this->rename = $bool;
    }

    /**
     * Sets the maximum files size in bytes
     * @param int $size
     */
    public function setMaxFileSize($size) {
        $this->max_file_size = $size;
    }

    public function IsValidFilename($file_name) {
        return in_array(DotCoreFile::GetExtension($file_name), $this->allowed_types);
    }

    public function Validate(DotCoreDataRecord $record, &$val)
    {
        $file_array = NULL;
        if(is_array($val))
        {
            $file_array = $val;
            $val = $val['name'];
        }

        $parent_validation = parent::Validate($record, $val);
        
        if(!$this->IsEmpty($val))
        {
            if(!$file_array)
            {
                // If we were given a simple string, assume we're choosing a file already in the filesystem
                if(!file_exists($_SERVER['DOCUMENT_ROOT'].$this->getDestinationFolder($record).$val))
                {
                    throw new FileNotFoundException();
                }

                return $parent_validation && TRUE;
            }
            else
            {
                // Check that the type of the image is valid
                $ext = strtolower(DotCoreFile::GetExtension($val));

                if(in_array($ext, $this->allowed_types) == FALSE)
                {
                    throw new InvalidFileTypeException();
                }

                // Rename only if this is being edited, or if this is new, and do NOT edit if we're just filling from Database
                if($this->isRenamed())
                {
                    $val =  microtime(TRUE) . rand(0,1000) . '.' . $ext; // Renamed
                }

                $max_file_size = $this->getMaxFileSize();
                if($max_file_size > 0 && $file_array['size'] > $max_file_size)
                {
                    throw new MaxmimumFileSizeException($max_file_size, $file_array['size']);
                }

                $record->StoreValue(self::UPLOADED_FILE_STORE_KEY, $file_array);
                return $parent_validation && TRUE;
            }
        }
        else
        {
            // Keep the old value
            return FALSE;
        }
    }

    public function IsEmpty($val)
    {
        return is_array($val) ? empty($val['name']) : empty($val);
    }

    /**
     * Returns true if $val1 == $val2, false otherwise
     * By definition, two DotCoreImageField values are equal if and only if
     * they're two equal filename strings
     * @param array | string $val1
     * @param array | string $val2
     * @return boolean
     */
    public function Equals($val1, $val2)
    {
        // The two ways two values are not equal are:
        // 1- We got a File Upload array, so we also got a new file
        // 2- The filename got changed without getting a new file
        return !(
            // Check for File Upload arrays
            (is_array($val1) && !$this->IsEmpty($val1)) ||
            (is_array($val2) && !$this->IsEmpty($val2)) ||
            // If they're simple strings - check if they're equal or not
            $val1 != $val2);
    }

    protected function GetFileName($val)
    {
        if(is_array($val)) {
            return $val['name'];
        }
        return $val;
    }

    /**
     * If passed a file upload array - it returns the temporary location of the file on the server
     * If passed a DotCoreDataRecord it returns the location of the image of the record on the server
     * If passed a string - assuming it's the name of the file, its absolute location is returned
     * @param array | DotCoreDataRecord | string $val
     * @return string
     */
    protected function GetFilePath($val)
    {
        if(is_array($val)){
            return $val['tmp_name'];
        }
        elseif($val instanceof DotCoreDataRecord) {
            return $_SERVER['DOCUMENT_ROOT'] . $this->getDestinationFolder($val) . $val->GetField($this->GetFieldName());
        }
        else {
            return $_SERVER['DOCUMENT_ROOT'] . $this->getDestinationFolder() . '/' . $val;
        }
    }

    public function HasUploadedFileStored(DotCoreDataRecord $record) {
        return $record->HasStoredValue(self::UPLOADED_FILE_STORE_KEY);
    }

    public function MoveUploadedFile(DotCoreDataRecord $record) {
        $file_array = $record->RetrieveValue(self::UPLOADED_FILE_STORE_KEY);
        $val = $record->GetField($this->GetFieldName());

        // Check that the directory exists
        $save_directory = $_SERVER['DOCUMENT_ROOT'] . $this->getDestinationFolder($record);
        if(!is_dir($save_directory))
        {
            mkdir($save_directory, 0777, TRUE);
        }

        // Attempt to move the file now
        $save_path = $save_directory . $val;
        if(!move_uploaded_file($file_array['tmp_name'], $save_path))
        {
            throw new FileUploadException();
        }
        $record->DeleteStoredValue(self::UPLOADED_FILE_STORE_KEY);
    }

    /*
     *
     * Event handlers:
     *
     */

    public function Inserting(DotCoreDataRecord $record)
    {
        if($this->HasUploadedFileStored($record)){
            $this->MoveUploadedFile($record);
        }
    }

    public function Rollback(DotCoreDataRecord $record)
    {
        $edited_fields = $record->GetEditedFields();
        $field_name = $this->GetFieldName();
        if(in_array($field_name, $edited_fields))
        {
            // We have moved a new file, delete it
            $path = $this->GetFilePath($record);
            if(file_exists($path))
            {
                unlink($path);
            }
        }
    }

    public function Deleting(DotCoreDataRecord $record)
    {
        // Make sure the image file field is loaded before deleting - otherwise, it won't be possible to delete
        // the remaining file from the filesystem
        $field_name = $this->GetFieldName();
        if(!$record->HasFieldLoaded($field_name))
        {
            $record->LoadField($field_name); // Loads the missing field
        }
        // Save the original destination folder, in case information for it gets deleted
        $record->StoreValue(
            self::ORIGINAL_DESTINATION_STORE_KEY,
            $this->getOriginalDestinationFolder($record));
    }

    public function Deleted(DotCoreDataRecord $record)
    {
        $filename = $record->GetField($this->GetFieldName());
        $path =
            $_SERVER['DOCUMENT_ROOT'] .
            $record->RetrieveValue(self::ORIGINAL_DESTINATION_STORE_KEY) .
            $filename;
        if(file_exists($path) && is_file($path))
        {
            unlink($path);
        }
        $record->DeleteStoredValue(self::ORIGINAL_DESTINATION_STORE_KEY);
    }

    public function Updating(DotCoreDataRecord $record)
    {
        // Save the original destination folder
        if($this->HasUploadedFileStored($record)) {
            $this->MoveUploadedFile($record);
            $record->StoreValue(self::UPLOADED_FILE_CONFIRMATION_STORE_KEY, TRUE);
        }
        else {
            $record->StoreValue(self::UPLOADED_FILE_CONFIRMATION_STORE_KEY, FALSE);
        }
    }

    public function Updated(DotCoreDataRecord $record)
    {
        // The Algorithm:
        // 1. If the destinations didn't change:
        // 1.1. If the filename got changed, delete the old one
        // 1.2. Else do nothing (nothing changed)
        // 2. The destinations changed:
        // 2.1. If no new image was uploaded, move the old one to the new location
        // 2.2. If a new file was uploaded, delete the old one (the new one will be in the correct location)

        $field_name = $this->GetFieldName();
        $current_filename = $record->GetField($field_name);
        $original_filename = $record->GetOriginalValue($field_name);
        $dest_folder = $this->getDestinationFolder($record);
        $original_dest_folder = $this->getOriginalDestinationFolder($record);

        $original_path = $_SERVER['DOCUMENT_ROOT'] . $original_dest_folder . $original_filename;
        $current_path = $_SERVER['DOCUMENT_ROOT'] . $dest_folder . $current_filename;

        $uploaded_file = $record->RetrieveValue(self::UPLOADED_FILE_CONFIRMATION_STORE_KEY);
        $record->DeleteStoredValue(self::UPLOADED_FILE_CONFIRMATION_STORE_KEY);

        if($dest_folder == $original_dest_folder) {
            if(
                $current_filename != $original_filename &&
                !empty($original_filename)) {
                if(file_exists($original_path)) {
                    unlink($original_path);
                }
            }
        }
        else {
            if(!$uploaded_file) {
                if(file_exists($original_path)) {
                    DotCoreFile::Rename($original_path,$current_path);
                }
            }
            else {
                if(file_exists($original_path)) {
                    unlink($original_path);
                }
            }
        }

    }

}

?>
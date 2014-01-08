<?php

/**
 * DotCoreUploadFormElement - Defines a form element used for file uploading
 *
 * @author perrin
 */
class DotCoreUploadFormElement extends DotCoreInputFormElement {

    public function  __construct($name, $images_folder) {
        parent::__construct($name);

        $this->images_folder = $images_folder;
        $this->AddClass('image-preview-div');
    }

    /**
     * Stores the location relative to the document root where the images are uploaded
     * @var string
     */
    private $images_folder = NULL;

    public function GetImagesFolder() {
        return $this->images_folder;
    }

    public function SetImagesFolder($images_folder) {
        $this->images_folder = $images_folder;
    }

    public function __toString() {
        return '
            <input type="file" name="'.$this->GetName().'" id="'.$this->GetID().'" />';
    }

    public function  GetExtraContent() {
        $result = parent::GetExtraContent();
        
        $value = $this->GetSavedValue();
        if(
            !empty($value) &&
            file_exists($_SERVER['DOCUMENT_ROOT'].$this->images_folder.$value))
        {
            $result .= '<div class="'.$this->GetClass().'">';
            $ext = DotCoreFile::GetExtension($value);
            if($ext == "swf")
            {
                $result .= '
                    <object class="flash" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="400" height="200">
                        <param name="movie" value="'.$this->images_folder.$value.'" />
                        <!--[if !IE]>-->
                        <object type="application/x-shockwave-flash" data="'.$this->images_folder.$value.'" width="300" height="100">
                        <!--<![endif]-->
                        <div>
                            <p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
                        </div>
                        <!--[if !IE]>-->
                        </object>
                        <!--<![endif]-->
                    </object>';
            }
            elseif($ext == 'jpg' || $ext == 'gif' || $ext == 'png' || $ext == 'jpeg')
            {
                $result .= '
                    <img alt="" src="'.$this->images_folder.$value.'" />';
            }
            else
            {
                $result .= $value;
            }

            $result .= '</div>';
        }

        return $result;
    }

    // Override methods

    /**
     * Gets the value submitted by this element
     * @return mixed
     */
    public function GetSubmittedValue() {
        return $_FILES[$this->GetName()];
    }

    /**
     * Checks whether the value for this element was submitted
     * @return boolean
     */
    public function IsValueSet() { 
        return !empty($_FILES[$this->GetName()]['name']);
    }
}
?>

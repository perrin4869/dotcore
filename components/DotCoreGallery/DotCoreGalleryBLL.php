<?php

/**
 * DotCoreGalleryBLL - Contains the business logic of the galleries
 *
 * @author perrin
 */
class DotCoreGalleryBLL extends DotCoreBLL {

    /*
     *
     * Fields accessors
     *
     */

    public function getFieldGalleryID() {
        return $this->GetDAL()->GetField(DotCoreGalleryDAL::GALLERY_ID);
    }

    public function getFieldGalleryName() {
        return $this->GetDAL()->GetField(DotCoreGalleryDAL::GALLERY_NAME);
    }

    public function getFieldGalleryFolder() {
        return $this->GetDAL()->GetField(DotCoreGalleryDAL::GALLERY_FOLDER);
    }

    /*
     *
     * Overrides:
     *
     */

    /**
     *
     * @return DotCoreGalleryDAL
     */
    public static function GetDAL()
    {
        return self::GetDALHelper('DotCoreGalleryDAL');
    }

    /**
     * DotCoreGalleryBLL
     * @param <type> $id
     * @return <type>
     */
    public function ByGalleryID($id)
    {
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldGalleryID(), $id));

        $this->Restraints($restraints);
        return $this;
    }

    public function ByGalleryName($name)
    {
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldGalleryName(), $name));

        $this->Restraints($restraints);
        return $this;
    }

    public function SynchronizeGallery(DotCoreGalleryRecord $gallery) {
        $gallery_images_bll = new DotCoreGalleryImageBLL();
        $gallery_images = $gallery_images_bll
            ->Fields(
                array(
                    $gallery_images_bll->getFieldGalleryID(),
                    $gallery_images_bll->getFieldImageOrder(),
                    $gallery_images_bll->getFieldImagePath()
                )
            )
            ->ByGalleryID($gallery->getGalleryID())
            ->Select();

        // Check if the images exist on the filesystem, and if they don't, delete them
        $count_gallery_images = count($gallery_images);
        $base_path = self::GetGalleryRootPath($gallery);
        for($i = 0; $i < $count_gallery_images; $i++) {
            $curr_image = $gallery_images[$i];
            $path = $base_path . $curr_image->getImagePath();
            if(!file_exists($path)) {
                $gallery_images_bll->Delete($curr_image);
            }
        }

        // Get all the images in the folder, and find out
        $folder_images = $gallery_images_bll->GetImagesInGalleryFolder($gallery);
        $count_folder_images = count($folder_images);
        for($i = 0; $i < $count_folder_images; $i++) {
            // Find which of those are not in the database yet, and insert them
            $found = FALSE;
            for($j = 0; $j < $count_gallery_images; $j++) {
                if($folder_images[$i] == $gallery_images[$j]->getImagePath()) {
                    $found = TRUE;
                    break;
                }
            }
            if(!$found) {
                // Add them to the database
                $tmp_image = $gallery_images_bll->GetNewRecord();
                $tmp_image->setGalleryID($gallery->getGalleryID());
                $tmp_image->setImagePath($folder_images[$i]);
                $gallery_images_bll->Insert($tmp_image);
            }
        }
    }

    public static function GetGalleryRootPath(DotCoreGalleryRecord $gallery) {
        return $_SERVER['DOCUMENT_ROOT'].DotCoreGalleryDAL::GALLERY_FOLDER_PATH.$gallery->getGalleryFolder().'/';
    }

    public static function GetGalleryFolderUrl(DotCoreGalleryRecord $gallery) {
        return DotCoreGalleryDAL::GALLERY_FOLDER_PATH.$gallery->getGalleryFolder().'/';
    }

}
?>

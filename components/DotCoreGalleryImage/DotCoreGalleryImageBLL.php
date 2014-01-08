<?php

/**
 * DotCoreGalleryImageBLL - Contains the business logic of the gallery's images
 *
 * @author perrin
 */
class DotCoreGalleryImageBLL extends DotCoreBLL {

    /*
     *
     * Properties:
     *
     */
    

    /*
     *
     * Fields accessors
     *
     */

    /*
     *
     * @return DotCoreAutoIncrementingKey
     */
    public function getFieldImageID() {
        return $this->GetDAL()->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_ID);
    }

    /*
     *
     * @return DotCoreIntField
     */
    public function getFieldGalleryID() {
        return $this->GetDAL()->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_GALLERY_ID);
    }

    /*
     *
     * @return DotCorePlainStringField
     */
    public function getFieldImageTitle() {
        return $this->GetDAL()->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_TITLE);
    }

    /*
     *
     * @return DotCoreImageField
     */
    public function getFieldImagePath() {
        return $this->GetDAL()->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_PATH);
    }

    /*
     *
     * @return DotCorePlainStringField
     */
    public function getFieldImageDescription() {
        return $this->GetDAL()->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_DESC);
    }

    /*
     *
     * @return DotCoreIntField
     */
    public function getFieldImageOrder() {
        return $this->GetDAL()->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_ORDER);
    }
    
    /*
     *
     * Overrides:
     *
     */

    /**
     *
     * @return DotCoreGalleryImageDAL
     */
    public static function GetDAL()
    {
        return self::GetDALHelper('DotCoreGalleryImageDAL');
    }

    /**
     *
     * @param int $id
     * @return DotCoreGalleryImageBLL
     */
    public function ByImageID($id)
    {
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldImageID(), $id));

        $this->Restraints($restraints);
        return $this;
    }

    /**
     * 
     * @param int $id
     * @return DotCoreGalleryImageBLL
     */
    public function ByGalleryID($id)
    {
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldGalleryID(), $id));

        $this->Restraints($restraints);
        return $this;
    }

    /**
     *
     * @return DotCoreGalleryImageBLL
     */
    public function Ordered() {
        $order = new DotCoreDALSelectionOrder();
        $order
            ->AddOrderUnit(
                new DotCoreFieldSelectionOrder(
                    $this->getFieldImageOrder(),
                    DotCoreFieldSelectionOrder::DIRECTION_ASC));

        return $this->Order($order);
    }

    public function GetImagesInGalleryFolder(DotCoreGalleryRecord $gallery) {
        $path = DotCoreGalleryBLL::GetGalleryRootPath($gallery);
        $handle = opendir($path);
        if ($handle)
        {
            $result = array();
            $path_field = $this->getFieldImagePath();
            // var_dump(iconv_get_encoding('all'));
            /* This is the correct way to loop over the directory. */
            while (false !== ($file = readdir($handle)))
            {
                if($file != "." && $file != "..")
                {
                    //$file = iconv("UTF-8", "UTF-8", $file);
                    //$file = mb_convert_encoding($file, "UTF-8");
                    if(
                        $path_field->IsValidFilename($file)
                        )
                    {
                        array_push($result, $file);
                    }
                }
            }

            closedir($handle);
            return $result;
        }
        return array();
    }

    /*
     *
     * Link Methods
     *
     */

    /**
     * Links to the Galleries DAL
     *
     * @return DotCoreOneToManyRelationship
     */
    public function LinkGallery() {
        $link = DotCoreDAL::GetRelationship(DotCoreGalleryImageDAL::GALLERY_LINK);
        $this->GetDAL()->AddLink($link);
        return $link;
    }

    // Links results

    public function GetGallery(DotCoreGalleryImageRecord $gallery_image) {
        return $gallery_image->GetLinkValue(DotCoreGalleryImageDAL::GALLERY_LINK);
    }

}
?>

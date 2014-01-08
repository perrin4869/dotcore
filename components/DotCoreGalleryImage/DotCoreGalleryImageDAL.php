<?php

/**
 * DotCoreGalleryImageDAL - MySQL DAL for the images of galleries
 *
 * @author perrin
 */
class DotCoreGalleryImageDAL extends DotCoreDAL
{
	
    public function __construct()
    {
        parent::__construct(self::GALLERY_IMAGES_TABLE);

        $gallery_field = new DotCoreIntField(self::GALLERY_IMAGE_GALLERY_ID, $this, FALSE);
        $path_field = new DotCoreGalleryImageField(self::GALLERY_IMAGE_PATH, $this, FALSE, FALSE);

        $this->AddField(new DotCoreAutoIncrementingKey(self::GALLERY_IMAGE_ID, $this));
        $this->AddField($gallery_field);
        $this->AddField($path_field);
        $this->AddField(new DotCorePlainStringField(self::GALLERY_IMAGE_TITLE, $this, TRUE));
        $this->AddField(new DotCorePlainStringField(self::GALLERY_IMAGE_DESC, $this, TRUE));
        $this->AddField(new DotCoreOrderField(self::GALLERY_IMAGE_ORDER, $this, FALSE));

        $this->AddUniqueKey(
            self::GALLERY_IMAGE_UNIQUE_PATH,
            array(
                $path_field,
                $gallery_field
            )
        );

        $this->SetPrimaryField(self::GALLERY_IMAGE_ID);
    }

    /**
     *
     * @return DotCoreGalleryImageDAL
     */
    public static function GetInstance()
    {
        // throw new Exception('DotCoreGalleryImageDAL is not singleton');
        return parent::GetDALInstance(__CLASS__);
    }

    const GALLERY_IMAGES_TABLE = 'dotcore_galleries_images';

    const GALLERY_IMAGE_ID = 'gallery_image_id';
    const GALLERY_IMAGE_GALLERY_ID = 'gallery_image_gallery_id';
    const GALLERY_IMAGE_PATH = 'gallery_image_path';
    const GALLERY_IMAGE_TITLE = 'gallery_image_title';
    const GALLERY_IMAGE_DESC = 'gallery_image_desc';
    const GALLERY_IMAGE_ORDER = 'gallery_image_order';

    const GALLERY_GALLERY_IMAGES_LINK = 'gallery_gallery_images_link';

    const GALLERY_IMAGE_UNIQUE_PATH = 'gallery_image_unique_path';

    /**
     * Returns a record of DotCoreGalleryImageDAL
     * @return DotCoreGalleryImageRecord
     */
    public function GetRecord()
    {
        return new DotCoreGalleryImageRecord($this);
    }

}

DotCoreDAL::AddRelationship(new DotCoreOneToManyRelationship(
        DotCoreGalleryImageDAL::GALLERY_GALLERY_IMAGES_LINK,
        DotCoreGalleryDAL::GetInstance()->GetField(DotCoreGalleryDAL::GALLERY_ID),
        DotCoreGalleryImageDAL::GetInstance()->GetField(DotCoreGalleryImageDAL::GALLERY_IMAGE_GALLERY_ID)
    )
);

?>
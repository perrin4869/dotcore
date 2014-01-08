<?php

if(!isset($_REQUEST['img1']) || !isset($_REQUEST['img2']))
{
    header('HTTP/1.0 400 Bad Request');
    echo ('No links provided.');
    exit;
}

$gallery_image_bll = new DotCoreGalleryImageBLL();
$gallery_image_bll->Fields(array($gallery_image_bll->getFieldImageOrder()));
$img1 = $gallery_image_bll->ByImageID($_REQUEST['img1'])->SelectFirstOrNull();
$img2 = $gallery_image_bll->ByImageID($_REQUEST['img2'])->SelectFirstOrNull();

if($img1 == NULL || $img2 == NULL)
{
    header('HTTP/1.0 400 Bad Request');
    echo ('Invalid links provided.');
    exit;
}

$gallery_image_bll->BeginTransaction($img1);
$tmp_order = $img1->getImageOrder();
$img1->setImageOrder($img2->getImageOrder());
$img2->setImageOrder($tmp_order);
$gallery_image_bll->Save($img1);
$gallery_image_bll->Save($img2);
$gallery_image_bll->CommitTransaction($img1);

?>

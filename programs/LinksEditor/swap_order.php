<?php

if(!isset($_REQUEST['link1']) || !isset($_REQUEST['link2']))
{
    header('HTTP/1.0 400 Bad Request');
    echo ('No links provided.');
    exit;
}

// Include the basic things .CORE scripts require
include ($_SERVER['DOCUMENT_ROOT'] . '/admin/admin_include.php');

$link_bll = new DotCoreLinkBLL();
$link_bll->Fields(array($link_bll->getFieldOrder()));
$link1 = $link_bll->ByLinkID($_REQUEST['link1'])->SelectFirstOrNull();
$link2 = $link_bll->ByLinkID($_REQUEST['link2'])->SelectFirstOrNull();

if($link1 == NULL || $link2 == NULL)
{
    header('HTTP/1.0 400 Bad Request');
    echo ('Invalid links provided.');
    exit;
}

$link_bll->BeginTransaction($link1);
$tmp_order = $link1->getLinkOrder();
$link1->setLinkOrder($link2->getLinkOrder());
$link2->setLinkOrder($tmp_order);
$link_bll->Save($link1);
$link_bll->Save($link2);
$link_bll->CommitTransaction($link1);

?>

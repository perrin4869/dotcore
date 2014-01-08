<?php

if(!isset($_REQUEST['page1']) || !isset($_REQUEST['page2']))
{
    header('HTTP/1.0 400 Bad Request');
    echo ('No pages provided.');
    exit;
}

$pages_bll = new DotCorePageBLL();
$pages_bll->Fields(array($pages_bll->getFieldOrder()));
$page1 = $pages_bll->ByPageID($_REQUEST['page1'])->SelectFirstOrNull();
$page2 = $pages_bll->ByPageID($_REQUEST['page2'])->SelectFirstOrNull();

if($page1 == NULL || $page2 == NULL)
{
    header('HTTP/1.0 400 Bad Request');
    echo ('Invalid pages provided.');
    exit;
}

$pages_bll->BeginTransaction($page1);
$tmpOrder = $page1->getOrder();
$page1->setOrder($page2->getOrder());
$page2->setOrder($tmpOrder);
$pages_bll->Save($page1);
$pages_bll->Save($page2);
$pages_bll->CommitTransaction($page1);

?>

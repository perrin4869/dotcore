<?php

if(!isset($_REQUEST['page']))
{
	header('HTTP/1.0 400 Bad Request');
	echo ('No page provided.');
	exit;
}

if(!isset($_REQUEST['appears_in_nav']))
{
	header('HTTP/1.0 400 Bad Request');
	echo ('No value provided');
	exit;
}

$pages_bll = new DotCorePageBLL();
$page = $pages_bll
	->Fields(
			array(
				$pages_bll->getFieldPageID()
			)
		)
	->ByPageID($_REQUEST['page'])
	->SelectFirstOrNull();
if($page == NULL)
{
	header('HTTP/1.0 400 Bad Request');
	echo ('Invalid page provided.');
	exit;
}
$page->setPageAppearsInNav($_REQUEST['appears_in_nav'] == TRUE);
$pages_bll->Save($page);

?>

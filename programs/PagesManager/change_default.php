<?php

if(isset($_REQUEST['page']))
{
	$pages_bll = new DotCorePageBLL();
	$languages_link = $pages_bll->LinkLanguages();
	$languages_path = new DotCoreDALPath($languages_link->GetLinkName());
	$languages_bll = new DotCoreLanguageBLL();
	$page = $pages_bll
		->Fields(
			array(
				new DotCoreDALEntityPath($languages_bll->getFieldLanguageID(), $languages_path)
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
	$language = DotCorePageBLL::GetPageLanguage($page);
	$language->setLanguageDefaultPageID($page->getPageID());
	$languages_bll->Save($language);
}
else
{
	header('HTTP/1.0 400 Bad Request');
	echo ('No page provided.');
	exit;
}

?>

<?php

/**
 * Feature used to contact the admin of the site
 * @author perrin
 *
 */
class FeatureLinksList extends DotCoreFeature
{
	public function __construct(DotCoreFeatureRecord $record, $parameters = array())
	{
		parent::__construct($record, $parameters);

		if(
			$parameters['pages_length'] != NULL &&
			is_numeric($parameters['pages_length']) &&
			$parameters['pages_length'] > 0)
		{
			$this->pages_length = $parameters['pages_length'];
		}

		if(self::$instances == 0)
		{
			$header_content = '';
			$header_content .= '
				<link rel="stylesheet" type="text/css" href="' . $this->GetFeatureUrl() . '/feature_links_list.css" />
			';
			DotCorePageRenderer::GetCurrent()->RegisterHeaderContent($header_content);
		}

		self::$instances++;
	}

	private $pages_length = 12;
	private static $instances = 0;
	
	/**
	 * Shows the contact form to the user
	 *
	 */
	public function GetFeatureContent()
	{
		$result = '';
		$messages = $this->GetMessages();

		$links_bll = new DotCoreLinkBLL();
		$links_bll
			->Fields(
				array(
					$links_bll->getFieldTitle(),
					$links_bll->getFieldUrl(),
					$links_bll->getFieldDescription()
				)
			)
			->Ordered()
			->ByLanguageID(DotCorePageRenderer::GetCurrent()->GetLanguage()->getLanguageID());

		$num_pages = $links_bll->GetCountPages($this->pages_length);

		if(
			!isset($_REQUEST['page']) ||
			!is_numeric($_REQUEST['page']) ||
			$_REQUEST['page'] < 1 ||
			$_REQUEST['page'] > $num_pages)
		{
			$page = 1;
		}
		else
		{
			$page = $_REQUEST['page'];
		}

		$links = $links_bll->Page($this->pages_length, $page)->Select();
		$count_links = count($links);

		if($count_links > 0)
		{
			$result .= '<ul class="links_list">';
		}
		for($i = 0; $i < $count_links; $i++)
		{
			$li_class = $i == $offset ? ' class="first_link"' : '';
			$link = $links[$i];
			$result .= '
			<li'.$li_class.'>
				<h3><a href="'.$link->getLinkUrl().'" rel="external">'.$link->getLinkTitle().'</a></h3>
				'.$link->getLinkDescription().'
				<a class="url" href="'.$link->getLinkUrl().'" rel="external">'.$link->getLinkUrl().'</a>
			</li>';
		}
		if($count_links > 0)
		{
			$result .= '</ul>';
		}

		if($num_pages > 1) {
			$next_text = $messages['NextPage'];
			$prev_text = $messages['PrevPage'];

			if(($pos = strpos($_SERVER['REQUEST_URI'], '?')) !== FALSE)
			{
				$url = substr($_SERVER['REQUEST_URI'], 0, $pos);
			}
			else
			{
				$url = $_SERVER['REQUEST_URI'];
			}

			if($page > 1)
			{
				$prev_text = '<a href="'.$url.'?page='.($page - 1).'">'.$prev_text.'</a>';
			}
			if($page < $num_pages)
			{
				$next_text = '<a href="'.$url.'?page='.($page + 1).'">'.$next_text.'</a>';
			}

			$result .= '
			<div class="links_pager">
				'.$prev_text . ' | ' . $next_text . '
			</div>';
		}

		return $result;
	}
}

?>
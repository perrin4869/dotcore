<?php

/**
 * Feature used to contact the admin of the site
 * @author perrin
 *
 */
class FeatureLinksTable extends DotCoreFeature
{
	public function __construct(DotCoreFeatureRecord $record, $parameters = array())
	{
		parent::__construct($record, $parameters);

		if(
			$parameters['columns'] != NULL &&
			is_numeric($parameters['columns']) &&
			$parameters['columns'] > 0)
		{
			$this->columns = $parameters['pages_length'];
		}

		if(self::$instances == 0)
		{
			$header_content = '';
			$header_content .= '
				<link rel="stylesheet" type="text/css" href="' . $this->GetFeatureUrl() . '/feature_links_table.css" />
			';
			DotCorePageRenderer::GetCurrent()->RegisterHeaderContent($header_content);
		}

		self::$instances++;
	}

	private $columns = 3;
	private $resize_options = array('w'=>160, 'h'=>120, 'crop'=>TRUE);

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
					$links_bll->getFieldDescription(),
					$links_bll->getFieldLogo()
				)
			)
			->ByLanguageID(DotCorePageRenderer::GetCurrent()->GetLanguage()->getLanguageID());
		
		$links = $links_bll->Page($this->pages_length, $page)->Select();
		$count_links = count($links);

		if($count_links > 0)
		{
			$result .= '
			<div class="links-table-container">
				<table class="links-table">';
		}
		for($i = 0; $i < $count_links; $i++)
		{
			if($i % $this->columns == 0)
			{
				$result .= '<tr>';
			}
			
			/**
			 * @var DotCoreLinkRecord $link
			 */
			$link = $links[$i];
			$logo_server_path = $_SERVER['DOCUMENT_ROOT'] . $link->getLinkLogoPath();
			$img = '';
			if(is_file($logo_server_path))
			{
				$img = '<img alt="'.$link->getLinkTitle().'" src="'.ResizeMethods::resize($logo_server_path, $this->resize_options).'" />';
			}

			$result .= '
			<td>
				<a href="'.$link->getLinkUrl().'" rel="external">'.$img.$link->getLinkTitle().'</a>
			</td>';

			if((($i + 1) % $this->columns) == 0)
			{
				$result .= '</tr>';
			}
		}
		
		// Complete the last TRs
		while($i % $this->columns != 0)
		{
			$result .= '<td></td>';
			$i++;
			if($i % $this->columns == 0)
			{
				$result .= '</tr>';
			}
		}
		
		if($count_links > 0)
		{
			$result .= '
				</table>
			</div>';
		}

		return $result;
	}
}

?>
<?php

/**
 * DotCorePageBLL - Contains the business logic behind the news
 *
 * @author perrin
 */
class DotCorePageBLL extends DotCoreBLL {

	/*
	 *
	 * Properties:
	 *
	 */


	/*
	 *
	 * Fields accessors:
	 *
	 */

	/**
	 * Gets the auto incrementing ID of this DAL
	 * @return DotCoreAutoIncrementingKey
	 */
	public function getFieldPageID()
	{
		return $this->GetDAL()->GetField(DotCorePageDAL::PAGE_ID);
	}

	/**
	 * Gets the field that defines the name of the page
	 * @return DotCoreStringField
	 */
	public function getFieldName()
	{
		return $this->GetDAL()->GetField(DotCorePageDAL::PAGE_NAME);
	}

	/**
	 * Gets the field that defines the url of the page
	 * @return DotCoreStringField
	 */
	public function getFieldUrl()
	{
		return $this->GetDAL()->GetField(DotCorePageDAL::PAGE_URL);
	}

	/**
	 * Gets the field that defines the meta-content of the page
	 * @return DotCoreStringField
	 */
	public function getFieldHeaderContent()
	{
		return $this->GetDAL()->GetField(DotCorePageDAL::PAGE_HEADER_CONTENT);
	}

	/**
	 * Gets the field that defines the order of the page
	 * @return DotCoreIntField
	 */
	public function getFieldOrder()
	{
		return $this->GetDAL()->GetField(DotCorePageDAL::PAGE_ORDER);
	}

	/**
	 * Gets the field that defines the parent of the page
	 * @return DotCoreRecursiveIntField
	 */
	public function getFieldPageParentID()
	{
		return $this->GetDAL()->GetField(DotCorePageDAL::PAGE_PARENT_ID);
	}

	/**
	 * Gets the field that defines the whether the page appears in the nav or not
	 * @return DotCoreBooleanField
	 */
	public function getFieldAppearsInNav()
	{
		return $this->GetDAL()->GetField(DotCorePageDAL::PAGE_APPEARS_IN_NAV);
	}

	/**
	 * Gets the field that defines the language of the page
	 * @return DotCoreIntField
	 */
	public function getFieldPageLanguageID()
	{
		return $this->GetDAL()->GetField(DotCorePageDAL::PAGE_LANGUAGE);
	}

	/**
	 * Gets the field that defines the title of the page
	 * @return DotCoreStringField
	 */
	public function getFieldTitle()
	{
		return $this->GetDAL()->GetField(DotCorePageDAL::PAGE_TITLE);
	}

	/*
	 *
	 * Link Methods:
	 *
	 */

	/**
	 * Links the languages DAL
	 *
	 * @return DotCoreOneToManyRelationship
	 */
	public function LinkLanguages() {
		$link = DotCoreDAL::GetRelationship(DotCorePageDAL::LANGUAGE_LINK);
		$this->AddLink($link);
		return $this->GetDAL()->GetSelectionLinkTree()->root->nodes[DotCorePageDAL::LANGUAGE_LINK]->value;
	}

	/**
	 * Gets the language of this page
	 *
	 * @param DotCorePageRecord $page
	 * @return DotCoreLanguageRecord
	 */
	public static function GetPageLanguage(DotCorePageRecord $page) {
		return $page->GetLinkValue(DotCorePageDAL::LANGUAGE_LINK);
	}

	/**
	 * Links the contents DAL
	 *
	 * @return DotCoreOneToManyRelationship
	 */
	public function LinkContents() {
		$link = DotCoreDAL::GetRelationship(DotCorePageDAL::PAGE_CONTENTS_LINK);
		$this->GetDAL()->AddLink($link);
		return $link;
	}

	/**
	 * Gets the contents of this page
	 *
	 * @param DotCorePageRecord $page
	 * @return DotCoreContentRecord
	 */
	public static function GetPageContent(DotCorePageRecord $page) {
		return $page->GetLinkValue(DotCorePageDAL::PAGE_CONTENTS_LINK);
	}

	/*
	 *
	 * Abstract Methods Implementation:
	 *
	 */

	/**
	 *
	 * @return DotCorePageDAL
	 */
	public static function GetDAL() {
		return self::GetDALHelper('DotCorePageDAL');
	}

	/*
	 *
	 * Overrides:
	 *
	 */

	public function GetPageContents()
	{
		// TODO: Get contents
	}

	/*
	 *
	 * Restraint Methods
	 *
	 */

	/**
	 * @param int $id
	 * 
	 * @return DotCorePageBLL
	 */
	public function ByPageID($id)
	{
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldPageID(), $id));

		$this->Restraints($restraints);
		return $this;
	}

	/**
	 *
	 * @param string $name
	 * @return DotCorePageBLL
	 */
	public function ByPageName($name)
	{
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldName(), $name));

		$this->Restraints($restraints);
		return $this;
	}

	/**
	 *
	 * @param string $url
	 * @return DotCorePageBLL
	 */
	public function ByPageUrl($url)
	{
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldUrl(), $url));

		$this->Restraints($restraints);
		return $this;
	}

	/**
	 *
	 * @param string $url
	 * @param int $lang_id
	 * @return DotCorePageBLL
	 */
	public function ByPageUrlAndLanguage($url, $lang_id)
	{
		$restraints = new DotCoreDALRestraint();
		$restraints
			->AddRestraint(
				new DotCoreFieldRestraint($this->getFieldUrl(), $url))
			->AddRestraint(
				new DotCoreFieldRestraint($this->getFieldPageLanguageID(), $lang_id));

		$this->Restraints($restraints);
		return $this;
	}

	/**
	 *
	 * @param int $parent_id
	 * @return DotCorePageBLL
	 */
	public function ByParentPageID($parent_id)
	{
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldPageParentID(), $parent_id));

		$this->Restraints($restraints);
		return $this;
	}

	/**
	 *
	 * @return DotCorePageBLL
	 */
	public function ByRootPages()
	{
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldPageParentID(), NULL));

		$this->Restraints($restraints);
		return $this;
	}

	/**
	 *
	 * @param int $language_id
	 * @return DotCorePageBLL
	 */
	public function ByRootPagesByLanguage($language_id)
	{
		$restraints = new DotCoreDALRestraint();
		$restraints
			->AddRestraint(
				new DotCoreFieldRestraint($this->getFieldPageParentID(), NULL))
			->AddRestraint(
				new DotCoreFieldRestraint($this->getFieldPageLanguageID(), $language_id));

		$this->Restraints($restraints);
		return $this;
	}

	/**
	 *
	 * @return DotCorePageBLL
	 */
	public function ByRootPagesThatAppearInNav()
	{
		$restraints = new DotCoreDALRestraint();
		$restraints
			->AddRestraint(
				new DotCoreFieldRestraint($this->getFieldPageParentID(), NULL))
			->AddRestraint(
				new DotCoreFieldRestraint($this->getFieldAppearsInNav(), TRUE));

		$this->Restraints($restraints);
		return $this;
	}

	/**
	 *
	 * @param int $language_id
	 * @return DotCorePageBLL
	 */
	public function ByRootPagesThatAppearInNavByLanguage($language_id)
	{
		$restraints = new DotCoreDALRestraint();
		$restraints
			->AddRestraint(
				new DotCoreFieldRestraint($this->getFieldPageParentID(), NULL))
			->AddRestraint(
				new DotCoreFieldRestraint($this->getFieldAppearsInNav(), TRUE))
			->AddRestraint(
				new DotCoreFieldRestraint($this->getFieldPageLanguageID(), $language_id));

		$this->Restraints($restraints);
		return $this;
	}

	/**
	 *
	 * @param int $page_id
	 * @return DotCorePageBLL
	 */
	public function ByParentPageThatAppearInNav($page_id)
	{
		$restraints = new DotCoreDALRestraint();
		$restraints
			->AddRestraint(
				new DotCoreFieldRestraint($this->getFieldPageParentID(), $page_id))
			->AddRestraint(
				new DotCoreFieldRestraint($this->getFieldAppearsInNav(), TRUE));

		$this->Restraints($restraints);
		return $this;
	}

	/**
	 *
	 * @param int $lang_id
	 * @return DotCorePageBLL
	 */
	public function ByLanguageID($lang_id) {

		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint($this->getFieldPageLanguageID(), $lang_id));

		$this->Restraints($restraints);
		return $this;
		
	}

	/*
	 *
	 * Order Methods
	 *
	 */

	 /**
	  *
	  * @return DotCorePageBLL
	  */
	 public function Ordered($direction = DotCoreFieldSelectionOrder::DIRECTION_ASC)
	 {
		 $pages_order = new DotCoreDALSelectionOrder();
		 $pages_order
			->AddOrderUnit(
				new DotCoreFieldSelectionOrder($this->getFieldOrder(), $direction)
			);
		 return $this->Order($pages_order);
	 }

	 /**
	  *
	  * @return DotCorePageBLL
	  */
	 public function OrderedByLanguageAndOrder(
		 $lang_direction = DotCoreFieldSelectionOrder::DIRECTION_ASC,
		 $order_direction = DotCoreFieldSelectionOrder::DIRECTION_ASC)
	 {
		 $pages_order = new DotCoreDALSelectionOrder();
		 $pages_order
			->AddOrderUnit(
				new DotCoreFieldSelectionOrder($this->getFieldPageLanguageID(), $lang_direction)
			)
			->AddOrderUnit(
				new DotCoreFieldSelectionOrder($this->getFieldOrder(), $order_direction)
			);
		 return $this->Order($pages_order);
	 }

	 /*
	  *
	  * Select Methods
	  *
	  */

	public function SelectHierarchy($lang)
	{
		$pages_hirerarchy = array();
		$pages = $this->ByRootPages()->Select();
		$count_pages = count($pages);
		for($i = 0; $i < $count_pages; $i++)
		{
			$current_node = new DotCoreTreeNode();
			$current_node->value = $pages[$i];
			array_push($pages_hirerarchy, $current_node);
			$this->LoadHierarchy($pages[$i]->getPageID(), $current_node->nodes);
		}
		return $pages_hirerarchy;
	}

	public function SelectHierarchyByLanguages()
	{
		$parents_by_languages = array();
		$pages = $this->ByRootPages()->Select();
		$count_pages = count($pages);
		
		for($i = 0; $i < $count_pages; $i++)
		{
			$page = $pages[$i];
			$curr_lang_id = $page->getPageLanguageID();
			if(!key_exists($curr_lang_id, $parents_by_languages))
			{
				// We're working on a new language now, so store the pages in a different array
				$parents_by_languages[$curr_lang_id] = array();
			}
			$node = new DotCoreTreeNode();
			$node->value = $page;
			array_push($parents_by_languages[$curr_lang_id], $node);
			$this->LoadHierarchy($page->getPageID(), $node->nodes);
		}

		return $parents_by_languages;
	}

	public function LoadHierarchy($parent_id, &$array)
	{
		$pages = $this->ByParentPageID($parent_id)->Select();
		$count_pages = count($pages);
		for($i = 0; $i < $count_pages; $i++)
		{
			$current_node = new DotCoreTreeNode();
			$current_node->value = $pages[$i];
			array_push($array, $current_node);
			$this->LoadHierarchy($pages[$i]->getPageID(), $current_node->nodes);
		}
	}

	/*
	 *
	 * Misc Methods
	 *
	 */

	/**
	 *
	 * @param DotCorePageRecord $page
	 * @return DotCorePageRecord
	 */
	public function GetParentPage(DotCorePageRecord $page)
	{
		return $this->ByPageID($page->getPageParentID())->SelectFirstOrNull();
	}

	public static function GetPagePath(DotCorePageRecord $page)
	{
		if($page->HasLinkValueLoaded(DotCorePageDAL::LANGUAGE_LINK)) {
			$language_code = self::GetPageLanguage($page)->getLanguageCode();
		}
		else {
			$languages_dictionary = DotCoreLanguageBLL::GetLanguagesIDDictionary();
			$language_code = $languages_dictionary[$page->getPageLanguageID()]->getLanguageCode();
		}
		return '/' . $language_code . '/' . $page->getUrl() . '.' . DotCoreConfig::$DEFAULT_EXTENSION;
	}

	/**
	 * TRUE if the DotCorePageRecord page is the default for its language, FALSE otherwise
	 * @return bool
	 */
	public static function IsLanguageDefault(DotCorePageRecord $page)
	{
		return $page->GetPageLanguage()->getLanguageDefaultPageID() == $page->getPageID();
	}

}
?>

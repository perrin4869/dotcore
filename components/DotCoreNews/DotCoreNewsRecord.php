<?php

/**
 * DotCoreNewsRecord represents one record of news from a DAL
 *
 * @author perrin
 */
class DotCoreNewsRecord extends DotCoreDataRecord {

	/**
	 * Constructor for News record
	 *
	 * @param DotCoreDAL $dal
	 */
	public function  __construct(DotCoreDAL $dal) {
		parent::__construct($dal);
	}

	/*
	 *
	 * Accessors:
	 *
	 */
	
	/*
	 * Getters:
	 */

	public function getNewsID() {
		return $this->GetField(DotCoreNewsDAL::NEWS_ID);
	}

	public function getNewsTitle() {
		return $this->GetField(DotCoreNewsDAL::NEWS_TITLE);
	}

	public function getNewsShortContent() {
		return $this->GetField(DotCoreNewsDAL::NEWS_SHORT_CONTENT);
	}

	public function getNewsLanguageID() {
		return $this->GetField(DotCoreNewsDAL::NEWS_LANGUAGE_ID);
	}

	public function getNewsContent() {
		return $this->GetField(DotCoreNewsDAL::NEWS_CONTENT);
	}

	public function getNewsDate() {
		return $this->GetField(DotCoreNewsDAL::NEWS_DATE);
	}

	/*
	* Setters:
	*/


	private function setID($val) {
		$this->SetField(DotCoreNewsDAL::NEWS_ID, $val);
	}

	public function setNewsTitle($title) {
		$this->SetField(DotCoreNewsDAL::NEWS_TITLE, $title);
	}

	public function setNewsShortContent($content) {
		$this->SetField(DotCoreNewsDAL::NEWS_SHORT_CONTENT, $content);
	}

	public function setNewsLanguageID($langID) {
		$this->SetField(DotCoreNewsDAL::NEWS_LANGUAGE_ID, $langID);
	}

	public function setNewsContent($content) {
		$this->SetField(DotCoreNewsDAL::NEWS_CONTENT, $content);
	}

	public function setNewsDate($date){
		$this->SetField(DotCoreNewsDAL::NEWS_DATE, $date);
	}

}
?>

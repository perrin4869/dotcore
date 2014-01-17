<?php

/**
 * DotCoreEventRecord represents one record of events from a DAL
 *
 * @author perrin
 */
class DotCoreEventRecord extends DotCoreDataRecord {

	/**
	 * Constructor for Events record
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

	public function getEventID() {
		return $this->GetField(DotCoreEventDAL::EVENTS_ID);
	}

	public function getEventTitle() {
		return $this->GetField(DotCoreEventDAL::EVENTS_TITLE);
	}

	public function getEventDescription() {
		return $this->GetField(DotCoreEventDAL::EVENTS_DESCRIPTION);
	}

	public function getEventDetails() {
		return $this->GetField(DotCoreEventDAL::EVENTS_DETAILS);
	}

	public function getEventDate() {
		return $this->GetField(DotCoreEventDAL::EVENTS_DATE);
	}

	public function getEventLanguageID() {
		return $this->GetField(DotCoreEventDAL::EVENTS_LANGUAGE_ID);
	}

	/*
	 * Setters:
	 */


	private function setID($val) {
		$this->SetField(DotCoreEventDAL::EVENTS_ID, $val);
	}

	public function setEventTitle($title) {
		$this->SetField(DotCoreEventDAL::EVENTS_TITLE, $title);
	}

	public function setEventDescription($desc) {
		$this->SetField(DotCoreEventDAL::EVENTS_DESCRIPTION, $desc);
	}

	public function setEventDetails($details) {
		$this->SetField(DotCoreEventDAL::EVENTS_DETAILS, $details);
	}

	public function setEventDate($date) {
		$this->SetField(DotCoreEventDAL::EVENTS_DATE, $date);
	}

	public function setEventLanguageID($language_id) {
		$this->SetField(DotCoreEventDAL::EVENTS_LANGUAGE_ID, $language_id);
	}

}
?>

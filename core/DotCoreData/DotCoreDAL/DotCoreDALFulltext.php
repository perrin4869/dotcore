<?php

/**
 * DotCoreDALFulltext - Defines a list of fields from a DAL that make up a fulltext index
 *
 * @author perrin
 */
class DotCoreDALFulltext extends DotCoreObject {

	/**
	 * Constructor for a DotCoreDALFulltext, gets the name of the fulltext as a parameter
	 * @param string $name
	 */
	public function  __construct($name, DotCoreDAL $dal) {
		$this->name = $name;
		$this->dal = $dal;
	}

	/**
	 * Holds the DAL to which this Fulltext is associated
	 * @var DotCoreDAL
	 */
	private $dal = NULL;
	/**
	 * Holds the name of this fulltext
	 * @var string
	 */
	private $name = '';
	/**
	 * Holds the fields of this fulltext
	 * @var array of DotCoreDALField
	 */
	private $fields = array();

	/**
	 * Adds a field into the list of fields in this Fulltext
	 * @param DotCoreDALField $dal_field
	 * @return DotCoreDALFulltext
	 */
	public function AddField(DotCoreDALField $dal_field)
	{
		$this->fields[$dal_field->GetFieldName()] = $dal_field;
		return $this;
	}

	/**
	 * Removes the field $dal_field from this fulltext
	 * @param DotCoreDALField $dal_field
	 */
	public function RemoveField(DotCoreDALField $dal_field)
	{
		$this->fields[$dal_field->GetFieldName()] = NULL;
	}

	/**
	 * Gets a list of the fields in this fulltext
	 * @return array of DotCoreDALField
	 */
	public function GetFields()
	{
		return array_values($this->fields);
	}

	/*
	 *
	 * IDotCoreDALSelectableEntity Implementation:
	 *
	 */

	/**
	 * Gets the name of this fulltext
	 * @return string
	 */
	public function GetName()
	{
		return $this->name;
	}

	/**
	 *
	 * @return DotCoreDAL
	 */
	public function GetDAL()
	{
		return $this->dal;
	}

}
?>

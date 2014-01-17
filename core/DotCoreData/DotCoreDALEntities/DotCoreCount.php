<?php

/**
 * DotCoreCount - Describes an entity used to count records inside a DAL
 *
 * @author perrin
 */
class DotCoreCount extends DotCoreDALEntityBase {

	/**
	 *
	 * @param IDotCoreSelectableField | DotCoreDAL | DotCoreDALRelationship $entity - The the entity this count acts upon
	 * @param mixed $component
	 */
	public function  __construct($entity) {
		if($entity instanceof  IDotCoreDALSelectableEntity) {
			$this->field = $entity;
		}
		elseif($entity instanceof DotCoreDAL) {
			$this->dal = $entity;
		}
		elseif($entity instanceof DotCoreDALRelationship) {
			$this->link = $entity;
		}
		else {
			throw new InvalidArgumentException('Parameter must be an instance of IDotCoreSelectableField | DotCoreDAL | DotCoreDALRelationship');
		}
	}

	/**
	 *
	 * @var IDotCoreDALSelectableEntity 
	 */
	private $field = NULL;

	/**
	 *
	 * @var DotCoreDAL
	 */
	private $dal = NULL;

	/**
	 *
	 * @var DotCoreDALRelationship
	 */
	private $link = NULL;

	/*
	 *
	 * Accessors:
	 *
	 */

	/**
	 *
	 * @return IDotCoreDALSelectableField
	 */
	public function getCountField() {
		return $this->field;
	}

	/**
	 *
	 * @param IDotCoreDALSelectableField $field
	 */
	public function setCountField(IDotCoreDALSelectableField $field = NULL) {
		$this->field = $field;
	}

	/**
	 *
	 * @return DotCoreDAL
	 */
	public function getCountDAL() {
		return $this->dal;
	}

	/**
	 *
	 * @param DotCoreDAL $dal
	 */
	public function setCountDAL(DotCoreDAL $dal = NULL) {
		$this->dal = $dal;
	}

	/**
	 *
	 * @return DotCoreDALRelationship
	 */
	public function getCountLink() {
		return $this->link;
	}

	/**
	 *
	 * @param DotCoreDALRelationship $link
	 */
	public function setCountLink(DotCoreDALRelationship $link = NULL) {
		$this->link = $link;
	}

	/*
	 * IDotCoreDALSelectableEntity implementation:
	 */

	/**
	 * Gets the DAL to which the entity is associated
	 *
	 * @return DotCoreDAL
	 */
	public function GetDAL()
	{
		// If this entity is not linked, just get the regular container name
		if($this->field != NULL) {
			return $this->field->GetDAL();
		}
		elseif($this->dal != NULL) {
			return $this->dal;
		}
		else {
			return $this->link->GetLinkedDAL();
		}
	}

	/**
	 * Gets the name of this selectable entity for use inside a query (for example, inside WHERE statements, etc)
	 *
	 * @return string
	 */
	public function GetName()
	{
		return $this->GetSQLNameWithTablePrefix();
	}

	public function GetSQLContainerName()
	{
		// If this entity is not linked, just get the regular container name
		if($this->field != NULL) {
			return $this->field->GetSQLContainerName();
		}
		elseif($this->dal != NULL) {
			return $this->dal->GetName();
		}
		else {
			return $this->link->GetLinkAliasName();
		}
	}

	public function GetSQLNameWithTablePrefix()
	{
		// If it's not a field - i.e., it's a link or a DAL, it doesn't matter, just select everything
		if($this->field == NULL) {
			$count = '*';
		}
		else {
			$count = $this->field->GetSQLNameWithTablePrefix();
		}
		return 'COUNT('.$count.')';
	}

}
?>

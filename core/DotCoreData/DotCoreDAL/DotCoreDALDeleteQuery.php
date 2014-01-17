<?php
// +------------------------------------------------------------------------+
// | DotCoreDALDeleteQuery.php											  |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2009. All rights reserved.			   |
// | Version	   0.01													 |
// | Last modified 12/03/2010											   |
// | Email		 juliangrinblat@gmail.com								 |
// | Web		   http://www.dotcore.co.il								 |
// +------------------------------------------------------------------------+

/**
 * Class DotCoreDALSelectQuery
 *
 * @version   0.01
 * @author	Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
class DotCoreDALDeleteQuery extends DotCoreObject {

	/**
	 * Constructor for DotCoreDALDeleteQuery
	 * @param DotCoreDAL $executing_dal
	 */
	public function  __construct(DotCoreDAL $executing_dal, DotCoreDALRestraint $restraints = NULL) {
		$this->executing_dal = $executing_dal;
		$this->restraints = $restraints;
	}

	/*
	 *
	 * Fields Definitions
	 *
	 */

	/**
	 * Stores the root DAL of the query
	 * @var DotCoreDAL
	 */
	private $executing_dal = NULL;
	/**
	 *
	 * @var DotCoreDALRestraint
	 */
	private $restraints;

	/*
	 *
	 * Main Methods:
	 *
	 */

	/**
	 *
	 * @return DotCoreDAL
	 */
	public function GetDAL() {
		return $this->executing_dal;
	}

	/**
	 * Sets the restriction for the next selection
	 * @param DotCoreDALRestraint $restraints
	 * @return DotCoreDAL
	 */
	public function SetRestraints(DotCoreDALRestraint $restraints)
	{
		$this->restraints = $restraints;
		return $this;
	}

	/**
	 * Gets the restraints given to the DAL for the selection
	 * @return DotCoreDALRestraint
	 */
	public function GetRestraint()
	{
		return $this->restraints;
	}

	public function Execute() {
		$query = 'DELETE FROM ' . $this->GetDAL()->GetName();
		if($this->restraints != NULL) {
			$query .= ' WHERE ' . $this->restraints->GetRestraintSQL();
		}
		$mysql = new DotCoreMySql();
		$mysql->PerformDelete($query);
		unset ($mysql);
	}

}
?>
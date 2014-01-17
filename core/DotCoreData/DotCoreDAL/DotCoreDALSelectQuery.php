<?php
// +------------------------------------------------------------------------+
// | DotCoreDALSelectQuery.php											  |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2009. All rights reserved.			   |
// | Version	   0.01													 |
// | Last modified 12/03/2010											   |
// | Email		 juliangrinblat@gmail.com								 |
// | Web		   http://www.dotcore.co.il								 |
// +------------------------------------------------------------------------+

/**
 * Class DotCoreDALSelectQuery
 * Internal class used by DotCoreDAL classes to store and execute queries
 *
 * @version   0.01
 * @author	Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
class DotCoreDALSelectQuery extends DotCoreObject {

	/**
	 * Constructor for DotCoreDALSelectQuery
	 * @param DotCoreDAL $executing_dal
	 */
	public function  __construct(DotCoreDAL $executing_dal) {
		$this->executing_dal = $executing_dal;
		$this->link_tree = new DotCoreDALLinkTree($executing_dal);
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
	 * Holds the restraints for the next selection
	 * @var DotCoreDALRestraint
	 */
	private $sel_restraints = NULL;
	/**
	 * Holds the fields to select from the next selection
	 * @var array
	 */
	private $sel_fields = NULL;
	/**
	 * Holds the order in which the fields should be selected
	 * @var DotCoreDALSelectionOrder
	 */
	private $sel_order = NULL;
	/**
	 * Holds the offset of the next select statment
	 * @var int
	 */
	private $sel_offset = NULL;
	/**
	 * Holds the limit of the records to retrieve
	 * @var int
	 */
	private $sel_limit = NULL;
	/**
	 * Holds the fields that the selection is grouped by
	 * @var array
	 */
	private $sel_group_by = NULL;

	
	/**
	 * Holds the array of Links held by this DAL to other DALs
	 * @var DotCoreDALLinkTree
	 */
	private $link_tree = NULL;

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
	 * Performs a select on the underlying data, conforming with the restrictions.
	 * If given an array of columns to select, it'll select them only, otherwise it'll select everything
	 *
	 * @return array
	 */
	public function Select()
	{
		$mysql = $this->PerformSelection();
		$result = $this->GetRecords($mysql);

		unset($mysql);
		return $result;
	}

	/**
	 * Performs a select on the underlying data, conforming with the restrictions.
	 * and returning the very first match, or NULL if no match is found
	 *
	 * @return DotCoreDataRecord
	 */
	public function SelectFirstOrNull()
	{
		$mysql = $this->PerformSelection();
		$result = $this->GetRecords($mysql);

		unset($mysql);
	return (key_exists(0,$result)) ? $result[0] : NULL;
	}

	/**
	 * Performs a selection, and returns the results in an array
	 * If only one field was requested, the results will be a one dimensional array
	 * Otherwise it'll be a two dimensional array, the second dimension being a dictionary of the fields
	 * @return array
	 */
	public function SelectArray() {
		$mysql = $this->PerformSelection();

		$result = array();
		if(count($this->GetSelectionFields()) == 1)
		{
			while($row = $mysql->FetchRow())
			{
				array_push($result, current($row));
			}
		}
		else
		{
			while($row = $mysql->FetchRow())
			{
				array_push($result, $row);
			}
		}
		
		unset($mysql);
		return $result;
	}

	/**
	 * Performs a query, and returns the first result
	 * @return mixed
	 */
	public function SelectScalar() {
		$query = $this->GenerateQuery();
		// echo($query.'<br /><br /><br />');
		$mysql = new DotCoreMySql();
		$result = $mysql->PerformScalar($query);

		// Clean
		unset($mysql);
		return $result;
	}

	/**
	 * Like select, only the array key is the column key specified
	 *
	 * @param IDotCoreDALSelectableField $field
	 */
	public function SelectDictionary(IDotCoreDALSelectableEntity $key_field)
	{
		$mysql = $this->PerformSelection();
		$result = $this->GetRecords($mysql, $key_field->GetSQLName());

		// Clean Selection
		unset($mysql);
		return $result;
	}

	/*
	 *
	 * Setup methods:
	 *
	 */

	/**
	 * Adds a link to this Query
	 * @param DotCoreDALLink $link
	 * @param DotCoreDALPath
	 * @return DotCoreDAL
	 */
	public function AddLink(DotCoreDALLink $link, DotCoreDALPath $path = NULL)
	{
		$this->link_tree->AddLink($link, $path);
		return $this;
	}

	/**
	 * Removes the link with name $link_name from the links of this Query
	 * @param string $link_name
	 * @param DotCoreDALPath
	 * @return DotCoreDAL
	 */
	public function RemoveLink($link_name, DotCoreDALPath $path = NULL)
	{
		$this->link_tree->RemoveLink($link_name, $path);
		return $this;
	}

	/**
	 * Return the node which holds the link at the end of the DotCoreDALPath $path
	 * @param DotCoreDALPath $path
	 * @return DotCoreTreeNode
	 */
	public function GetLinkNode(DotCoreDALPath $path) {
		return $this->link_tree->GetLinkNode($path);
	}

	/**
	 * Gets all the links of this DAL
	 * @return DotCoreDALLinkTree
	 */
	public function GetLinks() {
		return $this->link_tree;
	}

	public function GetLink($link_name, DotCoreDALPath $path = NULL) {
		return $this->link_tree->GetLink($link_name, $path);
	}

	/**
	 * Sets the fields for the next selection.
	 * IF SELECTING FROM MULTIPLE DALs ALL THE PRIMARY KEYS OF THE DALS MUST BE SELECTED, OTHERWISE THE RESULTS ARE UNEXPECTED
	 *
	 * @param array $fields
	 * @return DotCoreDAL
	 */
	public function Fields($fields)
	{
		$this->sel_fields = $fields;
		return $this;
	}

	/**
	 * Sets the restriction for the next selection
	 * @param DotCoreDALRestraint $restraints
	 * @return DotCoreDAL
	 */
	public function Restraints(DotCoreDALRestraint $restraints)
	{
		$this->sel_restraints = $restraints;
		return $this;
	}

	/**
	 * Sets the order for the next selection
	 * @param array $order
	 * @return DotCoreDAL
	 */
	public function Order(DotCoreDALSelectionOrder $order)
	{
		$this->sel_order = $order;
		return $this;
	}

	/**
	 * Sets the offset of the next selection
	 * @param int $offset
	 * @return DotCoreDAL
	 */
	public function Offset($offset)
	{
		$this->sel_offset = $offset;
		return $this;
	}

	/**
	 * Sets the limit of the records to retrieve in the next selection
	 * @param int $limit
	 * @return DotCoreDAL
	 */
	public function Limit($limit)
	{
		$this->sel_limit = $limit;
		return $this;
	}

	/**
	 * Sets the fields by which the selection is grouped
	 * @param array $fields
	 * @return DotCoreDAL
	 */
	public function GroupBy($fields)
	{
		$this->sel_group_by = $fields;
		return $this;
	}

	/**
	 * Gets the restraints given to the DAL for the selection
	 * @return DotCoreDALRestraint
	 */
	public function GetRestraint()
	{
		return $this->sel_restraints;
	}

	/**
	 * Gets the fields given to the DAL for the selection
	 * @return array
	 */
	public function GetFields()
	{
		return $this->sel_fields;
	}

	/**
	 * Gets the order given to the DAL for the selection
	 * @return array
	 */
	public function GetOrder()
	{
		return $this->sel_order;
	}

	/**
	 * Gets the offset of the next selection
	 * @return int
	 */
	public function GetOffset()
	{
		return $this->sel_offset;
	}

	/**
	 * Gets the limit of the next selection
	 * @return int
	 */
	public function GetLimit()
	{
		return $this->sel_limit;
	}

	/**
	 *
	 * @return array
	 */
	public function GetGroupBy() {
		return $this->sel_group_by;
	}

	/**
	 *
	 * @return array
	 */
	public function GetLinkTree() {
		return $this->link_tree;
	}

	/**
	 * Cleans all selection data in the DAL, to start a fresh selection
	 */
	public function FinalizeSelection()
	{
		$this->sel_order =  NULL;
		$this->sel_fields = NULL;
		$this->sel_restraints = NULL;
		$this->sel_offset = NULL;
		$this->sel_limit = NULL;
		$this->sel_group_by = NULL;

		unset($this->link_tree);
		$this->link_tree = new DotCoreDALLinkTree($this->GetDAL());

	}

	protected function GenerateQuery() {
		// Get the parameters
		if(count($this->sel_fields) == 0)
		{
			throw new Exception('No fields were selected!');
		}

		$restraints = $this->sel_restraints;
		$order = $this->sel_order;
		$offset = $this->sel_offset;
		$limit = $this->sel_limit;
		$fields = $this->sel_fields;
		$group_by = $this->sel_group_by;

		$query = '';
		$join = '';
		$select = $this->GetSelectStatementFromFields($fields);
		$join = $this->GetLinkTree()->GetStatement();

		// $this->PrepareQueryResultsParsing();

		$query .= 'SELECT ' . $select . ' ' . $this->GetFromStatement() . ' ' . $join;

		if($restraints != NULL)
		{
			$query .= ' WHERE ' . $restraints->GetRestraintSQL();
		}

		if($order != NULL)
		{
			$query .= ' ORDER BY ' . $order->GetStatement();
		}

		if($group_by != NULL)
		{
			$query .= ' GROUP BY ';
			$count_group_by = count($group_by);
			$arr_group = array();
			for($i = 0; $i < $count_group_by; $i++) {
				$curr_group_field = $group_by[$i];
				array_push($arr_group, $curr_group_field->GetSQLName());
			}
			$query .= join(',', $arr_group);
		}

		if($offset !== NULL)
		{
			$query .= ' LIMIT ' . $offset . ', ';
			if($limit !== NULL)
			{
				$query .= $limit;
			}
			else
			{
				// Incredibly big number so no limit is felt
				$query .= '9999999999999999999';
			}
		}
		elseif($limit !== NULL)
		{
			$query .= ' LIMIT ' . $limit;
		}
		// echo $query . '<br /><br /><br />';
		return $query;
	}

	/**
	 * Returns a select statement from an array of fields
	 *
	 * @param array of DotCoreDALEntityPath $entity_paths
	 */
	protected function GetSelectStatementFromFields($entity_paths)
	{
		$select = '';
		foreach($entity_paths as $path)
		{
			if(strlen($select) > 0)
			{
				$select .= ',';
			}
			$select .= $path->GetSelectStatementSQL();
		}
		return $select;
	}

	public function GetFromStatement() {
		return 'FROM ' . $this->GetDAL()->GetName();
	}
	

	/**
	 * Peforms a selection, and returns the resulting MySql result
	 *
	 * @return DotCoreMySql
	 */
	protected function PerformSelection()
	{
		$query = $this->GenerateQuery();
		$mysql = new DotCoreMySql();
		$mysql->PerformQuery($query);
		return $mysql;
	}

	/**
	 * Gets the records off an MySql result object, and returns the hierarchy of records. If a key field is given,
	 * the result will be an array with the key being the $key_field given
	 * @param DotCoreMySql $sql_result
	 * @param string $key_field
	 * @return array
	 */
	protected function GetRecords(DotCoreMySql $sql_result, $key_field = NULL)
	{
		// Holds the result of the overall operation
		$result = array();
		// Held in order to check wether the record for a given DAL changed since the last iteration
		// Needed in order to successfully create a correct hierarchy
		$previous_records = array();
		$count_fields = count($this->sel_fields);
		$this_dal_name = $this->GetDAL()->GetName();

		while($row = $sql_result->FetchRow())
		{
			// Array of the records container by the current row
			// Key - String representing the name of the DAL
			// Value - DotCoreDataRecord
			$records = array();
			$records_changed = array();
			for($i = 0; $i < $count_fields; $i++)
			{
				$field = $this->sel_fields[$i];
				$field_path = $field->GetSQLContainerName();
				$dal = $field->GetDAL();

				// Still didn't get the record of this dal
				if(!isset($records[$field_path]))
				{
					$prev_rec = isset($previous_records[$field_path]) ? $previous_records[$field_path] : NULL;
					if($this->RowEqualsRecord($dal, $row, $prev_rec, $field_path))
					{
						$records[$field_path] = $prev_rec;
						$records_changed[$field_path] = FALSE;
					}
					else
					{
						if(!$this->IsRowNull($dal, $row, $field_path))
						{
							$records[$field_path] = $dal->GetRecord();
						}
						else
						{
							$records[$field_path] = NULL;
						}
						$previous_records[$field_path] = $records[$field_path];
						$records_changed[$field_path] = TRUE;
					}
				}

				if($records_changed[$field_path] && $records[$field_path] != NULL)
				{
					$field_sql_name = $field->GetSQLName();
					$field->SetValueFromDAL($records[$field_path],$row[$field_sql_name]);
				}
			}

			$this->SetLinkedRecords($records, $records_changed, $records_changed[$this_dal_name]);

			// Push into the result only if changed
			if($records_changed[$this_dal_name])
			{
				$record = $records[$this_dal_name];
				$record->SetSaved();
				if($key_field !== NULL)
				{
					if(!key_exists($key_field, $row))
					{
						throw new InvalidKeyFieldException();
					}
					$key = $row[$key_field];
					$result[$key] = $record;
				}
				else
				{
					array_push($result, $record);
				}
			}
		}

		return $result;
	}

	/**
	 * Checks whether the DotCoreDataRecord $record equals to the record in the Database row $row
	 * @param array $row
	 * @param DotCoreDataRecord $record
	 * @param $path Holds the path to the values in the row
	 * @return boolean
	 */
	protected function RowEqualsRecord(DotCoreDAL $dal, $row, DotCoreDataRecord $record = NULL, $path = NULL)
	{
		if($record == NULL)
		{
			return $this->IsRowNull($dal, $row, $path);
		}

		$primary_fields = $dal->GetPrimaryFields();
		if(count($primary_fields) > 0)
		{
			$prefix = $path != NULL ? $path : $this->GetDAL()->GetName();
			$prefix .= '_';
			foreach($primary_fields as $field)
			{
				if(!$field->Equals($row[$prefix.$field->GetName()], $record->GetField($field->GetFieldName())))
				{
					return FALSE;
				}
			}

			return TRUE;
		}
		else
		{
			return FALSE; // TODO: Real checking
		}
	}

	/**
	 * Checks wether the record held by the row result is null
	 * @param array $row
	 * @param string $path
	 * @return boolean
	 */
	protected function IsRowNull(DotCoreDAL $dal, $row, $path = NULL)
	{
		$primary_fields = $dal->GetPrimaryFields();
		if(count($primary_fields) > 0)
		{
			$prefix = $path != NULL ? $path : $this->GetDAL()->GetName();
			$prefix .= '_';
			foreach($primary_fields as $field)
			{
				if($row[$prefix.$field->GetFieldName()] == NULL)
				{
					return TRUE;
				}
			}

			return FALSE;
		}
		else
		{
			return FALSE; // TODO: Real checking
		}
	}

	protected function SetLinkedRecords(&$records, &$changed_records, $changed = FALSE, $current_dal = NULL, $curr_node = NULL, DotCoreDALPath $curr_path = NULL)
	{
		if($current_dal == NULL) {
			$current_links = $this->GetLinkTree()->GetFirstGenerationLinks()->nodes;
			$current_dal = $this->GetDAL();
			$current_container_name = $current_dal->GetName();
			$curr_path = new DotCoreDALPath();
		}
		else {
			$current_dal = $curr_node->value->GetOppositeDAL($current_dal);
			$current_links = $curr_node->nodes;
			$current_container_name = $curr_path->GetPathSQL();
		}
		$current_dal_record = $records[$current_container_name];
		$count_links = count($current_links);

		// There are 2 times in which we insert a new link record -
		// Either the current_node's value, the record being checked now changed, and we need to populate it all again
		// Or the linked node being checked now changed, and then we need to insert the new one into current_node's value
		foreach($current_links as $node)
		{
			$current_link = $node->value;
			if($current_link->StoreLinkResults())
			{
				$next_path = clone($curr_path);
				$next_path->append($current_link->GetLinkName());
				$next_container_path = $next_path->GetPathSQL();
				$current_link_record = $records[$next_container_path];
				$curr_changed = $changed || $changed_records[$next_container_path];
				if($curr_changed)
				{
					// The current node changed, so insert for all the childs
					$current_dal->SetLinkValue(
						$current_link->GetRelationship(),
						$current_dal_record,
						$current_link_record);
					if($current_link_record != NULL)
					{
						$current_link_record->SetSaved();
					}
				}
				$this->SetLinkedRecords($records, $changed_records, $curr_changed, $current_dal, $node, $next_path);
			}
		}
	}

}

?>

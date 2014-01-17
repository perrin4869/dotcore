<?php

/**
 * Defines the base functionality for BLLs
 *
 * @author perrin
 */
abstract class DotCoreBLL extends DotCoreObject {

	/**
	 * Constructor for the base of Business Logic Layers
	 * @param DotCoreDAL $dal The data access layer used by this BLL, if none is provided the default one is used
	 */
	public function  __construct(DotCoreDAL $dal = NULL)
	{
		if($dal != NULL)
		{
			$this->dal = $dal;
		}
		else
		{
			$this->dal = $this->GetDAL();
		}
	}

	public function  __destruct() {
		$this->FinalizeSelection();
	}

	/*
	 *
	 * Properties:
	 *
	 */


	protected static $dal_holder = array();

	/**
	 *
	 * @var DotCoreDAL
	 */
	private $dal = NULL;
	/**
	 *
	 * @var Holds the fields for the next query 
	 */
	private $fields = NULL;
	/**
	 *
	 * @var mixed
	 */
	private $append_restraint = NULL;
	/**
	 * Holds the restraints as they build up
	 * @var DotCoreDALRestraint
	 */
	private $restraint = NULL;

	/**
	 * Gets the DAL used by this class to access data
	 *
	 * @return DotCoreDAL
	 */
	public static abstract function GetDAL();

	public static function GetDALHelper($dal_class) {

		// If this function was called by an instance of the DAL, and that DAL had already the DAL loaded, return it
		if(!empty($this) && $this->dal != NULL)
		{
			return $this->dal;
		}

		// Else create a new DAL, as requested
		if(!array_key_exists($dal_class, self::$dal_holder))
		{
			self::$dal_holder[$dal_class] = call_user_func(array($dal_class, 'GetInstance'));
		}
		return self::$dal_holder[$dal_class];
	}

	/**
	 * Links all the DALs necessary to select the required fields
	 *
	 * @return DotCoreBLL
	 */
	public function SolveDependencies() {
		// First, resolve all dependencies and convert the paths to linked fields (that are what DALs understand)
		$dal = $this->GetDAL();
		$count_fields = count($this->fields);
		for($i = 0; $i < $count_fields; $i++)
		{
			/**
			 * @var DotCoreDALEntityPath $field
			 */
			$current_entity = $this->fields[$i];
			if(is_string($current_entity))
			{
				// Parse the string, and make it an entity path
				$path_array = explode('.', $current_entity);
				if(count($path_array) == 1)
				{
					$this->fields[$i] = $dal->GetEntity($current_entity);
				}
				else
				{
					$entity_name = array_pop($path_array);
					$path_length = count($path_array);
					$tmp_path = new DotCoreDALPath();
					$curr_tree = $dal->GetSelectionLinkTree()->GetFirstGenerationLinks();
					$curr_dal = $dal;
					for($j = 0; $j < $path_length; $j++) {
						$curr_link_name = $path_array[$j];
						$relationship = DotCoreDAL::GetRelationship($curr_link_name);
						$curr_dal = $relationship->GetOppositeDAL($curr_dal);
						if(!key_exists($curr_link_name, $curr_tree->nodes)) {
							$this->AddLink($relationship, $tmp_path);
						}
						$tmp_path = clone($tmp_path);
						$tmp_path->append($curr_link_name);
						$curr_tree = $curr_tree->nodes[$curr_link_name];
					}
					$entity = $curr_dal->GetEntity($entity_name);
					if($entity == NULL)
					{
						throw new Exception('Invalid entity in selection fields.');
					}
					$this->fields[$i] = new DotCoreDALEntityPath($entity, $tmp_path);
				}
			}
			elseif($current_entity instanceof DotCoreDALEntityPath) {
				$path = $current_entity->getPath();
				// Solve all the path dependencies
				$path_length = $path->count();
				$curr_tree = $dal->GetSelectionLinkTree()->GetFirstGenerationLinks();
				$tmp_path = new DotCoreDALPath();
				for($j = 0; $j < $path_length; $j++) {
					$curr_link_name = $path[$j];
					$relationship = DotCoreDAL::GetRelationship($curr_link_name);
					if(!key_exists($curr_link_name, $curr_tree->nodes)) {
						$this->AddLink($relationship, $tmp_path);
					}
					$tmp_path = clone($tmp_path);
					$tmp_path->append($curr_link_name);
					$curr_tree = $curr_tree->nodes[$curr_link_name];
				}
			}
		}
		return $this;
	}

	/**
	 * Adds the primary fields of all the linked DALs
	 *
	 * @return DotCoreBLL
	 */
	
	public function SolvePrimaryFields()
	{
		$dal = $this->GetDAL();
		$this->AddMissingFields($dal->GetPrimaryFields(), new DotCoreDALPath());
		$this->SolvePrimaryFieldsHelper($dal, $dal->GetSelectionLinkTree()->GetFirstGenerationLinks(), new DotCoreDALPath());
		return $this;
	}

	private function SolvePrimaryFieldsHelper(DotCoreDAL $current_dal, $current_node, DotCoreDALPath $current_path)
	{
		foreach($current_node->nodes as $node) {
			$link = $node->value;
			$param_path = clone($current_path);
			$param_path->append($link->GetLinkName());
			$linked_dal = $link->GetOppositeDAL($current_dal);
			$this->AddMissingFields($linked_dal->GetPrimaryFields(), $param_path);
			$this->SolvePrimaryFieldsHelper($linked_dal, $node, $param_path);
		}
	}

	public function AddMissingFields($fields, $path) {
		$count_fields = count($fields);
		$new_fields = array(); // Holds the entities that need to be appended
		$count_curr_fields = count($this->fields);
		for($i = 0; $i < $count_fields; $i++)
		{
			$entity = new DotCoreDALFieldPath($fields[$i],$path);
			$found = 0;
			for($j = 0; $j < $count_curr_fields; $j++)
			{
				$field = $this->fields[$j];
				if($entity->GetSQLName() == $field->GetSQLName())
				{
					$found = 1;
					break;
				}
			}
			if($found == 0)
			{
				array_push($new_fields, $entity);
			}
		}

		$this->fields = array_merge($this->fields, $new_fields);
		return $this;
	}

	/**
	 * Sets the fields that will be selected by the next selection
	 * @param array $fields
	 * @return DotCoreBLL
	 */
	public function Fields($fields)
	{
		if(!is_array($fields))
		{
			$this->fields = array($fields); // Convert into array
		}
		else
		{
			$this->fields = array_values($fields); // Make it a sequential array
		}
		
		return $this;
	}

	/**
	 * Sets the order of the next selection
	 * @param DotCoreSelectionOrder $order
	 * @return DotCoreBLL
	 */
	public function Order(DotCoreDALSelectionOrder $order)
	{
		$dal = $this->GetDAL();
		$dal->Order($order);
		return $this;
	}

	/**
	 * Sets the fields by which to group the query results
	 * @param array $fields
	 * @return DotCoreBLL
	 */
	public function GroupBy($fields)
	{
		$dal = $this->GetDAL();
		$dal->GroupBy($fields);
		return $this;
	}

	/**
	 * Sets the limit of the next selection
	 * @param int $limit
	 * @return DotCoreBLL
	 */
	public function Limit($limit)
	{
		$dal = $this->GetDAL();
		$dal->Limit($limit);
		return $this;
	}

	/**
	 * Sets the offset of the next selection
	 * @param int $offset
	 * @return DotCoreBLL
	 */
	public function Offset($offset)
	{
		$dal = $this->GetDAL();
		$dal->Offset($offset);
		return $this;
	}

	/**
	 * Adds a link to the tree of links.
	 * If $link is a string, it adds a link with the relationship by that name,
	 * If $link is a DotCoreDALRelationship it ads
	 * @param string | DotCoreDALRelationship | DotCoreDALLink $link
	 * @param DotCoreDALPath $path
	 */
	public function AddLink($link, DotCoreDALPath $path = NULL) {
		if(is_string($link)) {
			$link = DotCoreDAL::GetRelationship($link);
		}
		if($link instanceof DotCoreDALRelationship) {
			$link = new DotCoreDALLink($link);
		}
		$this->GetDAL()->AddLink($link, $path);
		return $this;
	}

	/**
	 *
	 * @param DotCoreDALRestraint $restraints
	 * @return DotCoreBLL
	 */
	public function Restraints($restraints)
	{
		if($this->append_restraint != NULL && $this->restraint != NULL) {
			$this->restraint
				->ChangeRestraintAddingMethod($this->append_restraint)
				->OpenRestrainingUnit()
				->AddRestraint($restraints)
				->CloseRestrainingUnit();
			$this->append_restraint = NULL;
		}
		else
		{
			$this->restraint = new DotCoreDALRestraint();
			$this->restraint
				->OpenRestrainingUnit()
				->AddRestraint($restraints)
				->CloseRestrainingUnit();
				
			$dal = $this->GetDAL();
			$dal->Restraints($this->restraint);
		}

		return $this;
	}

	/**
	 * Resets the selection parameters on this BLL
	 * @return DotCoreBLL
	 */
	public function FinalizeSelection()
	{
		$dal = $this->GetDAL();
		$dal->FinalizeSelection();
		$this->restraint = NULL;
		return $this;
	}

	/**
	 * Updates the record given to it
	 * @param DotCoreDataRecord $record
	 * @return DotCoreBLL
	 */
	public function Update(DotCoreDataRecord $record)
	{
		$dal = $this->GetDAL();
		$dal->Update($record);
		return $this;
	}

	/**
	 * Insert the record or records passed to it
	 * @param DotCoreDataRecord $record
	 * @return DotCoreBLL
	 */
	public function Insert($record)
	{
		$this->GetDAL()->Insert($record);
		return $this;
	}

	/**
	 * Deletes the record passed to it, or deletes by the restraints on this BLL
	 * @param mixed $record_or_query
	 * @return DotCoreBLL
	 */
	public function Delete($record_or_query = NULL)
	{
		if($record_or_query == NULL) {
			$record_or_query = new DotCoreDALDeleteQuery($this->GetDAL());
			$record_or_query->SetRestraints($this->restraint);
		}
		$this->GetDAL()->Delete($record_or_query);
		return $this;
	}

	/**
	 * Saves the changes done to $record
	 * @param DotCoreDataRecord $record
	 * @return DotCoreBLL
	 */
	public function Save(DotCoreDataRecord $record)
	{
		$this->GetDAL()->Save($record);
		return $this;
	}

	/**
	 * Acts as a proxy for DotCoreDAL select
	 * @return array
	 */
	public function Select()
	{
		return $this
			->SolveDependencies()
			->SolvePrimaryFields()
			->GetDAL()
			->Fields($this->fields)
			->Select();
	}

	/**
	 * Acts as a proxy for DotCoreDAL select first or null
	 * @return DotCoreDataRecord
	 */
	public function SelectFirstOrNull()
	{
		return $this
			->SolveDependencies()
			->SolvePrimaryFields()
			->GetDAL()
			->Fields($this->fields)
			->SelectFirstOrNull();
	}

	/**
	 *
	 * @param IDotCoreDALSelectableEntity $field
	 * @return array
	 */
	public function SelectDictionary(IDotCoreDALSelectableEntity $field)
	{
		return $this
			->SolveDependencies()
			->SolvePrimaryFields()
			->GetDAL()
			->Fields($this->fields)
			->SelectDictionary($field);
	}

	public function SelectArray() {
		return $this->GetDAL()->Fields($this->fields)->SelectArray();
	}

	public function SelectScalar() {
		return $this->GetDAL()->Fields($this->fields)->SelectScalar();
	}

	/**
	 * Gets the count of records in the underlying Database, under the current restraints on the DAL
	 * @return int
	 */
	public function GetCount()
	{
		return $this->GetDAL()->GetCount();
	}

	/**
	 * Gets a record of the DAL behind this BLL
	 * @return DotCoreDataRecord
	 */
	public function GetRecord()
	{
		return $this->GetDAL()->GetRecord();
	}

	/**
	 * Gets a new record of the DAL behind this BLL
	 * @return DotCoreDataRecord
	 */
	public function GetNewRecord()
	{
		return $this->GetDAL()->GetNewRecord();
	}

	/*
	 *
	 * Pager Functions
	 *
	 */

	public function GetCountPages($page_length)
	{
		$count = $this->GetDAL()->GetCount();
		return ceil($count / $page_length);
	}

	public function Page($page_length, $page_num)
	{
		$page_num--;
		$this->Limit($page_length);
		$this->Offset($page_num*$page_length);
		return $this;
	}

	// Transaction Methods

	/**
	 * Begins a transaction with the Database on $record
	 * @param mixed $records
	 */
	public function BeginTransaction($records) {
		$this->GetDAL()->BeginTransaction($records);
	}

	/**
	 * Rollbacks all the transactions done on $record since beginning the transaction
	 * @param mixed $records
	 */
	public function Rollback($records) {
		$this->GetDAL()->Rollback($records);
	}

	/**
	 * Saves all the changes since the beginning of the transaction
	 * @param DotCoreDataRecord $records
	 */
	public function CommitTransaction($records) {
		$this->GetDAL()->CommitTransaction($records);
	}

	/**
	 * Used to check whether the last transaction that was begun was committed
	 * @return boolean
	 */
	public function TransactionCommitted() {
		return $this->GetDAL()->TransactionCommitted();
	}

	/**
	 * Used to check whether the last transaction that was begun was rollbacked
	 * @return boolean
	 */
	public function TransactionWasRollbacked() {
		return $this->GetDAL()->TransactionWasRollbacked();
	}

	/*
	 *
	 * Shortcut Methods:
	 *
	 */

	/**
	 *
	 * @return DotCoreBLL
	 */
	public function OrderByRandom() {
		$order = new DotCoreDALSelectionOrder();
		$order->AddOrderUnit(new DotCoreSQLSelectionOrder('RAND()'));
		return $this->Order($order);
	}

	/**
	 *
	 * @return DotCoreBLL
	 */
	public function AndBy() {
		$this->append_restraint = DotCoreDALRestraint::RESTRAINT_ADDING_METHOD_AND;
		return $this;
	}

	/**
	 *
	 * @return DotCoreBLL
	 */
	public function OrBy() {
		$this->append_restraint = DotCoreDALRestraint::RESTRAINT_ADDING_METHOD_OR;
		return $this;
	}

	
	public static function GetDictionary($records, IDotCoreDALSelectableEntity $entity) {
		$result = array();
		foreach($records as $record) {
			$result[$entity->GetValue($record)] = $record;
		}
		return $result;
	}

}
?>

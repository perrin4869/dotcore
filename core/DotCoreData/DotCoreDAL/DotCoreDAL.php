<?php

/**
 * DotCoreDAL - Provides the basic interface for all the data access made in the system
 * Every data access object should inherit from this class
 *
 * @author perrin
 */
abstract class DotCoreDAL extends DotCoreObject {

	/**
	 * Constructor for DotCoreDAL
	 *
	 * @param string $name The name designated to this DAL
	 */
	protected function  __construct($name) {
		$this->name = $name;

		$this->entities[self::FIELDS] = array();
		$this->entities[self::ENTITIES] = array();

		$this->fields = &$this->entities[self::FIELDS];
		$this->select_query = new DotCoreDALSelectQuery($this);

		foreach($this->events as $event)
		{
			$this->events[$event] = new DotCoreEvent();
		}
	}

	const FIELDS = 0;
	const ENTITIES = 1;

	/**
	 * Function used for getting the singleton instance of a given DAL
	 *
	 * @return DotCoreDAL
	 */
	public abstract static function GetInstance();

	/**
	 * Helper function to be used by child dals for automatic storage and retrieval of singleton instances
	 *
	 * @param string $dal_class
	 */
	protected static function GetDALInstance($dal_class) {
		if(!key_exists($dal_class, self::$singleton_dals))
		{
			self::$singleton_dals[$dal_class] = new $dal_class;
		}
		return self::$singleton_dals[$dal_class];
		// return new $dal_class; // For now
	}

	/*
	 *
	 * Data:
	 *
	 */

	private static $singleton_dals = array();
	/**
	 * Holds a link name to link dictionary
	 * @var array
	 */
	private static $links_definitions = array();

	/**
	 * Holds an identifier for this DAL
	 * @var string
	 */
	private $name = NULL;

	/**
	 * Stores a reference to the fields array
	 * @var array
	 */
	private $fields;

	/**
	 * Stores the custom entities used by this DAL (Generic ones like COUNT)
	 * @var array
	 */
	private $entities;

	/**
	 * Holds the array of the names of the primary fields of this DAL
	 * @var array of string
	 */
	private $primary_fields = array();

	/**
	 * Select Query object used by this DAL
	 * @var DotCoreDALSelectQuery
	 */
	private $select_query = NULL;

	/**
	 * Used to store whether the last transaction was commited or rollbacked, false by default
	 * @var boolean
	 */
	private $commited_transaction = FALSE;

	// Stuff stored in DAL
	
	/**
	 * Holds the fulltexts defined by this DAL
	 * @var array of DotCoreDALFulltext
	 */
	private $fulltexts = array();

	/**
	 * Holds the unique keys of this DAL
	 * @var array
	 */
	private $unique_keys = array();

	/**
	 * Adds a relationship definition
	 * @param DotCoreDALRelationship $relationship
	 */
	public static function AddRelationship(DotCoreDALRelationship $relationship) {
		// Store links in two ways - by link name, and by relation, that way they can be fastly indexed by need
		self::$links_definitions[$relationship->GetRelationshipName()] = $relationship;
		$relationship->MakePermanent();
	}

	/**
	 * Removes a relationship definition
	 * @param DotCoreDALRelationship $relationship
	 */
	public static function RemoveRelationship(DotCoreDALRelationship $relationship) {
		unset(self::$links_definitions[$relationship->GetRelationshipName()]);
	}

	/**
	 *
	 * @param string $relationship_name
	 * @return DotCoreDALRelationship
	 */
	public static function GetRelationship($relationship_name) {
		return self::$links_definitions[$relationship_name];
	}

	/**
	 * Gets the name of this DAL, representing the name of the SQL table accessed
	 * @return string
	 */
	public function GetName() {
		return $this->name;
	}
	
	public function AddField(DotCoreDALField $dal_field)
	{
		$this->fields[$dal_field->GetFieldName()] = $dal_field;
	}

	public function RemoveField($dal_field_name)
	{
		unset($this->fields[$dal_field_name]);
	}

	/**
	 * Gets the field defined by $dal_field
	 * @param string $dal_field_name
	 * @return IDotCoreDALSelectableField
	 */
	public function GetField($dal_field_name)
	{
		if(!key_exists($dal_field_name, $this->fields))
		{
			throw new InvalidFieldException();
		}
		return $this->fields[$dal_field_name];
	}

	/**
	 * Validates the value $val passed to it according to the field $field passed, and taking $record into account
	 * Manipulates $val as needed by the field definition
	 *
	 * @param string $field
	 * @param DotCoreDataRecord $record
	 * @param mixed $val
	 * 
	 * @return TRUE if $val should be set, false otherwise
	 */
	public function ValidateField($field, DotCoreDataRecord $record, &$val)
	{
		$field_definition = $this->GetField($field);
		if($field_definition == NULL)
		{
			throw new InvalidFieldException();
		}

		// If valid, nothing happens. The value passed will get manipulated as needed by the field definition
		// If invalid, an exception will be thrown that the client will have to take care of
		return $field_definition->Validate($record, $val);
	}

	public function ValidateRecord(DotCoreDataRecord $record) {
		// If the record is was not labeled as valid, check it again
		$result = TRUE;

		// Fire OnValidating Event
		$onvalidating_event_handlers = $this->events[self::EVENT_VALIDATING]->GetHandlers();
		$count_onvalidating_event_handlers = count($onvalidating_event_handlers);
		for($i = 0; $i < $count_onvalidating_event_handlers; $i++) {
			$result = $result && $onvalidating_event_handlers[$i]->Fire(array($record));
		}

		if(!$record->IsValid())
		{
			// Make sure the unique keys restraints are respected
			$edited_fields = $record->GetEditedFields();
			$count_edited_fields = count($edited_fields);
			$violated_keys = array();
			$query = new DotCoreDALSelectQuery($this);
			foreach($this->unique_keys as $name=>$key)
			{
				$count_fields = count($key); // $key is an array of the key fields
				$edited = FALSE;
				for($i = 0; $i < $count_fields; $i++)
				{
					// We need to check if any of the fields in this key was edited. If it was, we must make sure it's still unique
					for($j = 0; $j < $count_edited_fields; $j++)
					{
						if($key[$i]->GetFieldName() == $edited_fields[$j])
						{
							$edited = TRUE; // This key's fields were edited, so check immediately if they're still unique
							break;
						}
					}

					if($edited)
					{
						break; // Time to check the uniqueness
					}
				}

				if($edited)
				{
					$restraint = new DotCoreDALRestraint();
					for($i = 0; $i < $count_fields; $i++)
					{
						$field = $key[$i];
						$field_name = $field->GetFieldName();
						$restraint->AddRestraint(
							new DotCoreFieldRestraint(
								$field,
								$record->GetField($field_name)));
					}

					$count_unique = $query
						->Fields(array(new DotCoreCount($this)))
						->Restraints($restraint)
						->SelectScalar();
					$query->FinalizeSelection();
					if($count_unique > 0)
					{
						$violated_keys[$name] = $key;
					}
				}
			}
			
			$this->FinalizeSelection();
			if(count($violated_keys) > 0)
			{
				throw new UniqueKeyException($violated_keys);
			}

			// Fire OnValidating Event
			$onvalidated_event_handlers = $this->events[self::EVENT_VALIDATING]->GetHandlers();
			$count_onvalidated_event_handlers = count($onvalidated_event_handlers);
			for($i = 0; $i < $count_onvalidated_event_handlers; $i++) {
				$result = $result && $onvalidated_event_handlers[$i]->Fire(array($record));
			}

			$record->SetValid();
		}
		
		return $result;
	}

	/**
	 * If valid, it'll set the value of field $field in DotCoreDataRecord $record to $value
	 * Else it'll throw an Exception
	 *
	 * @param string $field_name
	 * @param DotCoreDataRecord $record
	 * @param mixed $value
	 *
	 * @return TRUE if the value  was set, false otherwise
	 */
	public function SetFieldValue($field_name, DotCoreDataRecord $record, &$value)
	{
		// Validates and cleans the value so it's fit for insertion into the record
		$field = $this->GetField($field_name);
		return $field->SetValue($record, $value);
	}

	/**
	 * Sets the value $val into the Record without validating - after cleaning it from taking it from the database
	 *
	 * @param string $entity_name
	 * @param DotCoreDataRecord $record
	 * @param mixed $value
	 */
	public function SetValueFromDAL($entity_name, DotCoreDataRecord $record, $value)
	{
		$entity = $this->GetEntity($entity_name);
		$entity->SetValueFromDAL($record, $value);
	}

	public function SetLinkValue(DotCoreDALRelationship $link, DotCoreDataRecord $record, DotCoreDataRecord $linked_record = NULL)
	{
		$link->SetLinkValue($record, $linked_record);
	}

	/**
	 *
	 * @param string $field_name
	 * @param DotCoreDataRecord $record
	 */
	public function LoadField($field_name, DotCoreDataRecord $record)
	{
		$query = new DotCoreDALSelectQuery($this);
		$restraint = $this->GetRecordRestraint($record);
		$result = $query
			->Fields(
				array(
					$this->GetField($field_name)
				)
			)
			->Restraints($restraint)
			->SelectScalar();

		unset($restraint);
		$this->SetValueFromDAL($field_name, $record, $result);
	}

	public function SetPrimaryField($dal_field_name)
	{
		array_push($this->primary_fields, $dal_field_name);
	}

	public function RemovePrimaryField($dal_field_name)
	{
		$primary_fields = $this->primary_fields;
		if(($key = array_search($dal_field_name, $primary_fields)) !== FALSE)
		{
			$primary_fields[$key] = NULL;
			$this->primary_fields = array_values($primary_fields);
		}
	}

	/**
	 * Gets an indexed array with the primary DotCoreDALFields of this DAL
	 * @return array of DotCoreDALField
	 */
	public function GetPrimaryFields()
	{
		$result = array();
		foreach($this->primary_fields as $field)
		{
			array_push($result, $this->GetField($field));
		}
		return $result;
	}

	/**
	 * Adds a new unique key, by the name $key_name, which makes the fields in $array_fields unique
	 * Can be chained
	 * @param string $key_name
	 * @param array $array_fields
	 * @return DotCoreDAL
	 */
	public function AddUniqueKey($key_name, $array_fields) {
		// If the fields are not in an array (i.e., the key is just one field, and it was passed as one field)
		// simply create an array, with the field as its sole member
		if(!is_array($array_fields)) {
			$tmp_array_fields = $array_fields;
			$array_fields = array();
			array_push($array_fields, $tmp_array_fields);
		}
		$this->unique_keys[$key_name] = $array_fields;
		return $this;
	}

	/**
	 * Removes the unique key by the name $key_name from the unique keys defined in this DAL
	 * @param string $key_name
	 * @return DotCoreDAL
	 */
	public function RemoveUniqueKey($key_name) {
		$this->unique_keys[$key_name] = NULL;
		return $this;
	}

	/**
	 * Gets the unique key by the name $key_name
	 * @param string $key_name
	 * @return array
	 */
	public function GetUniqueKey($key_name) {
		return $this->unique_keys[$key_name];
	}

	/**
	 * Gets the unique keys defined in this DAL in a dictionary,
	 * where the keys are the name of the keys and the values is the array of the keys
	 * @return array
	 */
	public function GetUniqueKeys() {
		return $this->unique_keys;
	}

	/**
	 * Adds a fulltext key to this DAL
	 * @param DotCoreDALFulltext $fulltext
	 * @return DotCoreDAL
	 */
	public function AddFulltext(DotCoreDALFulltext $fulltext)
	{
		$this->fulltexts[$fulltext->GetName()] = $fulltext;
		return $this;
	}

	/**
	 * Removes a fulltext key from this DAL
	 * @param DotCoreDALFulltext $fulltext
	 * @return DotCoreDAL
	 */
	public function RemoveFulltext(DotCoreDALFulltext $fulltext)
	{
		$this->fulltexts[$fulltext->GetName()] = NULL;
		return $this;
	}

	public function GetFulltext($fulltext_name)
	{
		if(key_exists($fulltext_name, $this->fulltexts))
		{
			return $this->fulltexts[$fulltext_name];
		}
		else
		{
			throw new InvalidFulltextException();
		}
	}

	/**
	 *
	 * @param string $entity_name
	 * @return IDotCoreDALSelectableEntity
	 */
	public function GetEntity($entity_name)
	{
		$count_entities_types = count($this->entities);
		
		for($i = 0; $i < $count_entities_types; $i++)
		{
			$entities_type = $this->entities[$i];
			if(isset($entities_type[$entity_name]))
			{
				return $entities_type[$entity_name];
			}
		}
		return NULL;
	}

	public function AddEntity(IDotCoreDALSelectableEntity $entity)
	{
		$this->entities[self::ENTITIES][$entity->GetName()] = $entity;
	}

	public function RemoveEntity($entity_name)
	{
		unset($this->entities[self::ENTITIES][$entity_name]);
	}

	/**
	 * Adds a link to this DAL
	 * @param DotCoreDALLink $link
	 * @param DotCoreDALPath
	 * @return DotCoreDAL
	 */
	public function AddLink(DotCoreDALLink $link, DotCoreDALPath $path = NULL)
	{
		$this->select_query->AddLink($link, $path);
		return $this;
	}

	/**
	 * Removes the DAL by the name $linke_name from the links of this DAL
	 * @param string $link_name
	 * @param DotCoreDALPath
	 * @return DotCoreDAL
	 */
	public function RemoveLink($link_name, DotCoreDALPath $path = NULL)
	{
		$this->links[$linked_dal_id] = NULL;
		return $this;
	}

	/**
	 * Sets the restriction for the next selection
	 * @param DotCoreDALRestraint $restraints
	 * @return DotCoreDAL
	 */
	public function Restraints(DotCoreDALRestraint $restraints)
	{
		$this->select_query->Restraints($restraints);
		return $this;
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
		$this->select_query->Fields($fields);
		return $this;
	}

	/**
	 * Sets the order for the next selection
	 * @param array $order
	 * @return DotCoreDAL
	 */
	public function Order($order)
	{
		$this->select_query->Order($order);
		return $this;
	}

	/**
	 * Sets the offset of the next selection
	 * @param int $offset
	 * @return DotCoreDAL
	 */
	public function Offset($offset)
	{
		$this->select_query->Offset($offset);
		return $this;
	}

	/**
	 * Sets the limit of the records to retrieve in the next selection
	 * @param int $limit
	 * @return DotCoreDAL
	 */
	public function Limit($limit)
	{
		$this->select_query->Limit($limit);
		return $this;
	}

	/**
	 * Sets the fields by which the selection is grouped
	 * @param array $fields
	 * @return DotCoreDAL
	 */
	public function GroupBy($fields) {
		$this->select_query->GroupBy($fields);
		return $this;
	}

	/**
	 * Gets the restraints given to the DAL for the selection
	 * @return DotCoreDALRestraint
	 */
	public function GetSelectionRestraint()
	{
		return $this->select_query->GetRestraint();
	}

	/**
	 * Gets the fields given to the DAL for the selection
	 * @return array
	 */
	public function GetSelectionFields()
	{
		return $this->select_query->GetFields();
	}

	/**
	 * Gets the order given to the DAL for the selection
	 * @return array
	 */
	public function GetSelectionOrder()
	{
		return $this->select_query->GetOrder();
	}

	/**
	 * Gets the offset of the next selection
	 * @return int
	 */
	public function GetSelectionOffset()
	{
		return $this->select_query->GetOffset();
	}

	/**
	 * Gets the limit of the next selection
	 * @return int
	 */
	public function GetSelectionLimit()
	{
		return $this->select_query->GetLimit();
	}

	/**
	 * Gets the grouping of the next selection
	 * @return array
	 */
	public function GetSelectionGroupBy()
	{
		return $this->select_query->GetGroupBy();
	}

	/**
	 *
	 * @return array
	 */
	public function GetSelectionLinkTree() {
		return $this->select_query->GetLinkTree();
	}

	/**
	 * Cleans all selection data in the DAL, to start a fresh selection
	 */
	public function FinalizeSelection()
	{
		return $this->select_query->FinalizeSelection();
		
	}

	/**
	 * Performs a select on the underlying data, conforming with the restrictions.
	 * If given an array of columns to select, it'll select them only, otherwise it'll select everything
	 *
	 * @return array
	 */
	public function Select()
	{
		return $this->select_query->Select();
	}

	/**
	 * Performs a select on the underlying data, conforming with the restrictions.
	 * and returning the very first match, or NULL if no match is found
	 *
	 * @return DotCoreDataRecord
	 */
	public function SelectFirstOrNull()
	{
		return $this->select_query->SelectFirstOrNull();
	}

	/**
	 * Performs a selection, and returns the results in an array
	 * If only one field was requested, the results will be a one dimensional array
	 * Otherwise it'll be a two dimensional array, the second dimension being a dictionary of the fields
	 * @return array
	 */
	public function SelectArray() {
		return $this->select_query->SelectArray();
	}

	/**
	 * Performs a query, and returns the first result
	 * @return mixed
	 */
	public function SelectScalar() {
		return $this->select_query->SelectScalar();
	}

	/**
	 * Like select, only the array key is the column key specified
	 *
	 * @param IDotCoreDALSelectableField $field
	 */
	public function SelectDictionary(IDotCoreDALSelectableEntity $key_field)
	{
		return $this->select_query->SelectDictionary($key_field);
	}

	/**
	 * Used to check whether 2 records $left and $right are equal, the given records should be of the same type of course
	 * @param DotCoreDataRecord $left
	 * @param DotCoreDataRecord $right
	 * @return boolean
	 */
	public function EqualRecords(DotCoreDataRecord $left = NULL, DotCoreDataRecord $right = NULL)
	{
		// If we're talking about NULL records, we can get the answer easily
		if($left === NULL || $right === NULL)
		{
			return $right === $left;
		}

		// Find out which fields to compare of both records
		// If there are primary fields, it suffices to compare only them
		$fields = $this->GetPrimaryFields();
		if(!count($fields) > 0)
		{
			// Arbitrarily choose one of the records and compare those fields in both records
			// We're assuming the same records were loaded for both records
			// TODO: Better checking
			$fields = $left->GetLoadedFields();
		}

		foreach($fields as $field)
		{
			$field_name = $field->GetFieldName();
			if(!$field->Equals($left->GetField($field_name), $right->GetField($field_name)))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Gets the count of all the records, restricted by the given DotCoreDALRestraint
	 */
	public function GetCount()
	{
		$query = new DotCoreDALSelectQuery($this);
		return $query
			->Fields(array(new DotCoreCount($this)))
			->SelectScalar();
	}

	/**
	 * Returns an array with the field definitions of this DAL
	 *
	 * @return array
	 */
	public function GetFieldsDefinitions()
	{
		return $this->fields;
	}

	/**
	 * Returns a record from this DAL
	 *
	 * @return DotCoreDataRecord
	 */
	public abstract function GetRecord();

	/**
	 * Returns a record, with some initizations made for database insertion
	 *
	 * @return DotCoreDataRecord
	 */
	public function GetNewRecord()
	{
		// By default the implementation will be the same as getting a normal new record
		return $this->GetRecord();
	}

	/**
	 * Updates the record passed to this object
	 * @param DotCoreDataRecord $record
	 */
	public function Update(DotCoreDataRecord $record)
	{
		// TODO: Reimplement with DotCoreDALUpdateQuery
		if($record->IsEmpty())
		{
			throw new Exception('Can\'t update an empty.');
		}

		if(!$this->ValidateRecord($record))
		{
			throw new Exception('The record is not valid for updating.');
		}

		$this->events[self::EVENT_UPDATING]->FireHandlers($record);

		$edited_fields = $record->GetEditedFields();
		if(count($edited_fields))
		{
			$query = 'UPDATE ' . $this->GetName() . ' SET ';
			$i = 0;
			foreach($edited_fields as $field_name)
			{
				$field = $this->GetField($field_name);
				if($i != 0)
				{
					$query .= ', ';
				}
				$i++;
				$query .=
					$field->GetFieldName() . '=' .
					$field->GetValuePreparedForQuery($record->GetField($field->GetFieldName()));
			}

			$restraints = $this->GetRecordRestraint($record);
			$query .= ' WHERE ' . $restraints->GetRestraintSQL();
			$mysql = new DotCoreMySql();
			$mysql->PerformUpdate($query);
		}

		// This event expects to get the original values, so put it before setting as saved
		$this->events[self::EVENT_UPDATED]->FireHandlers($record);

		$record->SetSaved();
	}

	/**
	 * Inserts the record or records passed into the underlying data base
	 * @param DotCoreDataRecord
	 */
	public function Insert(DotCoreDataRecord $record)
	{
		// We're bulding the query each time again because the loaded fields may change after firing the event
		$this->events[self::EVENT_INSERTING]->FireHandlers($record);
		$this->ValidateRecord($record);

		// We don't do this check in the validation method because this validation must be committed after
		// firing the inserting events. This is not added to the validation function because this is relevant only when inserting
		if($record->IsEmpty() && !$this->IsReadyForInsertion($record))
		{
			throw new NotReadyForInsertionException();
		}

		$query = 'INSERT INTO ' . $this->GetName() . ' (';
		$fields = array_values($record->GetLoadedFields());
		$count_fields = count($fields);
		$cols_array = array();
		$values = array();
		for($i = 0; $i < $count_fields; $i++)
		{
			$field = $fields[$i];
			array_push($cols_array,$field->GetFieldName());
			array_push($values, $field->GetValuePreparedForQuery($record->GetField($field->GetFieldName())));
		}
		$query .= join(',', $cols_array) . ') VALUES ('.join(',', $values).')';
		$mysql = new DotCoreMySql();
		$inserted_id = $mysql->PerformInsert($query); // Return the inserted ID

		// If we were given an array, we're expecting multiple inserts and so the last inserted ID will not be valid
		if($inserted_id) // If an autoincrementing key was generated
		{
			$count_primary_fields = count($this->primary_fields);
			if($count_primary_fields == 1)
			{
				// Assume the key field is the autoincrementing key
				$this->SetValueFromDAL($this->primary_fields[0], $record, $inserted_id);
			}
		}

		$record->SetSaved();
		$this->events[self::EVENT_INSERTED]->FireHandlers($record);
	}

	public function IsReadyForInsertion(DotCoreDataRecord $record)
	{
		foreach($this->fields as $field)
		{
			if(
				!$field->IsNullable() &&
				$field->IsEmpty(
					$record->GetField($field->GetFieldName())
				)
			)
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Saves the changes made to the record passed in the underlying database.
	 * If not yet inserted into the database, it's inserted
	 * @param DotCoreDataRecord $record
	 */
	public function Save(DotCoreDataRecord $record)
	{
		if($record->IsEmpty())
		{
			$this->Insert($record);
		}
		else
		{
			$this->Update($record);
		}
	}

	/**
	 * Executes the deletion query given to it. If a DAL record is given instead, it'll delete the record
	 * @param DotCoreDALDeleteQuery | DotCoreDataRecord $query_record
	 */
	public function Delete($query_record)
	{
		// If a record is passed, you delete the record from the database
		$by_record = FALSE;
		if($query_record instanceof DotCoreDataRecord) {
			if($query_record->IsEmpty())
			{
				throw new Exception('Can\'t delete an empty.');
			}
			$record = $query_record;
			$restraints = $this->GetRecordRestraint($query_record);
			$query_record = new DotCoreDALDeleteQuery($this, $restraints);
			
			$this->events[self::EVENT_DELETING]->FireHandlers($record);
			$by_record = TRUE;
		}
		
		$query_record->Execute();

		if($by_record) {
			$this->events[self::EVENT_DELETED]->FireHandlers($record);
		}
	}

	/**
	 * Gets the DotCoreDALRestraint needed to restrict a query to DotCoreDataRecord $record
	 * @param DotCoreDataRecord $record
	 * @return DotCoreDALRestraint
	 */
	public function GetRecordRestraint(DotCoreDataRecord $record)
	{
		$restraints = new DotCoreDALRestraint();

		if(count($this->primary_fields) > 0)
		{
			// $primaryFields is sure to have valid field names, because they're validated when inserted
			foreach($this->primary_fields as $primary_field_name)
			{
				$restraints->AddRestraint(
					new DotCoreFieldRestraint(
						$this->fields[$primary_field_name],
						$record->GetField($primary_field_name),
						DotCoreFieldRestraint::OPERATION_EQUALS)
				);
			}
		}
		else
		{
			// If this DAL has no primary fields - restrict all fields
			$fields = $record->GetLoadedFields();
			foreach($fields as $field)
			{
				$restraints->AddRestraint(
					new DotCoreFieldRestraint(
						$field,
						$record->GetField($field->GetFieldName()),
						DotCoreFieldRestraint::OPERATION_EQUALS)
				);
			}
		}

		return $restraints;
	}

	/*
	 *
	 * Transaction Functions
	 *
	 */

	public function BeginTransaction($records) {
		// Begin a new transaction only if there isn't one running
		if($this->commited_transaction !== NULL)
		{
			DotCoreMySql::BeginTransaction();
			$this->commited_transaction = NULL;
		}
	}

	public function Rollback($records) {
		if(!is_array($records))
		{
			$records = array($records);
		}
		
		foreach($records as $record)
		{
			$this->events[self::EVENT_ROLLBACKING]->FireHandlers($record);

			// Restore the original values of record
			// $record->RestoreOriginalValues();
		}

		DotCoreMySql::Rollback();
		$this->commited_transaction = FALSE;

		foreach($records as $record)
		{
			$this->events[self::EVENT_ROLLBACK]->FireHandlers($record);
		}
	}

	public function CommitTransaction($records) {
		DotCoreMySql::CommitTransaction();
		$this->commited_transaction = TRUE;
	}

	public function TransactionCommitted() {
		return $this->commited_transaction;
	}

	public function TransactionWasRollbacked() {
		return $this->commited_transaction === FALSE;
	}

	/*
	 *
	 * MySQL Helper functions
	 *
	 */

	public static function EscapeString($str)
	{
		return DotCoreMySql::GetConnection()->real_escape_string($str);
	}

	/*
	 *
	 * Events:
	 *
	 */

	/**
	 * Event fired whenever a rollback occurs, before rollbacking
	 * @var int
	 */
	const EVENT_ROLLBACKING = 0;
	/**
	 * Event fired whenever a rollback occurs, after rollbacking
	 * @var int
	 */
	const EVENT_ROLLBACK = 1;
	/**
	 * Event fired whenever a record was successfully updated. It is fired before the original values are cleared
	 * @var int
	 */
	const EVENT_UPDATED = 2;
	/**
	 * Event fired before a record is updated
	 * @var int
	 */
	const EVENT_UPDATING = 3;
	/**
	 * Event fired whenever a record was successfully inserted
	 * @var int
	 */
	const EVENT_INSERTED = 4;
	 /**
	 * Event fired before a record is inserted
	 * @var int
	 */
	const EVENT_INSERTING = 5;
	/**
	 * Event fired whenever a record was successfully deleted
	 * @var int
	 */
	const EVENT_DELETED = 6;
	/**
	 * Event fired before a record is deleted
	 * @var int
	 */
	const EVENT_DELETING = 7;
	/**
	 * Event fired whenever a record was successfully deleted
	 * @var int
	 */
	const EVENT_VALIDATING = 8;
	/**
	 * Event fired before a record is deleted
	 * @var int
	 */
	const EVENT_VALIDATED = 9;

	 /**
	 * Holds the events available in this DAL
	 * @var array
	 */
	private $events = array(
		self::EVENT_ROLLBACKING,
		self::EVENT_ROLLBACK,
		self::EVENT_UPDATED,
		self::EVENT_UPDATING,
		self::EVENT_INSERTED,
		self::EVENT_INSERTING,
		self::EVENT_DELETED,
		self::EVENT_DELETING,
		self::EVENT_VALIDATING,
		self::EVENT_VALIDATED
	);

	/**
	 *
	 * @param int $event_id
	 * @param DotCoreEventHandler $event_handler
	 * @return DotCoreDAL
	 */
	public function RegisterEvent($event_id, DotCoreEventHandler $event_handler) {
		$this->events[$event_id]->RegisterHandler($event_handler);
		return $this;
	}

	/**
	 *
	 * @param int $event_id
	 * @param DotCoreEventHandler $event_handler
	 * @return DotCoreDAL
	 */
	public function RemoveHandler($event_id, DotCoreEventHandler $event_handler) {
		$this->events[$event_id]->RemoveHandler($event_handler);
		return $this;
	}

}

?>

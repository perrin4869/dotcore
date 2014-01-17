<?php

/**
 * DotCoreManyToManyRelationship - Describes a many-to-many relationship between 2 DALs
 *
 * @author perrin
 */
class DotCoreManyToManyRelationship extends DotCoreDALRelationship {

	public function  __construct(
		$name,
		DotCoreDALField $first_dal_field,
		DotCoreDALField $first_intermediate_dal_field,
		DotCoreDALField $second_intermediate_dal_field,
		DotCoreDALField $second_dal_field
		)
	{
		parent::__construct(
			$name,
			$first_dal_field->GetDAL(),
			$second_dal_field->GetDAL());
		
		$this->intermediate_dal = $second_intermediate_dal_field->GetDAL();

		$this->first_dal_field = $first_dal_field;
		$this->first_intermediate_dal_field = $first_intermediate_dal_field;
		$this->second_intermediate_dal_field = $second_intermediate_dal_field;
		$this->second_dal_field = $second_dal_field;
	}

	public function MakePermanent() {
		$first_dal = $this->GetFirstDAL();
		$first_dal->RegisterEvent(
			DotCoreDAL::EVENT_UPDATED,
			new DotCoreEventHandler(
				array($this, 'OnDALUpdated')
			)
		);
		$first_dal->RegisterEvent(
			DotCoreDAL::EVENT_INSERTED,
			new DotCoreEventHandler(
				array($this, 'OnDALInserted')
			)
		);
		$first_dal->RegisterEvent(
			DotCoreDAL::EVENT_VALIDATING,
			new DotCoreEventHandler(
				array($this, 'OnDALValidating')
			)
		);

		$second_dal = $this->GetSecondDAL();
		$second_dal->RegisterEvent(
			DotCoreDAL::EVENT_UPDATED,
			new DotCoreEventHandler(
				array($this, 'OnDALUpdated')
			)
		);
		$second_dal->RegisterEvent(
			DotCoreDAL::EVENT_INSERTED,
			new DotCoreEventHandler(
				array($this, 'OnDALInserted')
			)
		);
		$second_dal->RegisterEvent(
			DotCoreDAL::EVENT_VALIDATING,
			new DotCoreEventHandler(
				array($this, 'OnDALValidating')
			)
		);
	}

	const INTERMEDIATE_LINK_POSTFIX = '_intermediate_linking';
	const OLD_VALUE_STORAGE_KEY = '_old';
	const SAVED_MARK = '_many_to_many_link_saved';
	const VALIDATED_MARK = '_many_to_many_link_validated';

	public function GetIntermediateLinkName()
	{
		return $this->GetRelationshipName() . self::INTERMEDIATE_LINK_POSTFIX;
	}

	/**
	 * Holds the DAL linking between the main table and the foreign table
	 * @var DotCoreDAL
	 */
	private $intermediate_dal = NULL;

	/**
	 *
	 * @var IDotCoreDALField
	 */
	private $second_intermediate_dal_field = NULL;

	/**
	 *
	 * @var IDotCoreDALField
	 */
	private $first_intermediate_dal_field = NULL;

	/**
	 *
	 * @var IDotCoreDALField
	 */
	private $second_dal_field = NULL;
	/**
	 *
	 * @var IDotCoreDALField
	 */
	private $first_dal_field = NULL;


	/**
	 * Gets the DAL acting as an intermediate between the DAL holding this link, and the DAL held by this link
	 * @return DotCoreDAL
	 */
	public function GetIntermediateDAL() {
		return $this->intermediate_dal;
	}

	/**
	 * Gets the field in the intermediate DAL that is linked to the linking DAL
	 * @return DotCoreDALField
	 */
	public function GetFirstIntermediateDALField() {
		return $this->first_intermediate_dal_field;
	}

	/**
	 * Gets the field in the intermediate DAL that is linked to the linked DAL
	 * @return DotCoreDALField
	 */
	public function GetSecondIntermediateDALField() {
		return $this->second_intermediate_dal_field;
	}

	/**
	 * Gets the primary field in the linking DAL
	 * @return DotCoreDALField
	 */
	public function GetFirstDALField() {
		return $this->first_dal_field;
	}

	/**
	 * Gets the primary field in the linked DAL
	 * @return DotCoreDALField
	 */
	public function GetSecondDALField() {
		return $this->second_dal_field;
	}

	/**
	 *
	 * @param DotCoreDALField $linked_field
	 * @return DotCoreDALField
	 */
	public function GetIntermediateLinkedField(DotCoreDALField $linked_field) {
		return $this->GetFirstDALField() == $linked_field ? $this->GetFirstIntermediateDALField() : $this->GetSecondIntermediateDALField();
	}

	/**
	 *
	 * @param DotCoreDALField $linking_field
	 * @return DotCoreDALField
	 */
	public function GetIntermediateLinkingField(DotCoreDALField $linking_field) {
		return $this->GetFirstDALField() == $linking_field ? $this->GetFirstIntermediateDALField() : $this->GetSecondIntermediateDALField();
	}

	/**
	 *
	 * @param DotCoreDAL $dal
	 * @return DotCoreDAL
	 */
	public function GetLinkingAndLinkedFields(
			DotCoreDAL $linking_dal,
			&$linking_field,
			&$intermediate_linked_field,
			&$intermediate_linking_field,
			&$linked_field) {
		if($linking_dal == $this->GetFirstDAL()) {
			$linking_field = $this->GetFirstDALField();
			$intermediate_linked_field = $this->GetFirstIntermediateDALField();
			$intermediate_linking_field = $this->GetSecondIntermediateDALField();
			$linked_field = $this->GetSecondDALField();
		}
		else {
			$linking_field = $this->GetSecondDALField();
			$intermediate_linked_field = $this->GetSecondIntermediateDALField();
			$intermediate_linking_field = $this->GetFirstIntermediateDALField();
			$linked_field = $this->GetFirstDALField();
		}
	}

	/**
	 * This function is used to set the value of a link result into its respective object
	 * @param DotCoreDataRecord $record
	 * @param DotCoreDataRecord | array $value - If a Data Record is given, it is added to the existing records, if an array is given, exchange it with the previous array
	 */
	public function SetLinkValue(DotCoreDataRecord $record, $value = NULL)
	{
		// Simple way to store a result, may be overriden
		$links_holder = &$record->GetRecordLinkValuesHolder();
		$link_name = $this->GetRelationshipName();

		if(!isset($links_holder[$link_name]) || !is_array($links_holder[$link_name]))
		{
			$links_holder[$link_name] = array();
		}

		if($value !== NULL) // NULL == array() :(
		{
			// To choices - if the value is a Data Record, add it to the existing values
			// If the value is an array - exchange the 2 values
			if(is_array($value))
			{
				// Store the old value - used later for referencing when saving the changes
				$this->SaveCurrentValueAsOld($record);

				// Now set the new value
				$links_holder[$link_name] = $value;
			}
			else
			{
				array_push($links_holder[$link_name], $value);
			}
		}
	}

	public function GetJoinStatement(
			DotCoreDAL $linking_dal,
			$linking_type,
			DotCoreDALPath $path = NULL,
			$custom_restraints = NULL
			) {
		$this->GetLinkingAndLinkedFields($linking_dal, $linking_field, $intermediate_linked_field, $intermediate_linking_field, $linked_field);
		$linked_dal = $this->GetOppositeDAL($linking_dal);
		$intermediate_fields_path = $path == NULL ?
			new DotCoreDALPath() :
			clone($path);
		$intermediate_fields_path->append($this->GetIntermediateLinkName());
		$intermeidate_restraint = new DotCoreLinkRestraint(
			new DotCoreDALFieldPath($linking_field, $path),
			new DotCoreDALFieldPath($intermediate_linked_field, $intermediate_fields_path)
		);

		$linked_field_path = $path == NULL ?
			new DotCoreDALPath() :
			clone($path);
		$linked_field_path->append($this->GetRelationshipName());
		$link_restraint = new DotCoreLinkRestraint(
			new DotCoreDALFieldPath($intermediate_linking_field, $intermediate_fields_path),
			new DotCoreDALFieldPath($linked_field, $linked_field_path)
		);

		return
			$linking_type.' JOIN ' . $this->GetIntermediateDAL()->GetName().' AS '.$intermediate_fields_path->GetPathSQL().' ON '.$intermeidate_restraint->GetStatement().' '.
			$linking_type.' JOIN ' . $linked_dal->GetName().' AS '.$linked_field_path->GetPathSQL().' ON '.$link_restraint->GetStatement();
	}

	/**
	 * Saves the value of the record being linked to $record by this link
	 * @param DotCoreDataRecord $record - The record of the linking DAL
	 */
	public function Save(DotCoreDataRecord $record) {
		$link_name = $this->GetRelationshipName();
		$linking_dal = $record->GetDAL();
		$linked_dal = $this->GetOppositeDAL($linking_dal);
		if($record->HasLinkValueLoaded($link_name))
		{
			$link_records = $record->GetLinkValue($link_name);
			// For some stupid reason array() == NULL
			if($link_records !== NULL)
			{
				// First save all the records to the database (there may be relevant changes on them, or they might be new)
				foreach($link_records as $link_record)
				{
					if($this->RecordNeedsMarkingAsSaved($link_record)) {
						$this->MarkRecordAsSaved($link_record);
					}
					$linked_dal->Save($link_record);
				}

				// Initialize Variables used in the algorithm
				// First exchange the old values with the new ones
				$old_link_records = $this->GetOldValue($record);
				// If any new records have been added / removed
				if($old_link_records !== NULL)
				{
					$this->GetLinkingAndLinkedFields(
							$linking_dal,
							$linking_field,
							$intermediate_linked_field,
							$intermediate_linking_field,
							$linked_field);

					$linked_field_name = $linked_field->GetFieldName();
					$linking_field_name = $linking_field->GetFieldName();
					$intermediate_linked_field_name = $intermediate_linked_field->GetFieldName();
					$intermediate_linking_field_name = $intermediate_linking_field->GetFieldName();

					$linking_field_value = $record->GetField($linking_field_name);

					$intermediate_dal = $this->GetIntermediateDAL();
					// First find what records have to be deleted, and build a proper restraint
					$restraints = new DotCoreDALRestraint();
					// Find which records in the old link records do not exist in the new records
					foreach($old_link_records as $old_record)
					{
						$found = FALSE;
						foreach($link_records as $link_record)
						{
							if($linked_dal->EqualRecords($old_record, $link_record))
							{
								$found = TRUE;
								break;
							}
						}

						if(!$found)
						{
							$restraints
								->ChangeRestraintAddingMethod(DotCoreDALRestraint::RESTRAINT_ADDING_METHOD_OR)
								->OpenRestrainingUnit()
								->AddRestraint(
									new DotCoreFieldRestraint($intermediate_linked_field, $linking_field_value)
								)
								->ChangeRestraintAddingMethod(DotCoreDALRestraint::RESTRAINT_ADDING_METHOD_AND)
								->AddRestraint(
									new DotCoreFieldRestraint($intermediate_linking_field, $old_record->GetField($linked_field_name))
								)
								->CloseRestrainingUnit();
						}
					}
					// No restraint means deleting all values, be careful!
					if(!$restraints->IsEmpty())
					{
						$delete_query = new DotCoreDALDeleteQuery($intermediate_dal, $restraints);
						try
						{
							$intermediate_dal->Delete($delete_query);
						}
						catch(MySqlException $ex)
						{
							die($ex->getQuery());
						}
						unset($restraints); // Clean after yourself
					}

					// Now find what records need to be inserted
					$records = array();

					// Find which records in the new records do not exist in the old records
					foreach($link_records as $link_record)
					{
						$found = FALSE;
						foreach($old_link_records as $old_record)
						{
							if($linked_dal->EqualRecords($old_record, $link_record))
							{
								$found = TRUE;
								break;
							}
						}

						if(!$found)
						{
							$new_record = $intermediate_dal->GetNewRecord();
							$new_record->SetField($intermediate_linked_field_name, $linking_field_value);
							$new_record->SetField($intermediate_linking_field_name, $link_record->GetField($linked_field_name));
							$intermediate_dal->Insert($new_record);
						}
					}
				}
			}
		}
	}

	public function LoadLinkValue(DotCoreDataRecord $record, $entities = NULL) {
		if($entities == NULL) {
			$entities = array_values($this->GetLinkedDAL()->GetFieldsDefinitions());
		}
		$linking_dal = $record->GetDAL();
		$linked_dal = $this->GetOppositeDAL($linking_dal);
		$intermediate_dal = $this->GetIntermediateDAL();
		$this->GetLinkingAndLinkedFields(
			$linking_dal,
			$linking_field,
			$intermediate_linked_field,
			$intermediate_linking_field,
			$linked_field);

		$intermediate_path = new DotCoreDALPath(array(
				$this->GetIntermediateLinkName()
			));
		$intermediate_linking_field_path = new DotCoreDALFieldPath(
				$intermediate_linking_field,
				$intermediate_path
			);
		$intermediate_linked_field_path = new DotCoreDALFieldPath(
				$intermediate_linked_field,
				$intermediate_path
			);

		if(!array_search($linked_field, $entities)) {
			array_push($entities, $linked_field);
		}
		array_push(
			$entities,
			$intermediate_linking_field_path
		);
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreEntityRestraint(
				$intermediate_linked_field_path,
				$record->GetField($linking_field->GetFieldName())
			)
		);
		$select_query = new DotCoreDALSelectQuery($linked_dal);
		$result = $select_query
			->AddLink(
				new DotCoreDALLink(
					new DotCoreOneToManyRelationship(
						$this->GetIntermediateLinkName(),
						$linked_field,
						$intermediate_linking_field),
					$linked_dal,
					DotCoreDALLink::LINK_TYPE_INNER,
					NULL,
					FALSE // Don't store the link value
				)
			)
			->Fields($entities)
			->Restraints($restraints)
			->Select();

		$count_result = count($result);
		for($i = 0; $i < $count_result; $i++) {
			$this->SetLinkValue(
				$record,
				$result[$i]
			);
		}
	}

	/*
	 *
	 * Helper functions
	 *
	 */

	/**
	 * Saves the current value for this link as old for later referencing
	 * @param DotCoreDataRecord $record
	 */
	private function SaveCurrentValueAsOld(DotCoreDataRecord $record)
	{
		$link_name = $this->GetRelationshipName();
		
		// We save the value as old ONLY IF THERE'S NO OLD VALUE CURRENTLY INSTALLED
		// If we were to save the new old value, the reference to which to exchange the values later when saving
		// will be wrong, because the values from DB are the ones that are first saved
		if(
			$record->HasLinkValueLoaded($link_name) &&
			!$record->HasStoredValue(self::OLD_VALUE_STORAGE_KEY))
		{
			$old_value = $record->GetLinkValue($link_name);
			$record->StoreValue(self::OLD_VALUE_STORAGE_KEY, $old_value);
		}
	}

	/**
	 * Removes the old value for this link stored in $record
	 * @param DotCoreDataRecord $record
	 */
	public function RemoveOldValue(DotCoreDataRecord $record)
	{
		$record->DeleteStoredValue(self::OLD_VALUE_STORAGE_KEY);
	}

	public function GetOldValue(DotCoreDataRecord $record)
	{
		if($record->HasStoredValue(self::OLD_VALUE_STORAGE_KEY))
		{
			return $record->RetrieveValue(self::OLD_VALUE_STORAGE_KEY);
		}

		// No changes have been made
		return NULL;
	}

	public function Validate(DotCoreDataRecord $record)
	{
		$link_name = $this->GetRelationshipName();
		if($record->HasLinkValueLoaded($link_name))
		{
			$linked_records = $record->GetLinkValue($link_name);
			if($linked_records != NULL)
			{
				$result = TRUE;
				$linked_dal = $this->GetOppositeDAL($record->GetDAL());
				foreach($linked_records as $linked_record)
				{
					if($this->RecordNeedsMarkingAsValidated($linked_record)) {
						$this->MarkRecordAsValidated($linked_record);
					}
					$result = $result && $linked_dal->ValidateRecord($linked_record);
				}
				return $result;
			}
			else
			{
				return TRUE;
			}
		}
		else
		{
			return TRUE;
		}
	}

	public function OnDALUpdated(DotCoreDataRecord $record) {
		if(!$this->IsRecordMarkedAsSaved($record)) {
			$this->Save($record);
		}
		else {
			$this->UnmarkRecordAsUpdated($record);
		}
	}

	public function OnDALInserted(DotCoreDataRecord $record) {
		if(!$this->IsRecordMarkedAsSaved($record)) {
			$this->Save($record);
		}
		else {
			$this->UnmarkRecordAsUpdated($record);
		}
	}

	public function OnDALValidating(DotCoreDataRecord $record) {
		if(!$this->IsRecordMarkedAsValidated($record)) {
			return $this->Validate($record);
		}
		else {
			$this->UnmarkRecordAsValidated($record);
			return TRUE;
		}
	}

	// Infinite event loop prevention methods

	// Save prevention
	public function IsRecordMarkedAsSaved(DotCoreDataRecord $record) {
		return $record->HasStoredValue(self::SAVED_MARK);
	}

	public function MarkRecordAsSaved(DotCoreDataRecord $record) {
		$record->StoreValue(self::SAVED_MARK, TRUE);
	}

	public function UnmarkRecordAsSaved(DotCoreDataRecord $record) {
		$record->DeleteStoredValue(self::SAVED_MARK);
	}

	public function RecordNeedsMarkingAsSaved(DotCoreDataRecord $record) {
		return
			$record->HasLinkValueLoaded($this->GetRelationshipName()) &&
			$record->GetLinkValue($this->GetRelationshipName()) != NULL;
	}

	// Validate Prevention
	public function IsRecordMarkedAsValidated(DotCoreDataRecord $record) {
		return $record->HasStoredValue(self::VALIDATED_MARK);
	}

	public function MarkRecordAsValidated(DotCoreDataRecord $record) {
		$record->StoreValue(self::VALIDATED_MARK, TRUE);
	}

	public function UnmarkRecordAsValidated(DotCoreDataRecord $record) {
		$record->DeleteStoredValue(self::VALIDATED_MARK);
	}

	public function RecordNeedsMarkingAsValidated(DotCoreDataRecord $record) {
		return
			$record->HasLinkValueLoaded($this->GetRelationshipName()) &&
			$record->GetLinkValue($this->GetRelationshipName()) != NULL;
	}

}
?>

<?php

/**
 * DotCoreOneToOneRelationship - Describes a one to one link between 2 DALS
 *
 * @author perrin
 */
class DotCoreOneToOneRelationship extends DotCoreDALRelationship {

	public function  __construct(
		$name,
		DotCoreDALField $primary_field,
		DotCoreDALField $foreign_field) {
		
		parent::__construct($name, $primary_field->GetDAL(), $foreign_field->GetDAL());

		$this->primary_field = $primary_field;
		$this->foreign_field = $foreign_field;
	}

	public function MakePermanent() {
		$primary_dal = $this->GetPrimaryDAL();
		$foreign_dal = $this->GetForeignDAL();

		$primary_dal->RegisterEvent(
			DotCoreDAL::EVENT_INSERTED,
			new DotCoreEventHandler(
				array($this, 'OnPrimaryDALInserted')
			)
		);

		$foreign_dal->RegisterEvent(
			DotCoreDAL::EVENT_INSERTING,
			new DotCoreEventHandler(
				array($this, 'OnForeignDALInserting')
			)
		);

		$primary_dal->RegisterEvent(
			DotCoreDAL::EVENT_UPDATED,
			new DotCoreEventHandler(
				array($this, 'OnPrimaryDALUpdated')
			)
		);

		$foreign_dal->RegisterEvent(
			DotCoreDAL::EVENT_UPDATED,
			new DotCoreEventHandler(
				array($this, 'OnForeignDALUpdated')
			)
		);

		$primary_dal->RegisterEvent(
			DotCoreDAL::EVENT_VALIDATING,
			new DotCoreEventHandler(
				array($this, 'OnDALValidating')
			)
		);

		$foreign_dal->RegisterEvent(
			DotCoreDAL::EVENT_VALIDATING,
			new DotCoreEventHandler(
				array($this, 'OnDALValidating')
			)
		);
	}

	/**
	 *
	 * @var DotCoreDALField
	 */
	private $primary_field = NULL;

	/**
	 *
	 * @var DotCoreDALField
	 */
	private $foreign_field = NULL;

	const SAVED_MARK = '_one_to_one_link_saved';
	const VALIDATED_MARK = '_one_to_one_link_validated';

	/**
	 *
	 * @return DotCoreDALField
	 */
	public function GetPrimaryField() {
		return $this->primary_field;
	}

	/**
	 *
	 * @return DotCoreDALField
	 */
	public function GetForeignField() {
		return $this->foreign_field;
	}

	/**
	 *
	 * @return DotCoreDAL
	 */
	public function GetPrimaryDAL() {
		return $this->GetPrimaryField()->GetDAL();
	}

	/**
	 *
	 * @return DotCoreDAL
	 */
	public function GetForeignDAL() {
		return $this->GetForeignField()->GetDAL();
	}

	/**
	 *
	 * @param DotCoreDAL $dal
	 * @return DotCoreDAL
	 */
	public function GetLinkingAndLinkedFields(DotCoreDAL $linking_dal, &$linking_field, &$linked_field) {
		if($linking_dal == $this->GetPrimaryDAL()) {
			$linking_field = $this->GetPrimaryField();
			$linked_field = $this->GetForeignField();
		}
		else {
			$linking_field = $this->GetForeignField();
			$linked_field = $this->GetPrimaryField();
		}
	}
	
	/**
	 * Gets the SQL statement needed to embed the relationship in a query
	 * @param DotCoreDAL $linking_dal
	 * @param DotCoreDALPath $path
	 * @param DotCoreDALRestraint $custom_restraints
	 * @return string
	 */
	public function GetJoinStatement(
			DotCoreDAL $linking_dal,
			$linking_type,
			DotCoreDALPath $path = NULL,
			$custom_restraints = NULL) {
		$linked_dal = $this->GetOppositeDAL($linking_dal);
		$this->GetLinkingAndLinkedFields($linking_dal, $linking_field, $linked_field);
		$linked_field_path = $path == NULL ?
			new DotCoreDALPath() :
			clone($path);
		$linked_field_path->append($this->GetRelationshipName());
		$link_restraint = new DotCoreLinkRestraint(
			new DotCoreDALFieldPath($linking_field, $path),
			new DotCoreDALFieldPath($linked_field, $linked_field_path)
		);
		return $linking_type.' JOIN '.$linked_dal->GetName().' AS '.$linked_field_path->GetPathSQL().' ON '.$link_restraint->GetStatement();
	}

	/**
	 * Saves the value of the record being linked to $record by this link
	 * @param DotCoreDataRecord $record - The record of the linking DAL
	 */
	public function Save(DotCoreDataRecord $record) {
		$link_name = $this->GetRelationshipName();
		if($record->HasLinkValueLoaded($link_name)) {
			$link_record = $record->GetLinkValue($link_name);
			if($link_record != NULL) {
				$this->GetOppositeDAL($record->GetDAL())->Save($link_record);
			}
		}
	}

	public function Validate(DotCoreDataRecord $record) {
		$link_name = $this->GetRelationshipName();
		if($record->HasLinkValueLoaded($link_name))
		{
			$linked_record = $record->GetLinkValue($link_name);
			if($this->RecordNeedsMarkingAsValidated($linked_record)) {
				$this->MarkRecordAsValidated($linked_record);
			}
			return $linked_record != NULL ? 
				$this->GetOppositeDAL($record->GetDAL())->ValidateRecord($linked_record) :
				TRUE;
		}
		else
		{
			return TRUE;
		}
	}

	public function LoadLinkValue(DotCoreDataRecord $record, $entities = NULL) {
		$this->GetLinkingAndLinkedFields($record->GetDAL(), $linking_field, $linked_field);
		$dal = $this->GetOppositeDAL($record->GetDAL());
		if($entities == NULL) {
			$entities = $dal()->GetFieldsDefinitions();
		}

		$select_query = new DotCoreDALSelectQuery($dal);
		$restraints = new DotCoreDALRestraint();
		$restraints->AddRestraint(
			new DotCoreFieldRestraint(
				$linked_field,
				$record->GetField($linking_field->GetFieldName())
			)
		);
		$result = $select_query
			->Fields($entities)
			->Restraints($restraints)
			->SelectFirstOrNull();
		$this->SetLinkValue($record, $result);
	}

	public function OnPrimaryDALInserted(DotCoreDataRecord $record) {
		if(!$this->IsRecordMarkedAsSaved($record)) {
			if($record->HasLinkValueLoaded($this->GetRelationshipName()))
			{
				$link_value = $record->GetLinkValue($this->GetRelationshipName());
				$link_value->SetField(
					$this->GetForeignField()->GetFieldName(),
					$record->GetField($this->GetPrimaryField()->GetFieldName()));
				if($this->RecordNeedsMarkingAsSaved($link_value)) {
					$this->MarkRecordAsSaved($link_value);
				}
				$this->GetForeignDAL()->Save($link_value);
			}
		}
		else {
			$this->UnmarkRecordAsUpdated($record);
		}
	}

	public function OnForeignDALInserting(DotCoreDataRecord $record) {
		if(!$this->IsRecordMarkedAsSaved($record)) {
			$relationship_name = $this->GetRelationshipName();
			if(
					$record->HasLinkValueLoaded($relationship_name) &&
					$record->GetLinkValue($relationship_name) != NULL)
			{
				$link_value = $record->GetLinkValue($this->GetRelationshipName());
				if($this->RecordNeedsMarkingAsSaved($link_value)) {
					$this->MarkRecordAsSaved($link_value);
				}
				$this->GetPrimaryDAL()->Save($link_value);
				$record->SetField(
					$this->GetForeignField()->GetFieldName(),
					$link_value->GetField($this->GetPrimaryField()->GetFieldName()));
			}
		}
		else{
			$this->UnmarkRecordAsUpdated($record);
		}
	}

	public function OnPrimaryDALUpdated(DotCoreDataRecord $record) {
		if(!$this->IsRecordMarkedAsSaved($record)) {
			$relationship_name = $this->GetRelationshipName();
			if($record->HasLinkValueLoaded($relationship_name))
			{
				// If the record is marked as updated, it means this event was triggered after
				// saving OnForeignDALUpdated was triggered, and that the record was marked there as updated
				// This is here in order to prevent an infinite loop

				$link_value = $record->GetLinkValue($relationship_name);
				if($link_value->IsEmpty())
				{
					$link_value->SetField(
						$this->GetForeignField()->GetFieldName(),
						$record->GetField($this->GetPrimaryField()->GetFieldName()));
				}
				// If we don't mark this as updated, OnForeignDALUpdated will be triggered and save it again
				// And then this event will be triggered, and so on, creating an infinite loop
				if($this->RecordNeedsMarking($link_value)) {
					$this->MarkRecordAsUpdated($link_value);
				}
				$this->GetForeignDAL()->Save($link_value);
			}
		}
		else{
			$this->UnmarkRecordAsUpdated($record);
		}
	}

	public function OnForeignDALUpdated(DotCoreDataRecord $record) {
		// If the record is marked as updated, it means this event was triggered after
		// saving OnPrimaryDALUpdated was triggered, and that the record was marked there as updated
		// This is here in order to prevent an infinite loop
		if(!$this->IsRecordMarkedAsSaved($record)) {
			if($record->HasLinkValueLoaded($this->GetRelationshipName()))
			{

				$link_value = $record->GetLinkValue($this->GetRelationshipName());
				if($record->FieldChanged($this->GetForeignField()->GetFieldName())) {
					// The foreign key has changed, so we must unload the link value as it points
					// to the wrong record
					$this->UnloadLinkValue($record);
				}
				else {
					// If we don't mark this as updated, OnPrimaryDALUpdated will be triggered and save it again
					// And then this event will be triggered, and so on, creating an infinite loop
					// If the value was empty, it'll not trigger the second event (because it'll be inserted, not updated)
					// so no need for marking
					if($this->RecordNeedsMarkingAsSaved($link_value)) {
						$this->MarkRecordAsSaved($link_value);
					}
					$this->GetPrimaryDAL()->Save($link_value);
				}
			}
		}
		else {
			$this->UnmarkRecordAsSaved($record);
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

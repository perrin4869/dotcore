<?php

/**
 * DotCoreOneToManyRelationship - Describes a one-to-many link between 2 DALs
 *
 * @author perrin
 */
class DotCoreOneToManyRelationship extends DotCoreDALRelationship {

    public function  __construct(
        $name,
        DotCoreDALField $primary_field,
        DotCoreDALField $foreign_field) {

        parent::__construct($name, $primary_field->GetDAL(), $foreign_field->GetDAL());

        $this->primary_field = $primary_field;
        $this->foreign_field = $foreign_field;
    }

    public function MakePermanent() {
        $this->GetPrimaryDAL()->RegisterEvent(
            DotCoreDAL::EVENT_UPDATED,
            new DotCoreEventHandler(
                array($this, 'OnPrimaryDALUpdated')
            )
        );

        $this->GetForeignDAL()->RegisterEvent(
            DotCoreDAL::EVENT_UPDATED,
            new DotCoreEventHandler(
                array($this, 'OnForeignDALUpdated')
            )
        );

        // After this record was saved, save the subsequent ones
        $this->GetPrimaryDAL()->RegisterEvent(
            DotCoreDAL::EVENT_INSERTED,
            new DotCoreEventHandler(
                array($this, 'OnPrimaryDALInserted')
            )
        );

        // First saved the linked record, and then store its id on the linking record
        $this->GetForeignDAL()->RegisterEvent(
            DotCoreDAL::EVENT_INSERTING,
            new DotCoreEventHandler(
                array($this, 'OnForeignDALInserting')
            )
        );

        $this->GetPrimaryDAL()->RegisterEvent(
            DotCoreDAL::EVENT_VALIDATING,
            new DotCoreEventHandler(
                array($this, 'OnDALValidating')
            )
        );

        $this->GetForeignDAL()->RegisterEvent(
            DotCoreDAL::EVENT_VALIDATING,
            new DotCoreEventHandler(
                array($this, 'OnDALValidating')
            )
        );
    }

    /**
     * Holds the linking field in this relationship
     * @var DotCoreDALField
     */
    private $primary_field = NULL;

    /**
     * Holds the linked field in this relationship
     * @var DotCoreDALField
     */
    private $foreign_field = NULL;
    
    const SAVED_MARK = '_one_to_many_link_saved';
    const VALIDATED_MARK = '_one_to_many_link_validated';

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
        return $this->primary_field->GetDAL();
    }

    /**
     *
     * @return DotCoreDAL
     */
    public function GetForeignDAL() {
        return $this->foreign_field->GetDAL();
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
     * This function is used to set the value of a link result into its respective object
     * @param DotCoreDataRecord $record
     * @param DotCoreDataRecord $value
     */
    public function SetLinkValue(DotCoreDataRecord $record, DotCoreDataRecord $value = NULL)
    {
        // Simple way to store a result, may be overriden
        $links_holder = &$record->GetRecordLinkValuesHolder();
        $link_name = $this->GetRelationshipName();

        // There are two options here:
        // If the field in this DAL linked to the remote DAL is a primary field, there are multiple records
        // of this DAL inside the remote DAL, and so we need to construct an array
        // If it's a regular field - then it's just one single value
        if($record->GetDAL() == $this->GetPrimaryDAL())
        {
            if($links_holder[$link_name] == NULL)
            {
                $links_holder[$link_name] = array();
            }
            // No use pushing null values
            if($value != NULL)
            {
                array_push($links_holder[$link_name], $value);
            }
        }
        else
        {
            /*
             * No idea what I was thinking
            if($value == NULL) {
                $value = $this->GetLinkedDAL()->GetNewRecord();
            }
             *
             */
            $links_holder[$link_name] = $value;
        }
    }

    public function LoadLinkValue(DotCoreDataRecord $record, $entities = NULL) {
        $linked_dal = $this->GetOppositeDAL($record->GetDAL());
        if($entities == NULL) {
            $entities = $linked_dal->GetFieldsDefinitions();
        }
        $linked_dal = $this->GetLinkedDAL();
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint(
                $this->GetForeignField(),
                $record->GetField($this->GetPrimaryField()->GetFieldName())
            )
        );
        $result = $linked_dal
            ->Fields($entities)
            ->Restraints($restraints)
            ->Select();
        $count_result = count($result);
        for($i = 0; $i < $count_result; $i++) {
            $this->SetLinkValue($record, $result[$i]);
        }
    }

    /**
     * Gets the SQL statement needed to embed the link in a query
     * @param DotCoreDAL $linking_dal
     * @param string $linking_type The way in which the link should be deployed
     * @param DotCoreDALPath $path
     * @param DotCoreDALRestraint $custom_restraints
     * @return string
     */
    public function GetJoinStatement(
            DotCoreDAL $linking_dal,
            $linking_type,
            DotCoreDALPath $path = NULL,
            $custom_restraints = NULL
        )
    {
        $link_name = $this->GetRelationshipName();
        $linked_dal = $this->GetOppositeDAL($linking_dal);
        $this->GetLinkingAndLinkedFields($linking_dal, $linking_field, $linked_field);
        if($path == NULL) {
            $path = new DotCoreDALPath();
            $linked_field_path = new DotCoreDALPath();
        }
        else {
            $linked_field_path = clone($path);
        }
        $linked_field_path->append($this->GetRelationshipName());
        $link_restraint = new DotCoreLinkRestraint(
            new DotCoreDALFieldPath($linking_field, $path),
            new DotCoreDALFieldPath($linked_field, $linked_field_path));
        $restraint = new DotCoreDALRestraint();
        $restraint->AddRestraint($link_restraint);
        if($custom_restraints != NULL) {
            $restraint
                ->ChangeRestraintAddingMethod(DotCoreDALRestraint::RESTRAINT_ADDING_METHOD_AND)
                ->OpenRestrainingUnit()
                ->AddRestraint($custom_restraints)
                ->CloseRestrainingUnit();
        }

        return $linking_type.' JOIN ' . $linked_dal->GetName().' AS '.$linked_field_path->GetPathSQL().' ON '.$restraint->GetRestraintSQL();
    }

    /**
     * Saves the value of the record being linked to $record by this link
     * @param DotCoreDataRecord $record - The record of the linking DAL
     */
    public function Save(DotCoreDataRecord $record) {
        $link_name = $this->GetRelationshipName();
        if($record->HasLinkValueLoaded($link_name))
        {
            $link_record = $record->GetLinkValue($link_name);
            if($link_record != NULL)
            {
                $linked_dal = $this->GetOppositeDAL($record->GetDAL());
                if(is_array($link_record)) {
                    // Save each one of the records changed
                    // Use foreach instead of normal for because the values may be ordered as a dictionary instead of
                    // a list
                    foreach($link_record as $curr_link_record)
                    {
                        $linked_dal->Save($curr_link_record);
                    }
                }
                else
                {
                    // Assume DotCoreDataRecord
                    $linked_dal->Save($link_record);
                }
            }
        }
    }

    public function Validate(DotCoreDataRecord $record) {
        $link_name = $this->GetRelationshipName();
        if($record->HasLinkValueLoaded($link_name))
        {
            $link_record = $record->GetLinkValue($link_name);
            if($link_record != NULL)
            {
                $linked_dal = $this->GetOppositeDAL($record->GetDAL());
                if(is_array($link_record))
                {
                    $result = TRUE;
                    // Save each one of the records changed
                    // Use foreach instead of normal for because the values may be ordered as a dictionary instead of
                    // a list
                    foreach($link_record as $curr_link_record)
                    {
                        if($this->RecordNeedsMarkingAsValidated($curr_link_record)) {
                            $this->MarkRecordAsValidated($curr_link_record);
                        }
                        $result = $result && $linked_dal->ValidateRecord($curr_link_record);
                    }
                    return $result;
                }
                else
                {
                    // Assume DotCoreDataRecord
                    if($this->RecordNeedsMarkingAsValidated($link_record)) {
                        $this->MarkRecordAsValidated($link_record);
                    }
                    return $linked_dal->ValidateRecord($link_record);
                }
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

    public function OnPrimaryDALUpdated(DotCoreDataRecord $primary_record) {
        if(!$this->IsRecordMarkedAsSaved($primary_record)) {
            if($primary_record->HasLinkValueLoaded($this->GetRelationshipName())) {
                $foreign_dal = $this->GetForeignDAL();
                $link_values = $primary_record->GetLinkValue($this->GetRelationshipName());
                $foreign_field_name = $this->GetForeignField()->GetFieldName();
                $primary_field_value = $primary_record->GetField($this->GetPrimaryField()->GetFieldName());

                foreach($link_values as $link_value) {
                    // We may be inserting new items, so set the required fields
                    $link_value->SetField(
                        $foreign_field_name,
                        $primary_field_value
                    );
                    if($this->RecordNeedsMarkingAsSaved($link_value)) {
                        $this->MarkRecordAsSaved($link_value);
                    }
                    $foreign_dal->Save($link_value);
                }
            }
        }
        else {
            $this->UnmarkRecordAsUpdated($primary_record);
        }
    }

    public function OnForeignDALUpdated(DotCoreDataRecord $foreign_record) {
        if(!$this->IsRecordMarkedAsSaved($foreign_record)) {
            if($foreign_record->HasLinkValueLoaded($this->GetRelationshipName()))
            {
                // If the foreign field has changed, unload the linked field -
                // it no longer points to the correct record
                if($foreign_record->FieldChanged($this->GetForeignField()->GetFieldName())){
                    $this->UnloadLinkValue($foreign_record);
                }
                else {
                    $link_value = $foreign_record->GetLinkValue($this->GetRelationshipName());
                    if($this->RecordNeedsMarkingAsSaved($link_value)) {
                        $this->MarkRecordAsSaved($link_value);
                    }
                    $this->GetPrimaryDAL()->Save($link_value);
                }
            }
        }
        else {
            $this->UnmarkRecordAsUpdated($foreign_record);
        }
    }

    public function OnPrimaryDALInserted(DotCoreDataRecord $primary_record) {
        // Save all the records
        if(!$this->IsRecordMarkedAsSaved($primary_record)) {
            if($primary_record->HasLinkValueLoaded($this->GetRelationshipName()))
            {
                $foreign_dal = $this->GetForeignDAL();
                $link_values = $primary_record->GetLinkValue($this->GetRelationshipName());
                $foreign_field_name = $this->GetForeignField()->GetFieldName();
                $primary_field_value = $primary_record->GetField($this->GetPrimaryField()->GetFieldName());

                foreach($link_values as $link_value) {
                    $link_value->SetField(
                        $foreign_field_name,
                        $primary_field_value
                    );
                    if($this->RecordNeedsMarkingAsSaved($link_value)) {
                        $this->MarkRecordAsSaved($link_value);
                    }
                    $foreign_dal->Save($link_value);
                }
            }
        }
        else {
            $this->UnmarkRecordAsUpdated($primary_record);
        }
    }

    public function OnForeignDALInserting(DotCoreDataRecord $foreign_record) {
        if(!$this->IsRecordMarkedAsSaved($foreign_record)) {
            if($foreign_record->HasLinkValueLoaded($this->GetRelationshipName()))
            {
                $primary_dal = $this->GetPrimaryDAL();
                $link_value = $foreign_record->GetLinkValue($this->GetRelationshipName());
                if($this->RecordNeedsMarkingAsSaved($link_value)) {
                    $this->MarkRecordAsSaved($link_value);
                }
                $primary_dal->Save($link_value);
                $foreign_record->SetField(
                    $this->GetForeignField()->GetFieldName(),
                    $link_value->GetField($this->GetPrimaryField()->GetFieldName()));
            }
        }
        else {
            $this->UnmarkRecordAsUpdated($foreign_record);
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
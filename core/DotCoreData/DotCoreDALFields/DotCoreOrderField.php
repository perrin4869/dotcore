<?php

/**
 * Represents a field which describes the order of the records
 * @author perrin
 *
 */
class DotCoreOrderField extends DotCoreIntField
{
    public function __construct(
        $field_name,
        DotCoreDAL $dal,
        $is_nullable = TRUE)
    {
        parent::__construct($field_name, $dal, $is_nullable);

        $dal->RegisterEvent(
            DotCoreDAL::EVENT_INSERTING,
            new DotCoreEventHandler(
                array($this, 'OnInserting')
            )
        );
        
    }

    public function OnInserting(DotCoreDataRecord $record) {
        $field_name = $this->GetFieldName();
        if($this->IsEmpty($record->GetField($field_name))) {
            $dal = $this->GetDAL();
            $max_order = $dal
                ->Fields(
                    array(
                        new DotCoreMax($this)
                    )
                )
                ->SelectScalar();
            $dal->FinalizeSelection();

            $record->SetField($field_name, $max_order + 1);
        }
    }
}

?>
<?php

/**
 * Class used to represent any date field
 * @author perrin
 *
 */
class DotCoreDateField extends DotCoreDALField
{
    public function __construct(
        $field_name,
        DotCoreDAL $dal,
        $is_nullable = TRUE)
    {
        parent::__construct($field_name, $dal, $is_nullable);
    }

    public function Validate(DotCoreDataRecord $record, &$val)
    {
        $parent_validation = parent::Validate($record, $val);

        if(!$this->IsEmpty($val))
        {
            $timestamp = DotCoreDateTime::DateTimeStringToTimestamp($val); // If the date is invalid, it'll throw an exception here
            // Make sure the it's the correct format
            $val = date('Y-m-d', $timestamp);
        }

        return $parent_validation;
    }

    public function IsEmpty($val)
    {
        return empty($val);
    }

    public function GetValuePreparedForQuery($val)
    {
        if(self::IsEmpty($val))
        {
            return 'NULL';
        }
        return '\''.$val.'\''; // No need to escape, we know the value is a valid date, so no quotes
    }
}

?>
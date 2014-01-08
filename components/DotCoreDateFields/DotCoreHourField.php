<?php

class InvalidHourException extends DotCoreException {}

/**
 * Class used to represent any hour database field
 * @author perrin
 *
 */
class DotCoreHourField extends DotCoreIntField
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
        $result = parent::Validate($record, $val);

        // Check the validity of the hour
        if(!$this->IsEmpty($val) && ($val < 0 || $val > 23))
        {
            throw new InvalidHourException();
        }

        return $result;
    }
}

?>
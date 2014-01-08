<?php

/**
 * Class used to represent any timestamp database field
 * @author perrin
 *
 */
class DotCoreTimestampField extends DotCoreIntField
{
    public function __construct(
        $field_name,
        DotCoreDAL $dal,
        $is_nullable = TRUE)
    {
        parent::__construct($field_name, $dal, $is_nullable);
    }
}

?>
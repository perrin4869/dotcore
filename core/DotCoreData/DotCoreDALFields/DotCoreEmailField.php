<?php

class InvalidEmailException extends DotCoreException {}

/**
 * Class used to represent any email database field
 * @author perrin
 *
 */
class DotCoreEmailField extends DotCoreStringField
{
    public function __construct(
        $field_name,
        DotCoreDAL $dal,
        $is_nullable = TRUE)
    {
        parent::__construct($field_name, $dal, $is_nullable);
    }

    protected static function CheckEmail($email) {
        return is_email($email);
    }

    public function Validate(DotCoreDataRecord $record, &$val)
    {
        $result = parent::Validate($record, $val);

        // If this is empty, no need to check email validity
        // Furthermore, we know that it CAN be empty, because we would have been thrown already were it not
        // possible
        if(!$this->IsEmpty($val) && !self::CheckEmail($val))
        {
            throw new InvalidEmailException();
        }

        return $result;
    }
}

?>
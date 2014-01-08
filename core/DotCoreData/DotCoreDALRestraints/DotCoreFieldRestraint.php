<?php

/**
 * Description of DotCoreFieldRestraint
 *
 * @author perrin
 */
class DotCoreFieldRestraint extends DotCoreEntityRestraint {

    public function  __construct(IDotCoreDALField $field, $value, $operation = self::OPERATION_EQUALS) {
        parent::__construct($field, $value, $operation);
    }

}
?>

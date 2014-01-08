<?php

/**
 * DotCoreFieldSelectionOrder - Describes the order of a field
 *
 * @author perrin
 */
class DotCoreFieldSelectionOrder extends DotCoreDALSelectionOrderUnit {

    public function  __construct(DotCoreDALField $field, $direction = self::DIRECTION_DESC) {
        $this->field = $field;
        $this->direction = $direction;
    }

    const DIRECTION_ASC = 'ASC';
    const DIRECTION_DESC = 'DESC';

    private $field = NULL;
    private $direction = NULL;

    public function GetOrderField()
    {
        return $this->field;
    }

    public function GetOrderDirection()
    {
        return $this->direction;
    }

    public function GetStatement()
    {
        return $this->field->GetSQLNameWithTablePrefix() . ' ' . $this->direction;
    }

}
?>

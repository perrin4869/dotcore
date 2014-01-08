<?php

/**
 * Description of DotCoreFulltextRestraint
 *
 * @author perrin
 */
class DotCoreFulltextRestraint extends DotCoreDALRestraintUnit {

    public function  __construct($fulltext, $value) {
        $this->fulltext = $fulltext;
        $this->value = $value;
    }

    /**
     * Holds the definition of the fulltext to be restrained
     *
     * @var DotCoreDALFulltext
     */
    private $fulltext = NULL;

    /**
     * The value to which the fulltext is restrained
     *
     * @var mixed
     */
    private $value = NULL;

    /*
     *
     * Public accessors:
     *
     */

    /**
     * Gets the fulltext restrainted by this restraint
     * @return DotCoreDALFulltext
     */
    public function GetFulltext()
    {
        return $this->fulltext;
    }

    /**
     * Sets the fulltext that will be restrained
     *
     * @param DotCoreDALFulltext $fulltext
     * @return DotCoreFulltextRestraint
     */
    public function SetFulltext(DotCoreDALFulltext $fulltext)
    {
        $this->fulltext = $fulltext;
        return $this;
    }

    /**
     * Gets the value to which the fulltext of this DotCoreFulltextRestraint is restrained
     * @return mixed
     */
    public function GetValue()
    {
        return $this->value;
    }

    /**
     * Sets the value to which the field of this DotCoreFulltextRestraint is restrained
     *
     * @param mixed $val
     * @return DotCoreFulltextRestraint
     */
    public function SetValue($val)
    {
        $this->value = $val;
        return $this;
    }

    /**
     * Gets the SQL statement resulting from this DotCoreFulltextRestraint
     * @return string
     */
    public function GetStatement() {
        $statement = '';
        $fields = $this->fulltext->GetFields();
        $count_fields = count($fields);
        for($i = 0; $i < $count_fields; $i++)
        {
            if($i > 0)
            {
                $statement .= ',';
            }
            $statement .= $fields[$i]->GetSQLNameWithTablePrefix();
        }
        $prepared_val = DotCoreDAL::EscapeString($this->value);
        // It is recommended to remove commas
        $prepared_val = str_replace(",", " ", $prepared_val);
        $statement = 'MATCH('.$statement.') AGAINST(\'' . $prepared_val . '\')';
        return $statement;
    }

}
?>

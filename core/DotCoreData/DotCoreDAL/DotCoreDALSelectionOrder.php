<?php

/**
 * DotCoreDALSelectionOrder defines the order in which the selected elements from a DAL selection should be ordered
 *
 * @author perrin
 */
class DotCoreDALSelectionOrder {

    /**
     * Constructor for DotCoreDALSelectionOrder
     */
    public function  __construct() {
        
    }

    private $order_units = array();

    /**
     * Adds a new unit to the selection order. It'll be one level lower in the selection
     * @param DotCoreDALSelectionOrderUnit $order
     * @return DotCoreDALSelectionOrder
     */
    public function AddOrderUnit(DotCoreDALSelectionOrderUnit $order)
    {
        array_push($this->order_units, $order);
        return $this;
    }

    public function GetStatement()
    {
        $units_count = count($this->order_units);
        $result = '';
        for($i = 0; $i < $units_count; $i++)
        {
            if($i > 0)
            {
                $result .= ', ';
            }
            $result .= $this->order_units[$i]->GetStatement();
        }
        return $result;
    }
    
}
?>

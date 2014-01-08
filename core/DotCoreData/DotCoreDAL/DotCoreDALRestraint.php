<?php

class InvalidRestraintAddingMethodException extends DotCoreException { }
class InvalidRestraintUnitException extends DotCoreException { }

/**
 * DotCoreDALRestraint - Represents a restraint, supporting compound restraints.
 * Is composed of DotCoreDALRestraintUnit objects
 *
 * @author perrin
 */
class DotCoreDALRestraint extends DotCoreObject {

    public function  __construct() {
        $this->current_restraint = &$this->restraints;
        $this->current_restraint_method = self::RESTRAINT_ADDING_METHOD_AND;
    }

    public function  __destruct() {
        unset($this->restraints); // Free all the restraints resources
    }

    /**
     * Holds the restraints as a hierarchy of arrays, to represent the parenthesis in conditional statements
     * Example:
     * array(
     *  array(
     *      restraint, self::RESTRAINT_ADDING_METHOD_AND,
     *          array(
     *              restraint, self::RESTRAINT_ADDING_METHOD_OR,
     *              restraint),
     *          self::RESTRAINT_ADDING_METHOD_AND, restraint),
     *      self::RESTRAINT_ADDING_METHOD_OR, restraint) =
     * (restraint AND (restraint OR restraint) AND restraint) OR restraint
     * @var array
     */
    private $restraints = array();

    /**
     * Holds the array to which newer restraints are added
     * @var array
     */
    private $current_restraint = NULL;

    /**
     * Holds the previous restraining units so when the current one is closed, this previous ones will come to play
     * @var array
     */
    private $prev_restraints = array();

    /**
     * The way more restraints are added
     * @var int
     */
    private $current_restraint_method = NULL;

    /**
     * Constants
     */

    const RESTRAINT_ADDING_METHOD_AND = 'AND';
    const RESTRAINT_ADDING_METHOD_OR = 'OR';

    /**
     * Used to check whether any restraints were given to this restraints at all
     * @return boolean
     */
    public function IsEmpty()
    {
        return (count($this->restraints) == 0);
    }

    /**
     * Adds a restraint to the selecting of the next query, or an array of restraints
     *
     * @param DotCoreDALRestraint | array $restraint
     * @return DotCoreDALRestraint - Chainable command
     */
    public function AddRestraint($restraint)
    {
        if(count($this->current_restraint) != 0)
        {
            // Add a restraining relationship ONLY if there's already a restraint in the restraining array
            array_push($this->current_restraint, $this->current_restraint_method);
        }

        // Pass by reference - if it's an array, we have more we for it, so we need to record that copy
        array_push($this->current_restraint, &$restraint);
        return $this;
    }

    /**
     * Gets the restraint unit in position $num
     * @param int $num
     * @return DotCoreDALRestraintUnit
     */
    public function GetRestraintUnit($num)
    {
        $count_restraints = count($this->restraints);
        for($i = 0; $i < $count_restraints; $i++)
        {
            if($this->restraints[$i] instanceof DotCoreDALRestraintUnit)
            {
                return $this->restraints[$i];
            }
        }

        return NULL;
    }

    /**
     * Changes the way in which restraints are added, in respect to previous restraints
     *
     * @param int $method
     * @return DotCoreDALRestraint - Chainable command
     */
    public function ChangeRestraintAddingMethod($method)
    {
        $this->current_restraint_method = $method;
        return $this;
    }

    /**
     * Opens a new unit that encloses all the added restraints, until CloseRestrainingUnit is called
     *
     * @return DotCoreDALRestraint - Chainable command
     */
    public function OpenRestrainingUnit()
    {
        array_push($this->prev_restraints, &$this->current_restraint);
        $current = array();
        // Pass by reference so that all the subsequent restraints are added to that reference of the array
        $this->AddRestraint(&$current);
        // All the references are added to THIS reference of the array
        // It's times like this I thank god I learned C++!!!!!!! So many useful concepts!
        $this->current_restraint = &$current;
        return $this;
    }

    /**
     * Closes the current open unit of restraints, or throws an exception if it fails
     *
     * @throws InvalidRestraintClosingException if the closing is invalid
     * @return DotCoreDALRestraint - Chainable command
     */
    public function CloseRestrainingUnit()
    {
        $count_prev = count($this->prev_restraints);
        if($count_prev == 0)
        {
            throw new InvalidRestraintClosingException();
        }

        $this->current_restraint = &$this->prev_restraints[$count_prev-1];
        array_pop($this->prev_restraints); // Remove the last element
        return $this; // Make this chainable
    }

    /**
     * Removes all the currently inserted restraints
     *
     * @return DotCoreDALRestraint - Chainable command
     */
    public function ClearRestraints()
    {
        unset($this->restraints);
        $this->restraints = array();
        return $this;
    }

    /**
     * Function used to return the SQL conditional statement conveyed by this restraint
     * @return string
     */
    public function GetRestraintSQL()
    {
        // Parse the restraint
        // The restraint is recursive, so we'll need a recursive helper function
        $command = '';
        $this->GetRestraintSQLRecursiveHelper($command, $this->restraints);
        return $command;
    }

    private function GetRestraintSQLRecursiveHelper(&$command, $current_array)
    {
        $count = count($current_array);
        for($i = 0; $i < $count; $i++)
        {
            if(is_string($current_array[$i]))
            {
                // AND or OR
                $command .= ' ' . $current_array[$i];
            }
            elseif(is_array($current_array[$i]))
            {
                 // We're talking about a sub-restraint, so enclose in parenthesis
                $command .= ' (';
                $this->GetRestraintSQLRecursiveHelper($command, $current_array[$i]);
                $command .= ')';
            }
            elseif($current_array[$i] instanceof DotCoreDALRestraintUnit)
            {
                /**
                 * DotCoreDALRestraintUnit
                 */
                $restraint_unit = $current_array[$i];
                $command .= ' ' . $restraint_unit->GetStatement();
            }
            elseif($current_array[$i] instanceof DotCoreDALRestraint)
            {
                /**
                 * DotCoreDALRestraint
                 */
                $restraint_unit = $current_array[$i];
                // Add whole restraints as individual units
                $command .= ' (' . $restraint_unit->GetRestraintSQL() . ')';
            }
            else
            {
                throw new InvalidRestraintUnitException();
            }
        }
    }

}

?>
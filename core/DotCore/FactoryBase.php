<?php

/**
 * A class used for creating other classes
 * @author perrin
 *
 */
abstract class FactoryBase extends DotCoreObject
{
    private function __construct() { }

    abstract static public function GetInstance();

    /**
     * Creates an instance of the class the factory is programmed to create
     * @return DotCoreObject
     */
    abstract public function Create();
}

?>
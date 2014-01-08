<?php

/**
 * Base class for all objects in this project
 * @author perrin
 *
 */
class DotCoreObject
{
    public function GetType()
    {
        return get_class($this);
    }
}

?>
<?php

/**
 * Defines the handler for an event
 */
class DotCoreEventHandler
{

    public function  __construct($callback) {
        $this->callback = $callback;
    }

    /**
     * Holds the callback function to be used by the handler
     * @var callback
     */
    private $callback;

    // Accessors

    public function GetCallback()
    {
        return $this->callback;
    }

    public function SetCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * Fires the handler with the callback given to it
     * @param array $params
     */
    public function Fire($params = array())
    {
        if(is_array($params))
        {
            return call_user_func_array($this->callback, $params);
        }
        else
        {
            return call_user_func($this->callback, $params);
        }
    }

}

?>

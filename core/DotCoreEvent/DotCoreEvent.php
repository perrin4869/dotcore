<?php

/**
 * A class that defines an event for an object
 */
class DotCoreEvent
{

	public function  __construct() {
		
	}

	/**
	 * Holds the handlers given to this event
	 * @var array of callbacks
	 */
	private $handlers = array();

	public function FireHandlers($params = array())
	{
		if(!is_array($params))
		{
			$params = array($params);
		}

		$count_handlers = count($this->handlers);
		for($i = 0; $i < $count_handlers; $i++)
		{
			$this->handlers[$i]->Fire($params);
		}
	}

	/**
	 * Gets the handlers registered to this event
	 * @return array
	 */
	public function GetHandlers() {
		return $this->handlers;
	}

	/**
	 * Adds $handler to the list of handlers to be fired by this event
	 * @param DotCoreEventHandler $handler
	 */
	public function RegisterHandler(DotCoreEventHandler $handler)
	{
		array_push($this->handlers, $handler);
	}

	/**
	 * Removes $handler to the list of handlers to be fired by this event
	 * @param DotCoreEventHandler $handler
	 * @return void
	 */
	public function RemoveHandler(DotCoreEventHandler $handler)
	{
		$count_handlers = count($this->handlers);
		for($i = 0; $i < $count_handlers; $i++)
		{
			if($this->handlers[$i] === $handler)
			{
				$this->handlers[$i] = NULL;
				$this->handlers = array_values($this->handlers);
				return;
			}
		}
	}

	/**
	 * Removes all the handlers registered to this array
	 */
	public function RemoveHandlers() {
		unset($this->handlers);
		$this->handlers = array();
	}

}

?>

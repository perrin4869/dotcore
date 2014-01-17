<?php

/**
 * DotCoreArray - Custom array object
 *
 */
class DotCoreArray extends DotCoreObject implements Iterator, ArrayAccess, Countable {
	
	private $arr = NULL;

	function __construct($array = array()) {
		if(!is_array($array))
		{
			$array = array($array);
		}
		$this->arr = $array;
	}
	
	/**
	 * Returns the list as a normal PHP array
	 *
	 * @return Array<Object>
	 */
	public function &toArray()
	{
		return $this->arr;
	}

	public function append($val)
	{
		array_push($this->arr, $val);
	}

	public function shift()
	{
		return array_shift($this->arr);
	}

	public function unshift($val)
	{
		array_unshift($this->arr, $val);
	}

	public function pop()
	{
		return array_pop($this->arr);
	}

	public function is_empty() {
		return empty($this->arr);
	}
	
	/*
	 *
	 * Iterator implementation
	 *
	 */

	/**
	 * A switch to keep track of the end of the array
	 */
	private $valid = FALSE;

	/**
	 * Return the array "pointer" to the first element
	 * PHP's reset() returns false if the array has no elements
	 */
	function rewind() {
		$this->valid = (FALSE !== reset($this->arr));
	}

	/**
	 * Return the current array element
	 */
	function current() {
		return current($this->arr);
	}

	/**
	 * Return the key of the current array element
	 */
	function key() {
		return key($this->arr);
	}

	/**
	 * Move forward by one
	 * PHP's next() returns false if there are no more elements
	 */
	function next() {
		$this->valid = (FALSE !== next($this->arr));
	}

	/**
	 * Is the current element valid?
	 */
	function valid() {
		return $this->valid;
	}

	/*
	 *
	 * ArrayAccess implementation
	 *
	 */

	/**
	 * Defined by ArrayAccess interface
	 * Set a value given it's key e.g. $A['title'] = 'foo';
	 * @param mixed key (string or integer)
	 * @param mixed value
	 * @return void
	 */
	function offsetSet($key, $value) {
		$this->arr[$key] = $value;
	}

	/**
	 * Defined by ArrayAccess interface
	 * Return a value given it's key e.g. echo $A['title'];
	 * @param mixed key (string or integer)
	 * @return mixed value
	 */
	function offsetGet($key) {
		return $this->arr[$key];
	}

	/**
	 * Defined by ArrayAccess interface
	 * Unset a value by it's key e.g. unset($A['title']);
	 * @param mixed key (string or integer)
	 * @return void
	 */
	function offsetUnset($key) {
		unset($this->arr[$key]);
	}

	/**
	 * Defined by ArrayAccess interface
	 * Check value exists, given it's key e.g. isset($A['title'])
	 * @param mixed key (string or integer)
	 * @return boolean
	 */
	function offsetExists($offset) {
		return array_key_exists($key, $this->arr);
	}

	/*
	 *
	 * Countable implementation
	 *
	 */

	/**
	 * Defined by Countable interface
	 * Returns the number of items in this this list (when requested by count(), for example)
	 * @return int
	 *
	 */
	public function count()
	{
		return count($this->arr);
	}
}

?>

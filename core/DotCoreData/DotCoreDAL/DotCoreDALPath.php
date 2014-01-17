<?php

/**
 * DotCoreDALPath - Defines a path through a hierarchy of linked DALs
 *
 * @author perrin
 */
class DotCoreDALPath extends DotCoreArray {

	/**
	 * Constructor for DotCoreDALPath
	 * @param array $path
	 */
	public function  __construct($path = array()) {
		parent::__construct($path);
	}

	/**
	 * Gets the path described by this DotCoreDALPath in a sequential array
	 * @return array
	 */
	public function GetPathArray() {
		return $this->toArray();
	}

	public function GetPathSQL() {
		return join('_', $this->GetPathArray());
	}

	public function GetSubPath($length) {
		if($length < 1)
		{
			return NULL; // No path to get in this case
		}
		$array = array_chunk($this->GetPathArray(), $length);
		return new DotCoreDALPath($array);
	}

	/**
	 * Helper function used to build a path from a string with a dot as a delimiter
	 * @param DotCoreDAL $root_dal
	 * @param string $str
	 */
	public static function BuildPath($str)
	{
		return new DotCoreDALPath(explode('.', $str));
	}

}
?>

<?php

/**
 * DotCoreSQLSelectionOrder - Describes the order of a SQL string
 *
 * @author perrin
 */
class DotCoreSQLSelectionOrder extends DotCoreDALSelectionOrderUnit {

	public function  __construct($sql) {
		$this->sql = $sql;
	}

	private $sql = NULL;

	public function GetStatement()
	{
		return $this->sql;
	}

}
?>

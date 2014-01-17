<?php

/**
 * IDotCoreDALLinkedEntity provides the interface for linked entities
 *
 * @author perrin
 */
interface IDotCoreDALLinkedEntity extends IDotCoreDALSelectableEntity {

	/**
	 * Function used to get the record inside the hierarchy which holds the value of this entity
	 * @param DotCoreDataRecord $root_record
	 */
	function GetLinkedRecord(DotCoreDataRecord $root_record);

}
?>

<?php

/*
 *
 * File used to include basic classes needed to run the DAL library
 *
 */

$dirname = dirname(__FILE__);

include ($dirname . '/IDotCoreDALSelectableEntity.php');
include ($dirname . '/IDotCoreDALLinkedEntity.php');
include ($dirname . '/IDotCoreDALField.php');
include ($dirname . '/IDotCoreDALLinkedField.php');

include ($dirname . '/Exceptions.php');
include ($dirname . '/DotCoreDAL.php');
include ($dirname . '/DotCoreDALSelectQuery.php');
include ($dirname . '/DotCoreDALDeleteQuery.php');
include ($dirname . '/DotCoreDALEntityBase.php');
include ($dirname . '/DotCoreDALField.php');
include ($dirname . '/DotCoreDALPath.php');
include ($dirname . '/DotCoreDALEntityPath.php');
include ($dirname . '/DotCoreDALFieldPath.php');
include ($dirname . '/DotCoreDALFulltext.php');
include ($dirname . '/DotCoreDataRecord.php');
include ($dirname . '/DotCoreDALRestraint.php');
include ($dirname . '/DotCoreDALRestraintUnit.php');
include ($dirname . '/DotCoreDALRelationship.php');
include ($dirname . '/DotCoreDALLink.php');
include ($dirname . '/DotCoreDALLinkTree.php');
include ($dirname . '/DotCoreDALSelectionOrder.php');
include ($dirname . '/DotCoreDALSelectionOrderUnit.php');

?>

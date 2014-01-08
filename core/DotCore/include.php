<?php

/* 
 *
 * Serves to include basic framework components:
 *
 */

$dirname = dirname(__FILE__);

// Base
include ($dirname . '/DotCoreObject.php');

// Exceptions
include ($dirname . '/Exceptions.php');

// Helpers
include ($dirname . '/DotCoreHelper.php');

// Data Models
include ($dirname . '/DotCoreTree.php');
include ($dirname . '/DotCoreTreeNode.php');
include ($dirname . '/DotCoreArray.php');

// DateTime
include ($dirname . '/DotCoreDateTime.php');

// MySql
include ($dirname . '/DotCoreMySql.php');

// File System
include ($dirname . '/DotCoreFile.php');

// File System
include ($dirname . '/DotCoreExternalComponentsAutoloader.php');

// Basic Factory require
include ($dirname . '/FactoryBase.php');

?>

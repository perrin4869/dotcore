<?php

/*
 *
 * Includes and registers Data Access library components
 *
 */

$data_folder = 'DotCoreData/';

// Data Access
include ($core_folder . $data_folder . 'DotCoreDAL/include.php');

// Business Logic Layer
include ($core_folder . $data_folder . 'DotCoreBLL/include.php');

function register_core_dal_field($field) {
	global $data_folder;
	register_core_component($field, $data_folder . 'DotCoreDALFields/' . $field . '.php');
}

function register_core_dal_entity($entity) {
	global $data_folder;
	register_core_component($entity, $data_folder . 'DotCoreDALEntities/' . $entity . '.php');
}

function register_core_dal_relationship($link) {
	global $data_folder;
	register_core_component($link, $data_folder . 'DotCoreDALRelationships/' . $link . '.php');
}

function register_core_dal_restraint($restraint) {
	global $data_folder;
	register_core_component($restraint, $data_folder . 'DotCoreDALRestraints/' . $restraint . '.php');
}

function register_core_dal_selection_order($selection_order) {
	global $data_folder;
	register_core_component($selection_order, $data_folder . 'DotCoreDALSelectionOrders/' . $selection_order . '.php');
}

// Register Data Access Components

register_core_dal_field('DotCoreIntField');
register_core_dal_field('DotCoreAutoIncrementingKey');
register_core_dal_field('DotCoreStringField');
register_core_dal_field('DotCorePlainStringField');
register_core_dal_field('DotCoreMultilineStringField');
register_core_dal_field('DotCoreHTMLStringField');
register_core_dal_field('DotCoreURLField');
register_core_dal_field('DotCoreEmailField');
register_core_dal_field('DotCorePasswordField');
register_core_dal_field('DotCoreBooleanField');
register_core_dal_field('DotCoreDateField');
register_core_dal_field('DotCoreDateTimeField');
register_core_dal_field('DotCoreImageField');
register_core_dal_field('DotCoreRecursiveIntField');
register_core_dal_field('DotCoreTimestampField');
register_core_dal_field('DotCoreOrderField');

register_core_dal_entity('DotCoreCount');
register_core_dal_entity('DotCoreMax');
register_core_dal_entity('DotCoreMin');

register_core_dal_restraint('DotCoreEntityRestraint');
register_core_dal_restraint('DotCoreFieldRestraint');
register_core_dal_restraint('DotCoreFulltextRestraint');
register_core_dal_restraint('DotCoreLinkRestraint');

register_core_dal_relationship('DotCoreOneToOneRelationship');
register_core_dal_relationship('DotCoreOneToManyRelationship');
register_core_dal_relationship('DotCoreManyToManyRelationship');

register_core_dal_selection_order('DotCoreFieldSelectionOrder');
register_core_dal_selection_order('DotCoreSQLSelectionOrder');

?>

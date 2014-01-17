<?php

/*
 *
 * Includes the files needed to run the DotCoreConfiguration library
 *
 */

// Form Elements
$configuration_folder = 'DotCoreConfiguration/';

register_core_component('DotCoreConfiguration', $configuration_folder . 'DotCoreConfiguration/include.php');
register_core_component('DotCoreConfigurationField', $configuration_folder . 'DotCoreConfiguration/include.php');

function register_core_configuration_field($field_name, $class) {
	global $configuration_folder;
	register_core_component($class, $configuration_folder . 'DotCoreConfigurationFields/' . $class . '.php');
	DotCoreConfiguration::AddType($field_name, $class);
}

register_core_configuration_field('string', 'DotCoreStringConfigurationField');
register_core_configuration_field('rich_string', 'DotCoreRichStringConfigurationField');
register_core_configuration_field('int', 'DotCoreIntConfigurationField');

?>

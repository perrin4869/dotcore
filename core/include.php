<?php

$core_folder = dirname(__FILE__) . '/';

// Include basic components
// Core Classes
include ($core_folder . 'DotCore/include.php');

/**
 * Registers a component from DotCore Core library with a path relative to the installation of the core
 *
 * @param string $component_name
 * @param string $path_from_core_root
 */
function register_core_component($component_name, $path_from_core_root) {
    global $core_folder;
    DotCoreExternalComponentsAutoloader::RegisterComponent($component_name, $core_folder . $path_from_core_root);
}

// Events
include ($core_folder . 'DotCoreEvent/include.php');

// Data Access
include ($core_folder . 'DotCoreData/include.php');

// Forms
include ($core_folder . 'DotCoreForms/include.php');

// Configuration
include ($core_folder . 'DotCoreConfiguration/include.php');

?>

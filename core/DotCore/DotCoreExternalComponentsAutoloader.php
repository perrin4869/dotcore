<?php

class DotCoreExternalComponentsAutoloader
{
	
	private static $registered_components = array();

	public static function RegisterComponent($class_name, $path, $configuration = NULL)
	{
		self::$registered_components[$class_name] = array();
		self::$registered_components[$class_name]['path'] = $path;
		if(is_array($configuration)) {
			self::$registered_components[$class_name]['configuration'] = $configuration;
		}
	}

	public static function UnregisterComponent($class_name)
	{
		unset(self::$registered_components[$class_name]);
	}

	public static function Load($component)
	{
	if(key_exists($component, self::$registered_components)) {
		$path = self::$registered_components[$component]['path'];
		if(file_exists($path))
		{
			include($path);
		}
	}
	}

	public static function GetComponentsConfiguration($component_name) {
		if(
			self::IsComponentRegistered($component_name) &&
			is_array(self::$registered_components[$component_name]['configuration'])
		)
		{
			return self::$registered_components[$component_name]['configuration'];
		}
		else {
			return array();
		}
	}

	public static function IsComponentRegistered($component_name) {
		return is_array(self::$registered_components[$component_name]);
	}
	
	public static function RegisteredComponentsAutoload($class_name)
	{
		// Try to load
		if(key_exists($class_name, self::$registered_components))
		{
			self::Load($class_name);
		}
		// It's not always required to include a class!
		/*
		if(!class_exists($class_name))
		{
			throw new Exception('Class ' . $class_name . ' not found');
		}
		 *
		 */
	}

}

spl_autoload_register(array('DotCoreExternalComponentsAutoloader', 'RegisteredComponentsAutoload'));

?>

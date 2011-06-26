<?php
namespace Base;

class Helper extends \Base\Object
{
	/**
	 * Array of stored helper paths
	 * 
	 * @var array
	 */
	protected static $_namespaces = array();
	
	/**
	 * Store an array of instances of helpers
	 * 
	 * @var array
	 */
	protected static $_helpers = array();
	
	/**
	 * Return an instance of the desired helper
	 * 
	 * @param string $path
	 * @return \Base\Helper\HelperAbstract
	 */
	public static function get($path)
	{
		if(!array_key_exists($path, self::$_helpers)){
			$parts = explode('/', $path);
			if(count($parts) == 2 && array_key_exists($parts[0], self::$_namespaces)){
				$class = self::$_namespaces[$parts[0]].str_replace(' ','\\', ucwords(str_replace('_',' ', $parts[1])));
				if(class_exists($class)){
					self::$_helpers[$path] = new $class();
				} else {
					throw new \Base\Exception('Valid helper path, but invalid helper name');
				}
			} else {
				throw new \Base\Exception("Invalid helper path supplied");
			}
		}
		return self::$_helpers[$path];
	}
	
	/**
	 * Preload the helper paths
	 * 
	 * @return \Base\Helper
	 */
	public static function loadHelpers()
	{
		//get all the config
		$config = \Base\Config::instance()->getAllData('Helpers');
		
		foreach($config as $namespace=>$data)
		{
			foreach($data as $vpath=>$nspath){
				self::$_namespaces[$vpath] = $nspath;
			}
		}
	}
}
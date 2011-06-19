<?php
namespace Base\Helper;

class Namespacing extends HelperAbstract
{
	/**
	 * Get the full namespace path of a given module
	 * 
	 * @return string
	 */
	public function get($module)
	{
		$namespace = '';
		if($prefix = \Base\Config::path($module.'/_routerNamespace')){
			$namespace .= $prefix.'\\';
		}
		$namespace .= self::$module;
		return $namespace;
	}
}
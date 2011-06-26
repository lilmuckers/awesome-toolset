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
			if(!is_array($prefix)){
				$namespace .= $prefix.'\\';
			}
		}
		$namespace .= $module;
		return $namespace;
	}
}
<?php
namespace Base\Cli;

class Router extends \Base\Object
{
	/**
	 * Classname pattern for controllers
	 */
	const CONTROLLER_CLASSNAME = '\%s\Controller\Cli';
	
	/**
	 * Store the routing
	 * 
	 * @var array
	 */
	protected static $_routes = array();
	
	/**
	 * Perform the routing
	 * 
	 * If appropriate action is found - then we return it as a callback
	 * If one isn't found, we return false - this is the signal for a 404.
	 * 
	 * @param \Base\Web\Action $action
	 * @return mixed
	 */
	public static function route($command)
	{
		
		if(!array_key_exists($command, self::$_routes)){
			//it is not found! 404
			return false;
		}
		
		$namespace = '';
		if($prefix = \Base\Config::path(self::$_routes[$command].'/_routerNamespace')){
			$namespace .= $prefix.'\\';
		}
		$namespace .= self::$_routes[$command];
		
		//build the controller classname
		$class = sprintf(self::CONTROLLER_CLASSNAME,
			$namespace
		);
		
		if(!class_exists($class)){
			//the class in question doesn't exist!
			return false;
		}
		
		//build the callback and send it away for processing :)
		return $class;
	}
	
	
	/**
	 * Parse the routing basenames
	 * 
	 * @return void
	 */
	public static function loadRoutes()
	{
		//get all the config
		$config = \Base\Config::instance()->getAllData('Routing','cli');
		
		foreach($config as $namespace=>$data)
		{
			self::$_routes[$data] = $namespace;
		}
	}
}
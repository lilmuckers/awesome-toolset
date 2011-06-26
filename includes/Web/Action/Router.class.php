<?php
namespace Base\Web\Action;

class Router extends \Base\Object
{
	/**
	 * Classname pattern for controllers
	 */
	const CONTROLLER_CLASSNAME = '\%s\Controller\Web\%s';
	
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
	public static function route(\Base\Web\Action $action)
	{
		$path = $action->getRequest()->getRequestPath();
		
		if(!array_key_exists($path[0], self::$_routes)){
			//it is not found! 404
			return false;
		}
		
		//build the appropriate namespace
		$namespace = \Base\Helper::get('base/namespacing')->get(self::$_routes[$path[0]]);

		
		//build the controller classname
		$class = sprintf(self::CONTROLLER_CLASSNAME,
			$namespace,
			str_replace(' ','\\', ucwords(str_replace('_', ' ', strtolower($path[1]))))
		);
		
		if(!class_exists($class)){
			//the class in question doesn't exist!
			return false;
		}
		
		//build the action name
		$actionName = lcfirst(str_replace(' ','', ucwords(str_replace('_', ' ', strtolower($path[2])))).'Action');
		if(!in_array($actionName, get_class_methods($class))){
			//this isn't a callable action
			return false;
		}
		
		//Set the route in the request object
		$action->getRequest()->setRoute($class, $actionName);
		
		//build the callback and send it away for processing :)
		return array($class, $actionName);
	}
	
	/**
	 * Parse the routing basenames
	 * 
	 * @return void
	 */
	public static function loadRoutes()
	{
		//get all the config
		$config = \Base\Config::instance()->getAllData('Routing','basename');
		
		foreach($config as $namespace=>$data)
		{
			self::$_routes[$data] = $namespace;
		}
	}
}
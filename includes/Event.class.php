<?php
namespace Base;

class Event extends \Base\Object
{
	/**
	 * The events storage
	 * 
	 * @var array
	 */
	protected static $_eventCallbacks = array();
	
	/**
	 * Store instances of objects to be used as instances
	 * 
	 * @var array
	 */
	protected static $_classInstances = array();
	
	/**
	 * Build an array of the events to fire
	 * 
	 * @return void
	 */
	public static function registerEvents()
	{
		$scopes = Scope::getScopes();
		
		//get the global events
		$global = Config::instance()->getAllData('Events');
		
		//iterate through all the scopes and get all the events for them
		foreach($scopes as $scope){
			$moduleEvents = \Base\Config::instance()->getAllData('Events-'.$scope);
			$moduleEvents = array_merge_recursive($global, $moduleEvents);
			foreach($moduleEvents as $module=>$events){
				foreach($events as $eventName=>$callback){
					
					//get the event hook
					$event = $callback['event'];
					
					//get the classname for the callback
					$namespace = Helper::get('base/namespacing')->get($module);
					
					//unset the class var to make it work
					unset($class);
					if(class_exists($namespace.'\\'.$callback['class'])){
						$class = $namespace.'\\'.$callback['class'];
					} elseif(class_exists($namespace.'\\Model\\'.$callback['class'])){
						$class = $namespace.'\\Model\\'.$callback['class'];
					}
					
					if(isset($class)){
						//build the callback
						unset($cb);
						if($callback['type'] == 'singleton'){
							$cb = $class.'::'.$callback['method'];
						} else {
							$object = array_key_exists($class, self::$_classInstances) ? self::$_classInstances[$class] : new $class();
							self::$_classInstances[$class] = $object;
							$cb = array($object, $callback['method']);
						}
						self::$_eventCallbacks[$scope][$event][] = $cb;
					}
				}
				
			}
			
		}
	}
	
	/**
	 * Fire off an event
	 * 
	 * @param string $event
	 * @param array $params
	 * @return void
	 */
	public static function fire($event, $params = array())
	{
		$parameters = new Object($params);
		$parameters->setEventName($event);
		
		$currentScope = Scope::getScope();
		
		if(array_key_exists($currentScope, self::$_eventCallbacks)){
			if(array_key_exists($event, self::$_eventCallbacks[$currentScope])){
				$cbs = self::$_eventCallbacks[$currentScope][$event];
				foreach($cbs as $cb){
					call_user_func_array($cb, array($parameters));
				}
			}
		}
	}
}
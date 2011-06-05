<?php
namespace Base;

class Controller extends Object
{
	/**
	 * Runs the command against the correct module
	 * 
	 * @return \Base\Controller
	 */
	public function cli($arguments)
	{
		//pull out the first important vars - the first one is the script, we don't care about that.
		list( , $module, $action) = $arguments;
		//drop off the pointless stuff from the arguments array
		for($i=0;$i<3;$i++){
			array_shift($arguments);
		}
		
		$class = "\\{$module}\Controller\Cli";
		$controller = new $class();
		if(!$action){
			$action = 'run';
		}
		
		call_user_func_array(array($controller, $action), $arguments);
		
		return $this;
	}
	
	/**
	 * Launches the web modules using the supplied path as the command string
	 * 
	 * @return \Base\Controller
	 */
	public function web()
	{
		//we want to start the session first =)
		session_start();
		
		//we want to instantiate the action
		$action = Web\Action::instance();
		
		//get the routing
		if($route = Web\Action\Router::route($action)){
			//split up the routing params for use
			list($controller, $action) = $route;
			
			//instantiate the controller
			$controller = new $controller();
			
			//dispatch the action
			$controller->preDispatch();
			$controller->$action();
			
			//sort out the response
			$action->getResponse()->output();
			
			//clean up the response
			$controller->postDispatch();
		}
		
		
	}
}

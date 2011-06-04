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
		
		$class = "\\{$module}\Cli";
		$controller = new $class();
		if(!$action){
			$action = 'run';
		}
		
		call_user_func_array(array($controller, $action), $arguments);
		
		return $this;
	}
}

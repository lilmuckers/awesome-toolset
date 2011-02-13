<?php

class Controller extends BaseObject
{
	/**
	 * Runs the command against the correct module
	 * 
	 * @return Run
	 */
	public function run($arguments)
	{
		//pull out the first important vars - the first one is the script, we don't care about that.
		list( , $module, $action) = $arguments;
		//drop off the pointless stuff from the arguments array
		for($i=0;$i<3;$i++){
			array_shift($arguments);
		}
		
		$controller = new $module();
		if(!$action){
			$action = 'run';
		}
		
		call_user_func_array(array($controller, $action), $arguments);
		
		return $this;
	}
}

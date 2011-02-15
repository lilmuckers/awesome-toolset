<?php

class BaseController extends BaseObject
{
	/**
	 * The pattern for the sql definition class
	 */
	const DEFINITION_CLASS_PATTERN = '%sDefine';
	
	/**
	 * Check for SQL description and install if extant
	 * 
	 * @return BaseController
	 */
	public function install()
	{
		//create the classname from the parent classname
		$class = sprintf(self::DEFINITION_CLASS_PATTERN ,get_class($this));
		
		//check it exists, and run it if it does.
		if(AutoLoader::Instance()->classExists($class)){
			$tableDefine = new $class();
			$tableDefine->install();
		}
		return $this;
	}
}

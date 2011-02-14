<?php

class BaseController extends BaseObject
{
	public function install()
	{
		$class = get_class($this).'Define';
		
		if(AutoLoader::Instance()->fileExists($class)){
			$tableDefine = new $class();
			$tableDefine->install();
		}
		return $this;
	}
}
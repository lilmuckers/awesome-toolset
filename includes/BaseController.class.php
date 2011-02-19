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
	
	/**
	 * Boolean return for a confirmation, using STDIN/OUT
	 * 
	 * @param string $string
	 * @return bool
	 */
	protected function _confirm($string)
	{
		fwrite(STDOUT, $string.': [Yes/No] ');
		$confirm = strtolower(trim(fgets(STDIN)));
		if(in_array($confirm, array('yes', 'y'))){
			return true;
		} elseif(in_array($confirm, array('no', 'n'))) {
			return false;
		}
		$red = "\033[0;31m";
		$end = "\033[0m";
		fwrite(STDOUT, "{$red}[ERROR] Input must be Yes or No{$end}\n\n");
		return $this->_confirm($string);
	}
}

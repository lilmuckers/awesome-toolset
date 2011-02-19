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
		$confirm = strtolower($this->_input($string." [Yes/No] "));
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
	
	/**
	 * Get the input 
	 * 
	 * @param string $string
	 * @return string
	 */
	protected function _input($string)
	{
		fwrite(STDOUT, $string);
		return trim(fgets(STDIN));
	}
	
	/**
	 * Get the input without echoing whatever is typed out
	 * 
	 * @param string $string
	 * @return string
	 */
	protected function _silentInput($string)
	{
		if (preg_match('/^win/i', PHP_OS)) {
			$vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
			file_put_contents(
				$vbscript, 'wscript.echo(InputBox("'
				. addslashes($string)
				. '", "", "password here"))'
			);
			$command = "cscript //nologo " . escapeshellarg($vbscript);
			$password = rtrim(shell_exec($command));
			unlink($vbscript);
			return $password;
		} else {
			$command = "/usr/bin/env bash -c 'echo OK'";
			if (rtrim(shell_exec($command)) !== 'OK') {
				trigger_error("Can't invoke bash");
				return;
			}
			$command = "/usr/bin/env bash -c 'read -s -p \""
				. addslashes($string)
				. "\" mypassword && echo \$mypassword'";
			$password = rtrim(shell_exec($command));
			echo "\n";
			return $password;
		}
	}
}

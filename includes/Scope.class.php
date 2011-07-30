<?php
namespace Base;

class Scope
{
	/**
	 * The current run scope
	 * 
	 * @var string
	 */
	protected static $_scope;
	
	/**
	 * Constants to represent the various scope levels
	 */
	const SCOPE_WEB			= 'web';
	const SCOPE_CLI			= 'cli';
	const SCOPE_WEB_ADMIN	= 'admin';
	const SCOPE_CRON		= 'cron';
	
	/**
	 * Set the scope of the current runtime
	 * 
	 * @param string $scope
	 * @return void
	 */
	public static function setScope($scope)
	{
		self::_validateScope($scope);
		self::$_scope = $scope;
		Event::fire('register_scope_after');
	}
	
	/**
	 * Get the current scope
	 * 
	 * @return string
	 */
	public static function getScope()
	{
		return self::$_scope;
	}
	
	/**
	 * Validate the given scope
	 * 
	 * @param string $scope
	 * @return bool
	 * @throws \Base\Exception\Scope
	 */
	protected static function _validateScope($scope)
	{
		foreach(self::getScopes() as $const=>$value){
			if($value == $scope){
				return true;
			}
		}
		throw new Exception\Scope("Invalid scope '{$scope}'", 101);
	}
	
	/**
	 * Get all the available scopes
	 * 
	 * @return array
	 */
	public static function getScopes()
	{
		$reflection = new \ReflectionClass(__CLASS__);
		return $reflection->getConstants();
	}
}
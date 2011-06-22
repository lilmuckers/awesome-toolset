<?php
namespace Base;

class Registry
{
	/**
	 * Array of data to store in the global registry
	 * 
	 * @var array
	 */
	protected static $_regData = array();
	
	/**
	 * Assign data to the registry
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public static function set($key, $value){
		if(self::has($key)){
			throw new Exception("Registry entry already exists");
		}
		self::$_regData[$key] = $value;
	}
	
	/**
	 * Get the data from the registry
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public static function get($key){
		if(!self::has($key)){
			throw new Exception("Registry entry doesn't exist");
		}
		return self::$_regData[$key];
	}
	
	/**
	 * check the registry for a key
	 * 
	 * @param string $key
	 * @return bool
	 */
	public static function has($key){
		return array_key_exists($key, self::$_regData);
	}
	
	/**
	 * Unset an item from the registry
	 * 
	 * @param string $key
	 * @return void
	 */
	public static function uns($key){
		if(!self::has($key)){
			throw new Exception("Registry entry doesn't exist");
		}
		unset(self::$_regData[$key]);
	}
}
<?php

class Cli extends BaseObject
{
	/**
	 * List of all foreground colours
	 * 
	 * @var array
	 */
	protected static $_foregroundColours = array(
		'black'			=> '0,30',
		'dark_gray'		=> '1,30',
		'blue'			=> '0,34',
		'light_blue'	=> '1,34',
		'green'			=> '0,32',
		'light_green'	=> '1,32',
		'cyan'			=> '0,36',
		'light_cyan'	=> '1,36',
		'red'			=> '0,31',
		'light_red'		=> '1,31',
		'purple'		=> '0,35',
		'light_purple'	=> '1,35',
		'brown'			=> '0,33',
		'yellow'		=> '1,33',
		'light_gray'	=> '0,37',
		'white'			=> '1,37'
	);
	
	/**
	 * List of all background colours
	 * 
	 * @var array
	 */
	protected static $_backgroundColours = array(
		'black'			=> '40',
		'red'			=> '41',
		'green'			=> '42',
		'yellow'		=> '43',
		'blue'			=> '44',
		'magenta'		=> '45',
		'cyan'			=> '46',
		'light_gray'	=> '47'
	);
	
	/**
	 * Print out the formatted string
	 * 
	 * @param string $string
	 * @param string $foreground
	 * @param string $background
	 * @return void
	 */
	public static function write($string, $foreground = null, $background = null)
	{
		//print the colour hashes
		if(!is_null($foreground) && array_key_exists($foreground, self::$_foregroundColours)){
			$string = "\033[" . self::$_foregroundColours[$foreground] . "m".$string;
		}
		if(!is_null($background) && array_key_exists($background, self::$_backgroundColours)){
			$string = "\033[" . self::$_backgroundColours[$background] . "m".$string;
		}
		
		if((!is_null($foreground) && array_key_exists($foreground, self::$_foregroundColours)) || (!is_null($background) && array_key_exists($background, self::$_backgroundColours))){
			$string .= "\033[0m";
		}
		fwrite(STDOUT, $string);
	}
}
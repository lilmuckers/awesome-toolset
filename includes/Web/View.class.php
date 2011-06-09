<?php
namespace Base\Web;

class View extends View\Recursive
{
	/**
	 * Constants for the various View layer file types
	 */
	const FILE_TYPE_LAYOUT		= 'layout';
	const FILE_TYPE_TEMPLATE	= 'template';
	const FILE_TYPE_SKIN		= 'skin';
	
	/**
	 * Resource types for the skin elements
	 */
	const SKIN_TYPE_IMAGE		= 'images';
	const SKIN_TYPE_JS			= 'js';
	
	/**
	 * Array of patterns to find generate the paths we're after
	 * 
	 * @var array
	 */
	protected static $_filePathPatterns = array(
		'layout'	=> 'theme/%s/%s/layouts/%s',
		'template'	=> 'theme/%s/%s/templates/%s',
		'skin'		=> '%s/skin/%s/%s/%s'
	);

	/**
	 * The default fallback theme
	 * 
	 * @var string
	 */
	protected static $_fallbackTheme = 'default';
	
	/**
	 * Get thje layout file path
	 * 
	 * @param string $filename
	 * @return \SimpleXMLElement
	 * @throws \Base\Exception\View
	 */
	public static function getLayout($filename)
	{
		$path = self::getFilePath($filename, self::FILE_TYPE_LAYOUT);
		
		//load the file as simplexml - watching for errors
		libxml_use_internal_errors(true);
		$file = simplexml_load_file($path, '\Base\SimpleXML\Element');
		
		//catch any pesky XML errors
		if($error = libxml_get_last_error()){
			throw new \Base\Exception\View("XML Error 'Expected \"{$error->message}\" on line {$error->line}' for file: {$path}");
		}
		return $file;
	}
	
	/**
	 * Get the template file path - taking into account fallbacks
	 * 
	 * @param string $filename
	 * @return string
	 */
	public static function getTemplateFilePath($filename)
	{
		return self::getFilePath($filename, self::FILE_TYPE_TEMPLATE);
	}
	
	/**
	 * Get the skin resource file path - taking into account fallbacks
	 * 
	 * @param string $filename
	 * @return string
	 */
	public static function getSkinFilePath($filename, $resourceType = self::SKIN_TYPE_IMAGE)
	{
		return self::getFilePath($filename, self::FILE_TYPE_SKIN, $resourceType);
	}
	
	/**
	 * Get the currently active theme
	 * 
	 * @return string
	 */
	protected static function _getTheme()
	{
		$theme = \Base\Config::path("Base/Theme/".\Base\Scope::getScope()."/theme");
		return $theme ? $theme : self::$_fallbackTheme;
	}
	
	/**
	 * Return the path for the given file type and check if it exists
	 * 
	 * @param $filename
	 * @param $pathType
	 * @return string
	 * @throws \Base\Exception\View
	 */
	public static function getFilePath($filename, $pathType, $additional = array())
	{
		if(!array_key_exists($pathType, self::$_filePathPatterns)){
			throw new \Base\Exception\View("No file pattern for that file type");
		}
		
		//get the file pattern and use it to generate the theme filepath
		$pattern = self::$_filePathPatterns[$pathType];
		
		//build the array of values
		$args = array(
			\Base\Scope::getScope(),
			self::_getTheme()
		);
		
		//if we want more values, they go between the base information and the filename
		if(!empty($additional)){
			foreach((array)$additional as $a){
				$args[] = $a;
			}
		}
		
		$args[] = $filename;
		$filePath = vsprintf($pattern, $args);
		
		//check the file exists
		if(!file_exists($filePath)){
			//if it doesn't; use the fallback
			$args[1] = self::$_fallbackTheme;
			$filePath = vsprintf($pattern, $args);
			if(!file_exists($filePath)){
				throw new \Base\Exception\View("File does not exist in custom theme nor default");
			}
		}
		
		return $filePath;
	}
}
<?php
namespace Base\Web;

class View extends \Base\Object
{
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
	 * @throws \Base\Exception\View\Layout
	 */
	public static function getLayout($filename)
	{
		return 'default';
	}
	
	/**
	 * Get the template file path - taking into account fallbacks
	 * 
	 * @param string $filename
	 * @return string
	 * @throws \Base\Exception\View\Template
	 */
	public static function getTemplateFilePath($filename)
	{
	
	}
}
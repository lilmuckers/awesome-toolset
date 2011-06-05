<?php
namespace Base\Web\Action\Response\View;

class Layout extends \Base\Object
{
	/**
	 * Cache of the instance of this class
	 * 
	 * @var \Base\Web\Action\Response\View\Layout
	 */
	static protected $_instance;
	
	/**
	 * The layout xml all munged together
	 * 
	 * @var \SimpleXMLElement
	 */
	protected $_layoutXml;
	
	/**
	 * Fallback theme for the layout files
	 * 
	 * @var string
	 */
	protected $_fallback = 'default';
	
	/**
	 * Singleton Instance Initiation
	 * 
	 * @return \Base\Web\Action\Response\View\Layout
	 */
	static public function instance()
	{
		if(!self::$_instance){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Load the layout files, merging elements appropriately.
	 * 
	 * @return void
	 */
	public function loadFiles()
	{
		//get all the config
		$config = \Base\Config::instance()->getAllData('Theme','layout');
		
		//arrange the data for a plain list of files
		$files = array();
		foreach($config as $data){
			$files = array_merge($files, $data);
		}
		
		
		
		var_dump($files);
		
	}
}
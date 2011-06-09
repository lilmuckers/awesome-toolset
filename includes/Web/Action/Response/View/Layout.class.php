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
	 * The default exception type to throw
	 * 
	 * @var string
	 */
	protected $_exceptionClass = '\Base\Exception\View';
	
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
	 * The merged layout object
	 * 
	 * @var Base\SimpleXML\Element
	 */
	protected $_merged;
	
	/**
	 * The output layout object
	 * 
	 * @var Base\SimpleXML\Element
	 */
	protected $_output;
	
	/**
	 * Store the template Hierarchy
	 * 
	 * @var \Base\Web\View
	 */
	protected $_view;
	
	/**
	 * Store the template references
	 * 
	 * @var array
	 */
	protected $_templateReference;
	
	/**
	 * Instantiate the needed child objects
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct();
		$this->_view = new \Base\Web\View();
	}
	
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
		
		//Now we merge all the files together :)
		foreach($files as $file){
			$xml = \Base\Web\View::getLayout($file);
			if(!$this->_merged){
				$this->_merged = $xml;
			} else {
				$this->_merged->mergeXml($xml);
			}
		}
	}
	
	/**
	 * Start building the output layout
	 * 
	 * @param string $wrapperNodeName
	 * @return Base\Web\Action\Response\View\Layout
	 */
	public function load($wrapperNodeName = null)
	{
		if(is_null($wrapperNodeName)){
			$wrapperNodeName = $this->_fallback;
		}
		$this->_output = $this->_getLayoutNode($wrapperNodeName);
		return $this;
	}
	
	/**
	 * Start building the output layout
	 * 
	 * @param string $wrapperNodeName
	 * @return Base\Web\Action\Response\View\Layout
	 */
	public function loadInto($nodeName)
	{
		$this->_output->mergeXml($this->_getLayoutNode($nodeName));
		return $this;
	}
	
	/**
	 * Get a node from the layout of a particular type
	 * 
	 * @param string $nodeName
	 * @return \Base\SimpleXML\Element
	 * @throws \Base\Exception\View
	 */
	protected function _getLayoutNode($nodeName)
	{
		if(!isset($this->_merged->$nodeName)){
			$this->_error("Unknown Layout Node - '{$nodeName}'!");
		}
		return $this->_merged->$nodeName;
	}
	
	/**
	 * Echo out the final HTML, hooray!
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->_view->toHtml();
	}
	
	/**
	 * Use the loaded XML to build the template hierarchy
	 * 
	 * @return Base\Web\Action\Response\View\Layout
	 */
	public function parseLayout()
	{
		//now we parse out the entire hierarchy
		foreach($this->_output->children() as $key=>$xml){
			$this->_parseNode($xml, $this->_view);
		}
		return $this;
	}
	
	/**
	 * Parse the given node of XML to build the template hierarchy
	 * 
	 * @param \Base\SimpleXML\Element $_xml
	 * @param \Base\Web\View\ViewAbstract $parent
	 * @return Base\Web\Action\Response\View\Layout
	 */
	protected function _parseNode(\Base\SimpleXML\Element $_xml, $parent = null)
	{
		switch($_xml->getName()){
			case 'template':
				$this->_parseTemplate($_xml, $parent);
				break;
			case 'reference':
				$this->_parseReference($_xml, $parent);
				break;
			case 'remove':
				$this->_parseRemove($_xml, $parent);
				break;
			case 'action':
				$this->_parseAction($_xml, $parent);
				break;
		}
		
		return $this;
	}
	
	/**
	 * This creates a template block and assigns it to its parent
	 * 
	 * @param \Base\SimpleXML\Element $_xml
	 * @param \Base\Web\View\ViewAbstract $parent
	 * @return \Base\Web\Action\Response\View\Layout
	 * @throws \Base\Exception\View
	 */
	protected function _parseTemplate(\Base\SimpleXML\Element $_xml, $parent)
	{
		//get the template attributes
		$attributes = (array) $_xml->attributes();
		$attributes = $attributes['@attributes'];
		
		//grab the ones we need, and remove them from the data
		$class	= array_key_exists('class', $attributes) ? $attributes['class'] : null;
		$name	= array_key_exists('name', $attributes) ? $attributes['name'] : null;
		unset($attributes['class']);
		
		if(!$name || !$class){
			$this->_error("A template block requires both a name and a class at minimum");
		}
		
		//instantiate the template and give it a parent
		$template = new $class($attributes);
		$template->setParent($parent);
		
		//Congratulations! it's a bouncing baby block!
		$parent->addChild($name, $template);
		
		//give us a way of finding the block for the references
		$this->_templateReference[$name] = $template;
		
		//now we parse the children
		if($_xml->hasChildren()){
			foreach($_xml->children() as $key=>$child){
				$this->_parseNode($child, $template);
			}
		}
		
		return $this;
	}
	
	/**
	 * This modifies blocks by reference
	 * 
	 * @param \Base\SimpleXML\Element $_xml
	 * @param \Base\Web\View\ViewAbstract $parent
	 * @return Base\Web\Action\Response\View\Layout
	 */
	protected function _parseReference(\Base\SimpleXML\Element $_xml, $parent)
	{
		//get the name of the template to reference
		$name = (string) $_xml->attributes()->name;
		if(!$name){
			$this->_error('We need a name to reference from');
		}
		
		//check we actually have that template available
		if(!array_key_exists($name, $this->_templateReference)){
			$this->_error("The template referenced by '$name' has not been instantiated");
		}
		
		//now we can continue to process the directives
		$parent = $this->_templateReference[$name];
		if($_xml->hasChildren()){
			foreach($_xml->children() as $key=>$child){
				$this->_parseNode($child, $parent);
			}
		}
		
		return $this;
	}
	
	/**
	 * This removes a given child from a parent
	 * 
	 * @param \Base\SimpleXML\Element $_xml
	 * @param \Base\Web\View\ViewAbstract $parent
	 * @return Base\Web\Action\Response\View\Layout
	 */
	protected function _parseRemove(\Base\SimpleXML\Element $_xml, $parent)
	{
		//get the name of the template to reference
		$name = (string) $_xml->attributes()->name;
		
		//remove it from the parent block
		$parent->removeChild($name);
		
		//remove it from the reference array
		unset($this->_templateReference[$name]);
		
		return $this;
	}
	
	/**
	 * This calls the given arguments on the parent block
	 * 
	 * @param \Base\SimpleXML\Element $_xml
	 * @param \Base\Web\View\ViewAbstract $parent
	 * @return Base\Web\Action\Response\View\Layout
	 */
	protected function _parseAction(\Base\SimpleXML\Element $_xml, $parent)
	{
		//set up the callback
		$options = (array) $_xml->attributes();
		$options = $options['@attributes'];
		$method = $options['method'];
		unset($options['method']);
		$callback = array($parent, $method);
		
		//check everything is valid
		if(!is_callable($callback)){
			$class = get_class($parent);
			$this->_error("This is not a valid function {$class}::{$method}");
		}
		
		//punch a whale - right in the face
		call_user_func_array($callback, $options);
		return $this;
	}
}
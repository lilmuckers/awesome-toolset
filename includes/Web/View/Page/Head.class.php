<?php
namespace Base\Web\View\Page;

class Head extends \Base\Web\View\Template
{
	/**
	 * Link HTML
	 */
	const LINK_TAG = "<link rel=\"%s\" href=\"%s\" %s>\n  ";
	
	/**
	 * Additional layout tags to handle
	 * 
	 * @var array
	 */
	protected $_handleTags = array(
		'icon',
		'meta'
	);
	
	/**
	 * The arrays of JS to process
	 * 
	 * @var array
	 */
	protected $_mainJs		= array();
	protected $_behaviourJs	= array();
	protected $_libJs		= array();
	
	/**
	 * The arrays of CSS to process
	 * 
	 * @var array
	 */
	protected $_mainCss		= array();

	/**
	 * Add a javascript file to the page
	 * 
	 * @param string $file
	 * @return \Base\Web\View\Page\Head
	 */
	public function addJs($filename)
	{
		$this->_mainJs[] = \Base\Web\View::getSkinFilePath($filename, \Base\Web\View::SKIN_TYPE_JS);
		return $this;
	}
	
	/**
	 * Add a javascript behaviours file to the page
	 * 
	 * @param string $file
	 * @return \Base\Web\View\Page\Head
	 */
	public function addJsBehaviour($filename)
	{
		$this->_behaviourJs[] = \Base\Web\View::getSkinFilePath($filename, \Base\Web\View::SKIN_TYPE_JS);
		return $this;
	}
	
	/**
	 * Add a javascript static library
	 * 
	 * @param string $file
	 * @return \Base\Web\View\Page\Head
	 */
	public function addJsLib($filename)
	{
		$this->_libJs[] = \Base\Web\View::getSkinFilePath($filename, \Base\Web\View::LIB_TYPE_JS);
		return $this;
	}
	
	/**
	 * Setup a meta tag from constituent parts
	 * 
	 * @param array $attributes
	 * @return \Base\Web\View\Page\Head
	 */
	public function addMeta($attributes)
	{
		$meta = new Head\Meta();
		foreach($attributes as $key=>$value){
			$meta->addAttribute($key, $value);
		}
		$this->addChild('meta-'.rand(),$meta);
		return $this;
	}
	
	/**
	 * Att an icon to the file
	 * 
	 * @param mixed $attributes
	 * @return \Base\Web\View\Page\Head
	 */
	public function setIcon($attributes)
	{
		if(!is_array($attributes)){
			$attributes = array('type'=>'shortcut icon', 'location'=>$attributes);
		}
		
		$this->_icon[$attributes['type']][] = '/'.\Base\Web\View::getSkinFilePath($attributes['location'], \Base\Web\View::SKIN_TYPE_IMAGE);
		return $this;
	}
	
	/**
	 * Print out the icon html for all icons, or a given icon
	 * 
	 * @param string $type
	 * @return string
	 */
	public function getIconsHtml($type = null)
	{
		if(!is_null($type)){
			$icons[$type] = $this->_icon[$type];
		} else {
			$icons = $this->_icon;
		}
		
		$out = '';
		foreach($icons as $type=>$locations){
			foreach($locations as $location){
				$out .= sprintf(self::LINK_TAG, $type, $location, '');
			}
		}
		
		return $out;
	}
	
	/**
	 * Add a CSS file to the head
	 * 
	 * @param string $filename
	 * @param string $interface
	 * @return \Base\Web\View\Page\Head
	 */
	public function addCss($filename, $interface = 'screen')
	{
		$this->_mainCss[$interface][] = '/'.\Base\Web\View::getSkinFilePath($filename, \Base\Web\View::SKIN_TYPE_CSS);
		return $this;
	}
	
	/**
	 * Get the embed HTML for the CSS files
	 * 
	 * @return string
	 */
	public function getCssHtml()
	{
		$out = '';
		foreach($this->_mainCss as $type=>$locations){
			foreach($locations as $location){
				$additional = sprintf('media="%s" type="text/css"', $type);
				$out .= sprintf(self::LINK_TAG, 'stylesheet', $location, $additional);
			}
		}
		
		return $out;
	}
	
	/**
	 * Handle the unrecognised tag
	 * 
	 * @param string $name
	 * @param array $attributes
	 * @return \Base\Web\View\Page\Head
	 */
	public function handle($name, $attributes)
	{
		switch($name){
			case 'meta':
				$this->addMeta($attributes);
				break;
			case 'icon':
				$this->setIcon($attributes);
				break;
			default:
				$this->_error("I don't handle this tag - leave me alone!");
				break;
		}
		return $this;
	}
}
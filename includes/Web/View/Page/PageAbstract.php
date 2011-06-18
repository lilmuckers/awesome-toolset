<?php
namespace Base\Web\View\Page;

abstract class PageAbstract extends \Base\Web\View\Template
{
	/**
	 * Pattern for a script tag
	 */
	const SCRIPT_TAG = "<script src=\"%s\"></script>\n  ";
	
	/**
	 * The arrays of JS to process
	 * 
	 * @var array
	 */
	protected $_mainJs		= array();
	protected $_behaviourJs	= array();
	protected $_libJs		= array();

	/**
	 * Add a javascript file to the page
	 * 
	 * @param string $file
	 * @return \Base\Web\View\Page\Head
	 */
	public function addJs($filename)
	{
		$this->_mainJs[] = \Base\Web\View::getSkinUrl($filename, \Base\Web\View::SKIN_TYPE_JS);
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
		$this->_behaviourJs[] = \Base\Web\View::getSkinUrl($filename, \Base\Web\View::SKIN_TYPE_JS);
		return $this;
	}
	
	/**
	 * Add a javascript static library
	 * 
	 * @param string $file
	 * @return \Base\Web\View\Page\Head
	 */
	public function addJsLib($filename, $type = \Base\Web\View::LIB_TYPE_JS)
	{
		$this->_libJs[] = \Base\Web\View::getLibUrl($filename, \Base\Web\View::LIB_TYPE_JS);
		return $this;
	}
	
	/**
	 * Print the JS tags for the lib JS
	 * 
	 * @return string
	 */
	public function getJsHtml()
	{
		$out = '';
		//they're seperated to allow for the libraries to always
		// come before the internal libs, and the behaviours
		foreach($this->_libJs as $js){
			$out .= sprintf(self::SCRIPT_TAG, $js);
		}
		foreach($this->_mainJs as $js){
			$out .= sprintf(self::SCRIPT_TAG, $js);
		}
		foreach($this->_behaviourJs as $js){
			$out .= sprintf(self::SCRIPT_TAG, $js);
		}
		return $out;
	}

}
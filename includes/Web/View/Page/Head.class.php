<?php
namespace Base\Web\View\Page;

class Head extends \Base\Web\View\Template
{
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
}
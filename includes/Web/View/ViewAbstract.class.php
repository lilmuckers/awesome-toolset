<?php
namespace Base\Web\View;

abstract class ViewAbstract extends \Base\Object
{
	
	/**
	 * The default exception type to throw
	 * 
	 * @var string
	 */
	protected $_exceptionClass = '\Base\Exception\View';
	
	/**
	 * Child templates array
	 * 
	 * @var array
	 */
	protected $_children = array();
	
	/**
	 * The layout containing all the instance
	 * 
	 * @var \Base\Web\Action\Response\View\Layout
	 */
	protected $_layout;
	
	/**
	 * Sometimes a block needs to handle additional tags
	 * 
	 * @var array
	 */
	protected $_handleTags = array();
	
	/**
	 * Set the layout object on the template
	 * 
	 * @param \Base\Web\Action\Response\View\Layout
	 * @return \Base\Web\View\ViewAbstract
	 */
	public function setLayout($layout)
	{
		$this->_layout = $layout;
		return $this;
	}
	
	/**
	 * Get the existing children
	 * 
	 * @return array
	 */
	public function getChildren()
	{
		return $this->_children;
	}
	
	/**
	 * Add a child to this template
	 * 
	 * @param string $name
	 * @param \Base\Web\View\ViewAbstract $template
	 * @return \Base\Web\View\ViewAbstract
	 */
	public function addChild($name, \Base\Web\View\ViewAbstract $template)
	{
		$this->_children[$name] = $template;
		return $this;
	}
	
	/**
	 * Get a child of this template
	 * 
	 * @param string $name
	 * @return \Base\Web\View\ViewAbstract
	 * @throws \Base\Exception\View
	 */
	public function getChild($name)
	{
		if(!array_key_exists($name, $this->_children)){
			$this->_error("Invalid child block requested");
		}
		return $this->_children[$name];
	}
	
	/**
	 * Remove a child from the template
	 * 
	 * @param string $name
	 * @return \Base\Web\View\ViewAbstract
	 */
	public function removeChild($name)
	{
		if(!array_key_exists($name, $this->_children)){
			$this->_error("Invalid child block requested");
		}
		unset($this->_children[$name]);
		return $this;
	}
	
	/**
	 * Get a child html of this template
	 * 
	 * @param string $name
	 * @return \Base\Web\View\ViewAbstract
	 * @throws \Base\Exception\View
	 */
	public function getChildHtml($name)
	{
		if(!array_key_exists($name, $this->_children)){
			$this->_error("Invalid child block requested");
		}
		return $this->_children[$name]->toHtml();
	}
	
	/**
	 * Get all children html of this template
	 * 
	 * @param array $names
	 * @return \Base\Web\View\ViewAbstract
	 * @throws \Base\Exception\View
	 */
	public function getChildrenHtml($names = array())
	{
		if(empty($names)){
			$names = array_keys($this->getChildren());
		}
		$return = '';
		foreach($names as $name){
			if(!array_key_exists($name, $this->_children)){
				$this->_error("Invalid child block requested");
			}
			$return .= $this->getChildHtml($name);
		}
		return $return;
	}
	
	/**
	 * Set the parent of this template
	 * 
	 * @param \Base\Web\View\ViewAbstract $template
	 * @return \Base\Web\View\ViewAbstract
	 * @throws \Base\Exception\View
	 */
	public function setParent(\Base\Web\View\ViewAbstract $template)
	{
		$this->_parent = $template;
		return $this;
	}
	
	/**
	 * get the parent of this template
	 * 
	 * @return \Base\Web\View\ViewAbstract
	 * @throws \Base\Exception\View
	 */
	public function getParent()
	{
		return $this->_parent;
	}
	
	/**
	 * Actually convert this template to HTML
	 * 
	 * @return string
	 */
	final public function toHtml()
	{
		$this->_beforeToHtml();
		$html = $this->_toHtml();
		$this->_afterToHtml();
		return $html;
	}
	
	/**
	 * Do any pre-html setup
	 * 
	 * Here is where you would instantiate any data objects and such for use
	 * in the output itself.
	 * 
	 * @return \Base\Web\View\ViewAbstract
	 */
	protected function _beforeToHtml()
	{
		return $this;
	}
	
	/**
	 * Do the actual function that outputs the HTML
	 * 
	 * @return string
	 */
	abstract protected function _toHtml();
	
	/**
	 * Do any post-output cleanup that may be required
	 * 
	 * This is where you'd clear out anything that uses a lot of memory
	 * to keep everything running smoothly.
	 * 
	 * @return \Base\Web\View\ViewAbstract
	 */
	protected function _afterToHtml()
	{
		return $this;
	}
	
	/**
	 * Check if this block can handle the truth
	 * 
	 * @param string $name
	 * @return bool
	 */
	public function canHandle($name)
	{
		return in_array($name, $this->_handleTags);
	}
}
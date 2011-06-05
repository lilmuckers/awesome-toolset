<?php
namespace Base\Web\Action\Response\View\Layout;

class Merged extends \Base\Object
{
	/**
	 * The main xml element to talk about
	 * 
	 * @var \SimpleXMLElement
	 */
	protected $_mainXml;
	
	/**
	 * Add a SimpleXMLElement to the main one
	 * 
	 * @param string $file
	 * @return \Base\Web\Action\Response\View\Layout\Merged
	 */
	public function addFile( $file)
	{
		$file = \Base\Web\View::getLayout($file);
		
		//clean up the file, merging duplicate paths - etc
		$this->_clean($file);
		
		//and then merge it into the file
		if(!$this->_mainXml){
			$this->_mainXml = $file;
		} else {
			$this->_merge($file);
		}
		return $this;
	}
	
	/**
	 * Merge the incoming file into the existing file
	 * 
	 * @param \SimpleXMLElement $file
	 * @return \Base\Web\Action\Response\View\Layout\Merged
	 */
	protected function _merge(\SimpleXMLElement $file)
	{
		
		return $this;
	}
	
	/**
	 * clean the incoming file - merging keys, etc.
	 * 
	 * @param \SimpleXMLElement $file
	 * @return \Base\Web\Action\Response\View\Layout\Merged
	 */
	protected function _clean(\SimpleXMLElement $file)
	{
		
		return $this;
	}
	
	/**
	 * Walk the templates calling __toString() on them all
	 * 
	 * @return string
	 */
	public function render()
	{
		return 'Rendering the view :)';
	}
}
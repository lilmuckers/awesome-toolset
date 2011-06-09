<?php
namespace Base\Web\Action\Response;

class View extends \Base\Object
{
	/**
	 * Various template render options
	 * Used for the different wrapper layouts in default.xml
	 */
	const WRAPPER_NORMAL	= 'default';
	const WRAPPER_AJAX		= 'ajax';

	/**
	 * Render the view layer
	 * 
	 * @param string $wrapper
	 * @return \Base\Web\Action\Response\View\Layout
	 */
	public function render($wrapper = self::WRAPPER_NORMAL, $layouts)
	{
		//we want to merge the layout for this function with the wrapper layout
		$this->_buildLayout($wrapper, $layouts);
		
		return View\Layout::instance();
	}
	
	/**
	 * Build the full layout xml for this page
	 * 
	 * @param string $wrapper
	 * @param array $layoutTag
	 * @return \Base\Web\Action\Response\View
	 */
	protected function _buildLayout($wrapper, $layouts)
	{
		$layout = View\Layout::instance();
		$layout->load($wrapper);
		foreach((array) $layouts as $layoutTag){
			$layout->loadInto($layoutTag);
		}
		
		//load up the blocks etc
		$layout->parseLayout();
		
	}
}
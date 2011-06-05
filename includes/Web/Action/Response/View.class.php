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
	 * @return string
	 */
	public function render($wrapper = self::WRAPPER_NORMAL)
	{
		//we want to merge the layout for this function with the wrapper layout
		$this->_buildLayout($wrapper, $this->getLayout());
		
		return '';
	}
	
	/**
	 * Build the full layout xml for this page
	 * 
	 * @param string $wrapper
	 * @param string $layoutTag
	 * @return \Base\Web\Action\Response\View
	 */
	protected function _buildLayout($wrapper, $layoutTag)
	{
		$layout = View\Layout::instance();
		$layout->load($wrapper);
		$layout->loadInto($layoutTag);
	}
}
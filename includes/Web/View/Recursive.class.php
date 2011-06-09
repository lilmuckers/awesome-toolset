<?php
namespace Base\Web\View;

class Recursive extends ViewAbstract
{
	/**
	 * Print out all children
	 * 
	 * @return string
	 */
	protected function _toHtml()
	{
		return $this->getChildrenHtml();
	}
}
<?php
namespace Base\Web\View;

class Template extends ViewAbstract
{
	/**
	 * Print out the template
	 * 
	 * @return string
	 */
	protected function _toHtml()
	{
		$filename = \Base\Web\View::getTemplateFilePath($this->getTemplate());
		ob_start();
		include($filename);
		$html = ob_get_clean();
		return $html;
	}
}
<?php
namespace Base\Web\View\Page\Head;

class Meta extends \Base\Web\View\ViewAbstract
{
	/**
	 * The meta element attributes to put on the page
	 * 
	 * @var array
	 */
	protected $_attributes;
	
	/**
	 * Format constants for meta tags
	 */
	const META_WRAPPER		= "<meta%s>\n  ";
	const ATTRIBUTE_WRAPPER	= " %s=\"%s\"";
	
	/**
	 * Output the meta tag
	 * 
	 * @return string
	 */
	protected function _toHtml()
	{
		//build the attributes
		$attributes = '';
		foreach($this->_attributes as $attribute=>$value){
			$attributes .= sprintf(self::ATTRIBUTE_WRAPPER, $attribute, $value);
		}
		return sprintf(self::META_WRAPPER, $attributes);
	}
	
	/**
	 * Att an attribute to the meta tag
	 * 
	 * @param string $attribute
	 * @param string $value
	 * @return \Base\Web\View\Page\Head\Meta
	 */
	public function addAttribute($attribute, $value)
	{
		$this->_attributes[$attribute] = str_replace('"', '\"', $value);
		return $this;
	}
}
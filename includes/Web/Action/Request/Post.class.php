<?php
namespace Base\Web\Action\Request;

class Post extends ARequest
{
	/**
	 * Base variable set to use for this object
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct($_POST);
	}
}
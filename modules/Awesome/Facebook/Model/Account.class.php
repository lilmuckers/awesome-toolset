<?php
namespace Awesome\Facebook\Model;

class Account extends \Base\Object
{
	/**
	 * setup the database shiznaz
	 * 
	 * @return void
	 */
	protected function _construct(){
		parent::_construct('facebook_oauth');
	}
}
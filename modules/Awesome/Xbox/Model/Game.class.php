<?php
namespace Awesome\Xbox\Model;

class Game extends \Base\DB\Object
{
	/**
	 * setup the database shiznaz
	 * 
	 * @return void
	 */
	protected function _construct(){
		parent::_construct('game');
	}
}
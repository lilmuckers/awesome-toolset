<?php
namespace Awesome\Xbox\Model\Game;

class Image extends \Base\DB\Object
{
	/**
	 * setup the database shiznaz
	 * 
	 * @return void
	 */
	protected function _construct(){
		parent::_construct('game_image');
	}
}
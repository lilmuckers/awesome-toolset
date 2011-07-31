<?php
namespace Awesome\Xbox\Model\GamerTag\Game;

class Achievement extends \Base\DB\Object
{
	/**
	 * setup the database shiznaz
	 * 
	 * @return void
	 */
	protected function _construct(){
		parent::_construct('gamertag_game_achievement');
	}
}
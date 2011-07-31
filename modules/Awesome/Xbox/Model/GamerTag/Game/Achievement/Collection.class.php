<?php
namespace Awesome\Xbox\Model\GamerTag\Game\Achievement;

class Collection extends \Base\DB\Collection
{
	/**
	 * Configure the base of this collection
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct('gamertag_game_achievement', '\Awesome\Xbox\Model\GamerTag\Game\Achievement');
	}
}
<?php
namespace Awesome\Xbox\Model\GamerTag\Game;

class Collection extends \Base\DB\Collection
{
	/**
	 * Configure the base of this collection
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct('gamertag_game', '\Awesome\Xbox\Model\GamerTag\Game');
	}
}
<?php
namespace Awesome\Xbox\Model\Game\Achievement;

class Collection extends \Base\DB\Collection
{
	/**
	 * Configure the base of this collection
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct('game_achievement', '\Awesome\Xbox\Model\Game\Achievement');
	}
}
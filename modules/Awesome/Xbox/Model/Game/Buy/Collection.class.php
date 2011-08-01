<?php
namespace Awesome\Xbox\Model\Game\Buy;

class Collection extends \Base\DB\Collection
{
	/**
	 * Configure the base of this collection
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct('game_buy', '\Awesome\Xbox\Model\Game\Buy');
	}
}
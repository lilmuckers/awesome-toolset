<?php
namespace Awesome\Xbox\Model\Game;

class Collection extends \Base\DB\Collection
{
	/**
	 * Configure the base of this collection
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct('game', '\Awesome\Xbox\Model\Game');
	}
}
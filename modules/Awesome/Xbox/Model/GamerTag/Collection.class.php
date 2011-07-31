<?php
namespace Awesome\Xbox\Model\GamerTag;

class Collection extends \Base\DB\Collection
{
	/**
	 * Configure the base of this collection
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct('gamertag', '\Awesome\Xbox\Model\GamerTag');
	}
}
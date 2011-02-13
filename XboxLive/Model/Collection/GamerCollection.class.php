<?php

class GamerCollection extends BaseDBCollection
{
	/**
	 * Configure the base of this collection
	 * 
	 * Additionally; default ordering is applied here
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct('gamertag', 'Gamer');
	}
	
	/**
	 * Afterload - load up the achievement data for the game
	 * 
	 * @return GamerCollection
	 */
	protected function _afterLoad()
	{
		//$this->walk('loadGames');
		return parent::_afterLoad();
	}
}
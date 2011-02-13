<?php

class GameCollection extends BaseDBCollection
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
		$this->setOrder('gamertag_id');
		$this->setOrder('last_played');
		$this->setOrder('id');
		parent::_construct('game', 'Game');
	}
	
	/**
	 * Afterload - load up the achievement data for the game
	 * 
	 * @return GameCollection
	 */
	protected function _afterLoad()
	{
		//$this->walk('loadAchievements');
		return parent::_afterLoad();
	}
}
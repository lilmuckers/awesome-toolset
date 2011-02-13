<?php

class AchievementCollection extends BaseDBCollection
{
	/**
	 * Configure the base of this collection
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		$this->setOrder('gamertag_id');
		$this->setOrder('game_id');
		$this->setOrder('acquired');
		$this->setOrder('id', 'ASC');
		parent::_construct('achievement', 'Achievement');
	}
	
	/**
	 * Afterload - load up the parent data for the achievement
	 * 
	 * @return AchievementCollection
	 */
	protected function _afterLoad()
	{
		//$this->walk('loadGamer');
		//$this->walk('loadGame');
		return parent::_afterLoad();
	}
}
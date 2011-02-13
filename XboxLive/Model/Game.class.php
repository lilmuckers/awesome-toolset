<?php

class Game extends BaseDBObject
{
	/**
	 * setup the database shiznaz
	 * 
	 * @return void
	 */
	protected function _construct(){
		parent::_construct('game');
	}
	
	/**
	 * Load up the associated achievements
	 * 
	 * @return Game
	 */
	protected function _afterLoad()
	{
		$this->loadAchievements();
		$this->loadGamer();
		return parent::_afterLoad();
	}
	
	/**
	 * Load up the associated achievements
	 * 
	 * @return Game
	 */
	public function loadAchievements()
	{
		if(!$this->getAchievements()){
			$achievements = new AchievementCollection();
			$achievements->addFilter('game_id', array('eq'=>$this->getData('id')))->load();;
			$achievements->walk('setGame', array($this));
			$achievements->walk('setGamer', array($this->getGamer()));
			$this->setAchievements($achievements);
		}
		return $this;
	}
	
	/**
	 * Load up the gamer data
	 * 
	 * @return Game
	 */
	public function loadGamer()
	{
		if(!$this->getGamer()){
			$gamer = new Gamer();
			$gamer->load($this->getGamertagId(), 'id');
			$this->setGamer($gamer);
		}
		return $this->getGamer();
	}
}
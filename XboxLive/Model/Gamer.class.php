<?php

class Gamer extends BaseDBObject
{
	/**
	 * setup the database shiznaz
	 * 
	 * @return void
	 */
	protected function _construct(){
		parent::_construct('gamertag', 'gamertag');
	}
	
	/**
	 * Load all the games - collection
	 * 
	 * @return void
	 */
	protected function _afterLoad(){
		$this->loadGames();
		parent::_afterLoad();
	}
	
	/**
	 * Load the games for this gamer
	 * 
	 * return Gamer
	 */
	public function loadGames()
	{
		//add games
		if(!$this->getGames()){
			$games = new GameCollection();
			$games->addFilter('gamertag_id', array('eq'=>$this->getData('id')));
			$games->load();
			$games->walk('setGamer', array($this));
			$this->setGames($games);
		}
		return $this;
	}
}
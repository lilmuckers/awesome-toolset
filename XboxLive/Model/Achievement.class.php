<?php

class Achievement extends BaseDBObject
{
	/**
	 * setup the database shiznaz
	 * 
	 * @return void
	 */
	protected function _construct(){
		parent::_construct('achievement');
	}
	
	/**
	 * Load up the associated parents
	 * 
	 * @return Achievement
	 */
	protected function _afterLoad()
	{
		$this->loadGamer();
		$this->loadGame();
		return parent::_afterLoad();
	}
	
	/**
	 * Load up the gamer data
	 * 
	 * @return Gamer
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
	
	/**
	 * Load up the game data
	 * 
	 * @return Game
	 */
	public function loadGame()
	{
		if(!$this->getGame()){
			$game = new Game();
			$game->load($this->getGameId());
			$this->setGame($game);
		}
		return $this->getGame();
	}
}
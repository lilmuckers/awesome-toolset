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
	 * Format Foreign keys
	 * 
	 * @return Achievement
	 */
	protected function _beforeSave()
	{
		$this->setData('gamertag_id', $this->getGamer()->getId());
		$this->setData('game_id', $this->getGame()->getId());
		
		//only want to save if it's new
		if($this->getId()){
			$this->setFlag('save', false);
		}
		return parent::_beforeSave();
	}
	
	/**
	 * Load up the gamer data
	 * 
	 * @return Gamer
	 */
	public function getGamer()
	{
		if(!$this->hasGamer()){
			$gamer = new Gamer();
			$gamer->load($this->getGamertagId());
			$this->setGamer($gamer);
		}
		return $this->getData('gamer');
	}
	
	/**
	 * Load up the game data
	 * 
	 * @return Game
	 */
	public function getGame()
	{
		if(!$this->hasGame()){
			$game = new Game();
			$game->load($this->getGameId());
			$this->setGame($game);
		}
		return $this->getData('game');
	}
}
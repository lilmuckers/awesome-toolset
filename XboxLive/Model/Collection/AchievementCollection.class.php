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
		$this->setOrder('acquired');
		$this->setOrder('id', 'ASC');
		parent::_construct('achievement', 'Achievement');
	}
	
	/**
	 * After collection load, give the achievements their games
	 * 
	 * @return AchievementCollection
	 */
	protected function _afterLoad()
	{
		//give the children their game!
		if($game = $this->getGame()){
			$this->walk('setGame', array($game));
		} else {
			$gameIds = $this->getColumnValues('game_id');
			$games = new GameCollection();
			$games->addFilter('id', array('in'=>$gameIds))
				->setGamer($this->getGamer())
				->load();
			foreach($this as $item){
				$item->setGame($games->getItemByColumn('id', $item->getGameId()));
			}
			
			//store the collection for later use
			$this->setGames($games);
		}
		
		//give the children their gamer
		if($gamer = $this->getGamer()){
			$this->walk('setGamer', array($gamer));
		} else {
			$gamerIds = $this->getColumnValues('gamertag_id');
		
			$gamers = new GamerCollection();
			$gamers->addFilter('id', array('in'=>$gamerIds))
				->load();
			foreach($this as $item){
				$item->setGamer($gamers->getItemByColumn('id', $item->getGamerId()));
			}
			
			//store the gamers for later use
			$this->setGamers($gamers);
		}
		return $this;
	}
}
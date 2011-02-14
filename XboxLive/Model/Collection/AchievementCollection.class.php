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
	 * After collection load, give the achievements their games
	 * 
	 * @return AchievementCollection
	 */
	protected function _afterLoad()
	{
		//give the children their game!
		if($game = $this->getGame()){
			$this->walk('setGame', $game);
		} else {
			$gameIds = $this->getColumnValues('game_id');
			$games = new GameCollection();
			$games->addFilter('id', array('in'=>$gameIds))
				->setGamer($this->getGamer())
				->load();
			foreach($this->_items as $item){
				$item->setGame($games->getItemByColumn('id', $item->getGameId()));
			}
		}
		
		//give the children their gamer
		if($gamer = $this->getGamer()){
			$this->walk('setGamer', $gamer);
		} else {
			$gamerIds = $this->getColumnValues('gamertag_id');
		
			$gamers = new GamerCollection();
			$gamers->addFilter('id', array('in'=>$gamerIds))
				->load();
			foreach($this->_items as $item){
				$item->setGamer($gamers->getItemByColumn('id', $item->getGamerId()));
			}
		}
		return $this;
	}
}
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
		$this->setOrder('id', 'DESC');
		parent::_construct('game', 'Game');
	}
	
	/**
	 * After collection load, give the games their gamers
	 * 
	 * @return GameCollection
	 */
	protected function _afterLoad()
	{
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
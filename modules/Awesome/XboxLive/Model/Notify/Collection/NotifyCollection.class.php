<?php

class NotifyCollection extends BaseDBCollection
{
	/**
	 * Configure the base of this collection
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct('gamer_notify', array('Notify', 'getClass'));
	}
	
	/**
	 * After collection load, give the notify objects their gamers
	 * 
	 * @return NotifyCollection
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
			foreach($this->_items as $item){
				$item->setGamer($gamers->getItemByColumn('id', $item->getGamerId()));
			}
		}
		return $this;
	}
}
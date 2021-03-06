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
	 * Setup the saving
	 * 
	 * @return Gamer
	 */
	protected function _beforeSave()
	{
		//format the foreign keys
		$this->setGamertagId($this->getGamer()->getId());
		
		//only want to save if it's been updated
		$this->setFlag('save', $this->getFlag('updated'));
		
		return parent::_beforeSave();
	}
	
	/**
	 * Save the achievements after the game has saved - because they rely on the foreign key of the game_id
	 * 
	 * @return Gamer
	 */
	protected function _afterSave()
	{
		//save all the achievements
		$this->getAchievementCollection()->walk('save');
		
		return parent::_afterSave();
	}
	
	/**
	 * Load up the associated achievements
	 * 
	 * @return AchievementCollection
	 */
	public function getAchievementCollection()
	{
		if(!$this->hasAchievementCollection()){
			$achievements = new AchievementCollection();
			if($this->hasData('id')){
				$achievements->addFilter('game_id', array('eq'=>$this->getId()))
					->setGame($this)
					->setGamer($this->getGamer())
					->load();
			}
			$this->setAchievementCollection($achievements);
		}
		return $this->getData('achievement_collection');
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
	 * Update the game from the scraper - this might not need doing
	 * 
	 * @param string $file To load achievements from a file
	 * @return Game
	 */
	public function update($file = null)
	{
		//update the object if applicable
		if($this->_hasUpdated() || $this->getFlag('forced')){
			if(is_null($file)){
				$this->setData($this->getUpdate()->getData());
			}
			$this->setFlag('updated', true);
			
			//now we worry about the achievements.
			$achievements = new AchievementScrape();
			$achievements->setGamer($this->getGamer())
				->setGame($this)
				->load($file);
		}
		
		return $this;
	}
	
	/**
	 * Check if the game has been updated since last check
	 * 
	 * @return bool
	 */
	protected function _hasUpdated()
	{
		//make sure we actually have an update to check against
		if($this->hasUpdate()){
			//check if the scraped score is larger than the saved score
			return $this->getUpdate()->getScore() > $this->getScore();
		}
		return false;
	}
	
	/**
	 * Flag for the forced update
	 * 
	 * @return Game
	 */
	public function force()
	{
		$this->setFlag('forced', true);
		return $this;
	}
	
	/**
	 * Before delete we delete all the achievements
	 * 
	 * @return Game
	 */
	protected function _beforeDelete()
	{
		$this->getAchievementCollection()->walk('delete');
		return parent::_beforeDelete();
	}
}
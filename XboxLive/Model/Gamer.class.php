<?php

class Gamer extends BaseDBObject
{
	/**
	 * Keep the login info delimited by this string
	 */
	const LOGIN_JOINER = '###';

	/**
	 * setup the database shiznaz
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		//setup last checked to be auto-populated
		$this->_autoUpdateFields['last_checked'] = '_dateTime';
		
		//setup last login field to be auto-encrypted
		$this->_autoUpdateFields['login_data'] = '_encryptLogin';
		
		parent::_construct('gamertag');
	}
	
	/**
	 * Encrypt the live data before save
	 * 
	 * @return Gamer
	 */
	protected function _beforeSave()
	{
		//save games
		$this->getGames()->walk('save');
		
		//only want to save if it's been updated
		if(!$this->getFlag('updated') && $this->hasId()){
			$this->setFlag('save', false);
		}
		return parent::_beforeSave();
	}
	
	/**
	 * Load all the games - collection, also decrypt the live data
	 * 
	 * @return Gamer
	 */
	protected function _afterLoad()
	{
		$this->decryptLogin();
		return parent::_afterLoad();
	}
	
	/**
	 * Force the login data to be decrypted
	 * 
	 * @return Gamer
	 */
	public function decryptLogin()
	{
		$loginData = Mcrypt::out($this->getData('login_data'));
		$this->setData('login_data', $loginData);
		return $this;
	}
	
	/**
	 * Auto-encrypt the login fields
	 * 
	 * @return string
	 */
	protected function _encryptLogin()
	{
		$loginData = Mcrypt::in($this->getData('login_data'));
		return $loginData;
	}
	
	/**
	 * Load the games for this gamer
	 * 
	 * return GameCollection
	 */
	public function getGames()
	{
		//add games
		if(!$this->hasGames()){
			$games = new GameCollection();
			if($this->hasData('id')){
				$games->addFilter('gamertag_id', array('eq'=>$this->getId()));
				$games->setGamer($this);
				$games->load();
			}
			$this->setGames($games);
		}
		return $this->getData('games');
	}
	
	/**
	 * Load up the associated achievements
	 * 
	 * @return AchievementCollection
	 */
	public function getAchievements()
	{
		if(!$this->hasAchievements()){
			$achievements = new AchievementCollection();
			$achievements->addFilter('gamertag_id', array('eq'=>$this->getId()))
				->setGamer($this)
				->load();
			$this->setAchievements($achievements);
		}
		return $this->getData('achievements');
	}
	
	/**
	 * Set the xbox-live login data
	 * 
	 * @param string $email
	 * @param string $password
	 * @return Gamer
	 */
	public function setLoginData($email, $password)
	{
		return $this->setData('login_data', $email.self::LOGIN_JOINER.$password);
	}
	
	/**
	 * Get array of login data for xbox live
	 * 
	 * @return array
	 */
	public function getLoginData()
	{
		return explode(self::LOGIN_JOINER, $this->getData('login_data'));
	}
	
	/**
	 * Check for updates and pull them if they exist
	 * 
	 * @return Gamer
	 */
	public function update()
	{
		//scrape the new data from the gamercard
		$external = new GamerScrape();
		$external->setGamer($this)->load();
		
		//keep this floating in memory for other use
		$this->setUpdate($external);
		
		//check if anything has been updated before setting the new data
		if($this->_hasUpdated() || $this->getFlag('forced')){
			$this->setData($external->getData());
			$this->setFlag('updated', true);
			
			//figure out which games have updated
			$games = new GameScrape();
			$games->setGamer($this)->load();
			
			if($this->getFlag('forced')){
				$this->getGames()->walk('force');
			}
			$this->getGames()->walk('update');
		}
		
		return $this;
	}
	
	/**
	 * Check if the gamer has been updated since last check
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
	 * Before delete we delete all the games
	 * 
	 * @return Gamer
	 */
	protected function _beforeDelete()
	{
		$this->getGames()->walk('delete');
		return parent::_beforeDelete();
	}
}
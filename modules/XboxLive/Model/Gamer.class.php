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
		return Mcrypt::in($this->getData('login_data'));
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
				->setGamer($this);
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
	
	/**
	 * Get all the notification methods
	 * 
	 * @return NotifyCollection
	 */
	public function getNotifications()
	{
		if(!$this->hasNotifications()){
			$notifications = new NotifyCollection();
			if($this->hasData('id')){
				$notifications->addFilter('gamertag_id', array('eq'=>$this->getId()));
				$notifications->setGamer($this);
			}
			$this->setNotifications($notifications);
		}
		return $this->getData('notifications');
	}
	
	/**
	 * Notify the world through the defined locations of our achievements
	 * 
	 * @param string $timescale
	 * @return Gamer
	 */
	public function notify($timescale = "-1 hour")
	{
		$since = date("Y-m-d H:i:s", strtotime($timescale));
		$achievements = $this->getAchievements()
			->addFilter('acquired', array('gt'=>$since))
			->load();
			
		if($achievements->count() == 1) {
			//if there's only one achievement - we post that.
			$template = "I just earned '%s' (%uG) in '%s'. My gamerscore is now %uG";
			$achievement = $achievements->getFirstItem();
			
			$message = sprintf($template, $achievement->getName(), $achievement->getScore(), $achievement->getGame()->getName(), $this->getScore());
		} elseif($achievements->getGames()->count() == 1) {
			//if there's more than one achievement, but only one game - we post that
			$template = "I just earned %u achievements (%uG) in %s. My gamerscore is now %uG";
			
			$message = sprintf($template, $achievements->count(), $achievements->sumColumn('score'), $achievements->getFirstItem()->getGame()->getName(), $this->getScore());
		} elseif($achievements->count() > 1) {
			//if there's multiple achievements and multiple games, we go a brief overview
			$template = "I just earned %u achievements (%uG). My gamerscore is now %uG";
			
			$message = sprintf($template, $achievements->count(), $achievements->sumColumn('score'), $this->getScore());
		} else {
			return $this;
		}
		
		//send all the notifications
		$this->getNotifications()->walk('send', array($message));
		
		return $this;
	}
}
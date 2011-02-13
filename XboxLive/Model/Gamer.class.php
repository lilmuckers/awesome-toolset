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
		parent::_construct('gamertag', 'gamertag');
	}
	
	/**
	 * Encrypt the live data before save
	 * 
	 * @return Gamer
	 */
	protected function _beforeSave()
	{
		//encrypt the Xbox Live login data - keep it secret, keep it safe.
		$loginData = Mcrypt::in($this->getData('login_data'));
		$this->setData('login_data', $loginData);
		
		//save games
		
		return parent::_beforeSave();
	}
	
	/**
	 * Load all the games - collection, also decrypt the live data
	 * 
	 * @return Gamer
	 */
	protected function _afterLoad()
	{
		$loginData = Mcrypt::out($this->getData('login_data'));
		$this->setData('login_data', $loginData);
		$this->loadGames();
		return parent::_afterLoad();
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
	 * @return XboxLive
	 */
	public function update()
	{
	
	}
}
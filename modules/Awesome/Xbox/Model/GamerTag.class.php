<?php
namespace Awesome\Xbox\Model;

class GamerTag extends \Base\DB\Object
{
	/**
	 * The join constant
	 */
	const LOGIN_JOINER = ':-:';
	
	/**
	 * setup the database shiznaz
	 * 
	 * @return void
	 */
	protected function _construct(){
		//setup last login field to be auto-encrypted
		$this->_autoUpdateFields['login_data'] = '_encryptLogin';
		parent::_construct('gamertag');
	}
	
	/**
	 * Set the xbox-live login data
	 * 
	 * @param string $email
	 * @param string $password
	 * @return \Awesome\Xbox\Model\GamerTag
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
	 * Force the login data to be decrypted
	 * 
	 * @return \Awesome\Xbox\Model\GamerTag
	 */
	protected function _decryptLogin()
	{
		$loginData = \Base\Mcrypt::out($this->getData('login_data'));
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
		return \Base\Mcrypt::in($this->getData('login_data'));
	}
	
	/**
	 * Load all the games - collection, also decrypt the live data
	 * 
	 * @return \Awesome\Xbox\Model\GamerTag
	 */
	protected function _afterLoad()
	{
		$this->_decryptLogin();
		return parent::_afterLoad();
	}
	
	/**
	 * Update the data from the api feed
	 * 
	 * @return \Awesome\Xbox\Model\GamerTag
	 */
	public function update()
	{
		$api = new \Awesome\Xbox\Model\Api\GamerTag();
		if($api->load($this->getGamertag())){
			//picture to load
			$picture = $api->getPicture();
			$this->setPicture($picture['large']);
			
			//set the other data
			$this->setAvatar($api->getAvatar());
			$this->setActivity($api->getActivity());
			$this->setLocation($api->getLocation());
			$this->setMotto($api->getMotto());
			$this->setBio($api->getBio());
			
			//Check to see if the user has new score or something.
			if($this->getScore() && $this->getScore() < $api->getScore()) {
				//there has been a change! Shit!
				//Check the games downloaded in the gamertag feed first and see if that makes up the difference
					//if it doesn't, then we need to check the games api
				
				//load up the achievement data for the updated games
				
				//save everything
				
				$this->setScore($api->getScore());
			}
			
		}
		return $this;
	}
}
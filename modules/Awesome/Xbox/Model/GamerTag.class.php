<?php
namespace Awesome\Xbox\Model;

class GamerTag extends \Base\DB\Object
{
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
	 * Load all the games - collection, also decrypt the live data
	 * 
	 * @return \Awesome\Xbox\Model\GamerTag
	 */
	protected function _afterLoad()
	{
		$this->_decryptLogin();
		return parent::_afterLoad();
	}
}
<?php

class GamerCollection extends BaseDBCollection
{
	/**
	 * Configure the base of this collection
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct('gamertag', 'Gamer');
	}
	
	/**
	 * Force decryption of all the login details
	 * 
	 * @return GamerCollection
	 */
	protected function _afterLoad()
	{
		$this->walk('decryptLogin');
		return parent::_afterLoad();
	}
}
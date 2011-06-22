<?php
namespace Awesome\Facebook\Model\Graph;

class Profile extends GraphAbstract
{
	/** 
	 * Api endpoints
	 */
	const USER_INFORMATION_ENDPOINT = "%s";
	const USER_FRIENDSLIST_ENDPOINT = "%s/friends";
	
	/**
	 * Load the profile data
	 * 
	 * @return \Awesome\Facebook\Model\Graph\Profile
	 */
	public function load()
	{
		$loadUrl = sprintf(self::USER_INFORMATION_ENDPOINT, $this->getId());
		$this->setData(array('user_info'=>$this->_request($loadUrl)));
		return $this;
	}
	
	/**
	 * Get the users friends list
	 * 
	 * @return \Awesome\Facebook\Model\Graph\Profile\Collection
	 */
	public function getFriends()
	{
		if(!$this->hasFriends()){
			$collection = new Profile\Collection();
			$loadUrl = sprintf(self::USER_FRIENDSLIST_ENDPOINT, $this->getId());
			foreach($this->_request($loadUrl)->data as $friend){
				$friend = new self($friend);
				$friend->setOAuth($this->_oAuth);
				$collection->addItem($friend);
			}
			$this->setFriends($collection);
		}
		return $this->getData('friends');
	}
	
	/**
	 * Get the users wall entity
	 * 
	 * @return \Awesome\Facebook\Model\Graph\Profile\Wall
	 */
	public function getWall()
	{
		if(!$this->hasWall()){
			$wall = $this->_initiateGraphObject("\Awesome\Facebook\Model\Graph\Profile\Wall");
			$wall->setProfileId($this->getId());
			$this->setWall($wall);
		}
		return $this->getData('wall');
	}
}
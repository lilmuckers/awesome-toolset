<?php

class TwitterNotify extends Notify
{
	/**
	 * The type code
	 * 
	 * @var string
	 */
	protected $_typeCode = 'twitter';
	
	/**
	 * Load the notification type
	 * 
	 * @param string $identifier
	 * @return TwitterNotify
	 */
	public function loadNotification($identifier)
	{
		$notify = new TwitterAccount();
		$notify->load($identifier, 'username');
		$this->setNotifier($notify);
		return $this;
	}
	
	/**
	 * Send the notification
	 * 
	 * @param string $message
	 * @return TwitterNotify
	 */
	protected function _send($message)
	{
		$account = new TwitterAccount();
		$account->load($this->getId());
		$account->tweet($message);
		return $this;
	}
}
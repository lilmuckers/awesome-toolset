<?php

class Twitter extends BaseController
{
	/**
	 * Add a user to the DB
	 * 
	 * @return Twitter
	 */
	public function add()
	{
		//request a new token
		$oAuth = new TwitterOAuth();
		
		//get the URL to go to
		$url = $oAuth->getAuthUrl();
		
		//print to the world
		$this->_write("Please visit the following URL and authorise access for this program.\n");
		$this->_write("\n{$url}\n\n", 'yellow');
		$accessCode = $this->_input("Please enter the access code: ");
		
		//import to the account information
		$accessToken = $oAuth->getAccessToken();
		$account = new TwitterAccount();
		$account->import($accessToken);
		$account->save();
		
		return $this;
	}
	
	/**
	 * Post a tweet
	 * 
	 * @param string $account
	 * @param string $tweet
	 * @return Twitter
	 */
	public function tweet($account, $tweet)
	{
		if(strlen($tweet) > 140){
			$this->_write("[ERROR] Tweet is too long!\n", 'red');
			return $this;
		}
		
		$twitter = new TwitterAccount();
		$twitter->load($account, 'username');
		
		try{
			$twitter->tweet($tweet);
		} catch(BaseOAuthException $e) {
			$this->_write("[ERROR] Access denied for user account. Perhaps the app has been disallowed\n", 'red');
		}
		return $this;
	}
}
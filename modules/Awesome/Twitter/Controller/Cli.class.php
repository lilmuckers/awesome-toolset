<?php
namespace Awesome\Twitter\Controller;

class Cli extends \Base\Cli\Controller
{
	/**
	 * Namespace to work in
	 * 
	 * @var string
	 */
	protected $_moduleNamespace = 'Awesome\Twitter';
	
	/**
	 * Add a user to the DB
	 * 
	 * @return \Twitter\Cli
	 */
	public function add()
	{
		//request a new token
		$oAuth = new \Awesome\Twitter\Model\OAuth();
		
		//get the URL to go to
		$url = $oAuth->getAuthUrl();
		
		//print to the world
		$this->_write("Please visit the following URL and authorise access for this program.\n");
		$this->_write("\n{$url}\n\n", 'yellow');
		$accessCode = $this->_input("Please enter the access code: ");
		
		//import to the account information
		$accessToken = $oAuth->getAccessToken();
		$account = new \Awesome\Twitter\Model\Account();
		$account->import($accessToken);
		$account->save();
		
		$this->_write("\n\nAdded new twitter user account for {$account->getUsername()}\n", 'green');
		
		return $this;
	}
	
	/**
	 * Post a tweet
	 * 
	 * @param string $account
	 * @param string $tweet
	 * @return \Twitter\Cli
	 */
	public function tweet($account, $tweet)
	{
		if(strlen($tweet) > 140){
			$this->_write("[ERROR] Tweet is too long (".strlen($tweet)." characters)!\n", 'red');
			return $this;
		}
		
		$twitter = new \Awesome\Twitter\Model\Account();
		$twitter->load($account, 'username');
		
		try{
			$twitter->tweet($tweet);
		} catch(\Base\Exception\OAuth $e) {
			$this->_write("[ERROR] Access denied for user account. Perhaps the app has been disallowed\n", 'red');
		}
		return $this;
	}
}
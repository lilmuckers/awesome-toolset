<?php
namespace Awesome\Xbox\Controller;

class Cli extends \Base\Cli\Controller
{
	/**
	 * Namespace to work in
	 * 
	 * @var string
	 */
	protected $_moduleNamespace = 'Awesome\Xbox';
	
	/**
	 * Add a user to the DB
	 * 
	 * @param string gamertag
	 * @param string $email
	 * @param string $password
	 * @return \Awesome\Xbox\Controller\Cli
	 */
	public function add($username, $email = null, $password = null)
	{
		if(is_null($email)){
			$email = $this->_input("Enter Login Email: ");
		}
		if(is_null($password)){
			$password = $this->_silentInput("Enter Password: ");
		}
		$gamertag = new \Awesome\Xbox\Model\GamerTag();
		try{
			$gamertag->load($username, 'gamertag');
			$this->_error('This Gamertag already exists', 0, $e);
		} catch(\Base\Exception\DB $e) {
		}
		
		//set up the gamertag, with login data, and run the update.
		$gamertag->setGamertag($username);
		$gamertag->setLoginData($email, $password);
		$gamertag->update();
		$gamertag->save();
		
		$this->_write('Success!');
		return$this;
	}
}
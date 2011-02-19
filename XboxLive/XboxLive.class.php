<?php

class XboxLive extends BaseController
{
	/**
	 * Update all existing gamertag information
	 * 
	 * @param string $gamertag
	 * @return XboxLive
	 */
	public function update($gamertag = null)
	{
		if(!is_null($gamertag)){
			$gamer = new Gamer();
			$gamer->load($gamertag, 'gamertag');
			$gamer->update();
			$gamer->save();
			return $this;
		}
		$gamers = new GamerCollection();
		$gamers->load();
		$gamers->walk('update');
		$gamers->walk('save');
		return $this;
	}
	
	/**
	 * Add a new tag to the system
	 * 
	 * @param string $gamertag
	 * @param string $liveId
	 * @param string $livePassword
	 * @return XboxLive
	 */
	public function add($gamertag, $liveId = null, $livePassword = null)
	{
		$gamer = new Gamer();
		$gamer->setGamertag($gamertag);
		
		//confirm the liveID
		if(is_null($liveId)){
			$liveId = $this->_input("Please enter your xbox live id: [eg: username@live.co.uk] ");
		}
		
		//get the password
		if(is_null($livePassword)){
			$livePassword = $this->_silentInput("Please enter your xbox live password: ");
		}
		
		$gamer->setLoginData($liveId, $livePassword);
		$gamer->save();
		$gamer->update()
			->save();
		return $this;
	}
	
	/**
	 * Modify the login details for a given gamertag
	 * 
	 * @param string $gamertag
	 * @param string $liveId
	 * @param string $livePassword
	 * @return XboxLive
	 */
	public function edit($gamertag, $liveId = null, $livePassword = null)
	{
		$gamer = new Gamer();
		$gamer->load($gamertag, 'gamertag');
		
		//confirm the liveID
		if(is_null($liveId)){
			$liveId = $this->_input("Please enter your xbox live id: [eg: username@live.co.uk] ");
		}
		
		//get the password
		if(is_null($livePassword)){
			$livePassword = $this->_silentInput("Please enter your xbox live password: ");
		}
		$gamer->setLoginData($liveId, $livePassword);
		$gamer->setFlag('updated', true);
		$gamer->save();
		return $this;
	}
	
	/**
	 * Check the consistency of the DB
	 * 
	 * @param string $gamertag
	 * @return XboxLive
	 */
	public function check($gamertag = null)
	{
		if(!is_null($gamertag)){
			$gamer = new Gamer();
			$gamer->load($gamertag, 'gamertag');
			$report = new ConsistencyReport();
			$report->setGamer($gamer)
				->calculate();
			fwrite(STDOUT, $report);
		} else {
			$gamers = new GamerCollection();
			$gamers->load();
			foreach($gamers as $gamer){
				$report = new ConsistencyReport();
				$report->setGamer($gamer)
					->calculate();
				fwrite(STDOUT, $report);
			}
		}
	}
	
	/**
	 * Force a complete update of the gamertag
	 * 
	 * @param string $gamertag
	 * @return XboxLive
	 */
	public function force($gamertag = null)
	{
		$gamer = new Gamer();
		$gamer->load($gamertag, 'gamertag');
		$gamer->setFlag('forced', true);
		$gamer->update();
		$gamer->save();
		return $this;
	}
	
	/**
	 * Delete a given gamertag
	 * 
	 * @param string $gamertag
	 * @return XboxLive
	 */
	public function delete($gamertag)
	{
		if($this->_confirm(sprintf("Are you sure you want to delete gamertag '%s'?\nThis will delete all game and achievement data as well.", $gamertag))){
			$gamer = new Gamer();
			$gamer->load($gamertag, 'gamertag');
			$gamer->delete();
		}
		return $this;
	}
}
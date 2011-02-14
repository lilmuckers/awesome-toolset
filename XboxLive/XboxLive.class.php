<?php

class XboxLive extends BaseController
{
	/**
	 * Update all existing gamertag information
	 * 
	 * @return XboxLive
	 */
	public function update()
	{
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
	 * @param string $livePassport
	 * @return XboxLive
	 */
	public function add($gamertag, $liveId, $livePassport)
	{
		$gamer = new Gamer();
		$gamer->setGamertag($gamertag);
		$gamer->setLoginData($liveId, $livePassport);
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
	 * @param string $livePassport
	 * @return XboxLive
	 */
	public function edit($gamertag, $liveId, $livePassport)
	{
		$gamer = new Gamer();
		$gamer->load($gamertag, 'gamertag');
		$gamer->setLoginData($liveId, $livePassport);
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
			print $report;
		} else {
			$gamers = new GamerCollection();
			$gamers->load();
			foreach($gamers as $gamer){
				$report = new ConsistencyReport();
				$report->setGamer($gamer)
					->calculate();
				print $report;
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
		
		return $this;
	}
}
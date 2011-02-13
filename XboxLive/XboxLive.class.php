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
	 * @return XboxLive
	 */
	public function add($gamertag, $liveId, $livePassport)
	{
		$gamer = new Gamer();
		$gamer->setGamertag($gamertag);
		$gamer->setLoginData($liveId, $livePassport);
		$gamer->update()->save();
		return $this;
	}
}
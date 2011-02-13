<?php

class ConsistencyReport extends BaseObject
{
	/**
	 * Run the calculations
	 * 
	 * @return ConsistencyReport
	 */
	public function calculate()
	{
		$this->_scoreVsGametotal()
			->_gameScoreVsAchievementTotal()
		return $this;
	}
	
	/**
	 * Check the consistency of the DB
	 * 
	 * @return Gamer
	 */
	public function _scoreVsGametotal()
	{
		//check games against gamertag
		$this->setGamesSum($this->getGamer()->getGames()->sumColumn('score'));
		$this->setGamerRecord($this->getGamer()->getScore());
		
		
		
		return $this;
	}
	
	/**
	 * Return this in a nice format for the command line
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return 'ARGH';
	}
}
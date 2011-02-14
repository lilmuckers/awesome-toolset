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
			->_gameScoreVsAchievementTotal();
		return $this;
	}
	
	/**
	 * Check the consistency of the DB
	 * 
	 * @return ConsistencyReport
	 */
	protected function _scoreVsGametotal()
	{
		//check games against gamertag
		$data = new BaseObject();
		
		$data->setGamesSum($this->getGamer()->getGames()->sumColumn('score'));
		$data->setAchievementSum($this->getGamer()->getAchievements()->sumColumn('score'));
		$data->setGamerScore($this->getGamer()->getScore());
		$data->setGameCount($this->getGamer()->getGames()->count());
		
		$this->setGamerVsGame($data);
		return $this;
	}
	
	/**
	 * Get the total for the game vs the achievement sum
	 * 
	 * @return ConsistencyReport
	 */
	protected function _gameScoreVsAchievementTotal()
	{
		$dataArray = array();
		foreach($this->getGamer()->getGames() as $game)
		{
			$data = new BaseObject();
			$data->setGameScore($game->getScore());
			$data->setAchievementSum($game->getAchievementCollection()->sumColumn('score'));
			$data->setAchievementCount($game->getAchievementCollection()->count());
			$data->setGameAchievement($game->getAchievements());
			
			$data->setName($game->getName());
			
			$dataArray[$game->getSlug()] = $data;
		}
		
		$this->setGameVsAchievement($dataArray);
		return $this;
	}
	
	/**
	 * Return this in a nice format for the command line
	 * 
	 * @return string
	 */
	public function __toString()
	{
	
		$return =  "===============================================\n";
		$return .= " {$this->getGamer()->getGamertag()} Consistency Report\n";
		$return .= "===============================================\n";
		$return .= "	GameCount:		{$this->getGamerVsGame()->getGameCount()}\n";
		$return .= "	Stored Score:		{$this->getGamerVsGame()->getGamerScore()}G\n";
		$return .= "	Game Sum:		{$this->getGamerVsGame()->getGamesSum()}G\n";
		$return .= "	Achievement Sum:	{$this->getGamerVsGame()->getAchievementSum()}G\n";
		$return .= "-----------------------------------------------\n";
		
		//diff calculations
		$gameInconsistency = $this->getGamerVsGame()->getGamerScore() - $this->getGamerVsGame()->getGamesSum();
		$achievementInconsistency = $this->getGamerVsGame()->getGamerScore() - $this->getGamerVsGame()->getAchievementSum();
		
		$return .= "	Game Inconsistency:		{$gameInconsistency}G\n";
		$return .= "	Achievement Inconsistency:	{$achievementInconsistency}G\n";
		$return .= "===============================================\n";
		
		foreach($this->getGameVsAchievement() as $game){
			$return .= "===============================================\n";
			$return .= " {$this->getGamer()->getGamertag()} Game Report - {$game->getName()}\n";
			$return .= "===============================================\n";
			$return .= "	Achievement Count:	{$game->getAchievementCount()}\n";
			$return .= "	Achievement Stored:	{$game->getGameAchievement()}\n";
			$return .= "	Stored Score:		{$game->getGameScore()}G\n";
			$return .= "	Achievement Sum:	{$game->getAchievementSum()}G\n";
			$return .= "-----------------------------------------------\n";
			
			//diff calculations
			$countInconsistency = $game->getGameAchievement() - $game->getAchievementCount();
			$scoreInconsistency = $game->getGameScore() - $game->getAchievementSum();
			
			if($countInconsistency != 0 || $scoreInconsistency != 0){
				$this->_addAlert($game);
			}
			
			$return .= "	Count Inconsistency:	{$countInconsistency}\n";
			$return .= "	Score Inconsistency:	{$scoreInconsistency}G\n";
		}
		$return .= "===============================================\n";
		
		return $this->_formatAlert($return);
	}
	
	/**
	 * Add an alert of an inconsistency
	 * 
	 * @return ConsistencyReport
	 */
	protected function _addAlert($game)
	{
		$this->_alerts[] = $game;
		return $this;
	}
	
	/**
	 * Append the alert string onto the return
	 * 
	 * @return string
	 */
	protected function _formatAlert($return)
	{
		$red = "\033[0;31m";
		if(count($this->_alerts) > 0){
			$return .= "{$red}===============================================\n";
			$return .= "{$red}===============================================\n";
			$return .= "{$red}		ALERT\n";
			$return .= "{$red}===============================================\n";
			foreach($this->_alerts as $alert){
				$return .= "{$red} Game:	{$alert->getName()}\n";
				
				//diff calculations
				$countInconsistency = $alert->getGameAchievement() - $alert->getAchievementCount();
				$scoreInconsistency = $alert->getGameScore() - $alert->getAchievementSum();
			
				$return .= "{$red}	Count Inconsistency:	{$countInconsistency}\n";
				$return .= "{$red}	Score Inconsistency:	{$scoreInconsistency}G\n";
				$return .= "{$red}-----------------------------------------------\n";
			}
		}
		return $return;
	}
}
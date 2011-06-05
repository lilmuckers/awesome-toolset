<?php

class GameScrape extends AbstractScrape
{
	/**
	 * Group of constants relating to the Game overview and scraping it
	 * I am scraping the en-US site because the date format is much easier to parse in PHP.
	 */
	const GAME_SUMMERY_URL				= 'http://live.xbox.com/en-US/GameCenter';
	const GAME_XPATH_NAME				= '//div[@class="LineItem"]/div[@class="TitleInfo grid-17"]/h3/a';
	const GAME_XPATH_IMAGE				= '//div[@class="LineItem"]/div[@class="TitleInfo grid-17"]/a/img';
	const GAME_XPATH_URL				= '//div[@class="LineItem"]/div[@class="grid-7 lastgridchild"][last()]/div[@class="grid-7 lastgridchild"][last()]/a';
	const GAME_XPATH_LAST_PLAYED		= '//div[@class="LineItem"]//div[@class="RightColumnItem GameProgressBlock"]//div[@class="PlayedBlock"]';
	const GAME_XPATH_ACHIEVEMENTS		= '//div[@class="LineItem"]//div[@class="RightColumnItem GameProgressBlock"]//div[@class="StatBlock"]//div[@class="Achievement Stat"]';
	const GAME_XPATH_SCORE				= '//div[@class="LineItem"]//div[@class="RightColumnItem GameProgressBlock"]//div[@class="StatBlock"]//div[@class="GamerScore Stat"]';
	const GAME_REGEX_SCORE_ACHIEVEMENTS	= '/(\d+) \/ (\d+)/';
	const GAME_REGEX_LAST_PLAYED		= '/([0-9]*\/[0-9]*/[0-9]*/';
	
	/**
	 * Get games as instance of self
	 * 
	 * @param Gamer $gamer
	 * @return GameScrape
	 */
	public function load()
	{
		$this->_scrapeGames();
		
		//iterate through games
		$currentGames = $this->getGamer()->getGames();
		
		foreach($this->getGames() as $gameUpdate){
			$game = $currentGames->getItemByColumn('slug', $gameUpdate->getSlug());
			if(!$game){
				$game = new Game();
				$game->setGamer($this->getGamer());
				$currentGames->addItem($game);
			}
			$game->setUpdate($gameUpdate);
		}
		
		return $this;
	}
	
	/**
	 * Scrape the game data
	 * 
	 * @return GameScrape
	 */
	protected function _scrapeGames()
	{
		$gamesScrape = $this->_getProtectedXpath(self::GAME_SUMMERY_URL);
		
		//get the full data types
		$nameScrape			= $gamesScrape->query(self::GAME_XPATH_NAME);
		$urlScrape			= $gamesScrape->query(self::GAME_XPATH_URL);
		$imageScrape		= $gamesScrape->query(self::GAME_XPATH_IMAGE);
		$lastPlayedScrape	= $gamesScrape->query(self::GAME_XPATH_LAST_PLAYED);
		$achievementScrape	= $gamesScrape->query(self::GAME_XPATH_ACHIEVEMENTS);
		$scoreScrape		= $gamesScrape->query(self::GAME_XPATH_SCORE);
		
		//iterate through the names and use the index to get the other data
		$games = array();
		for($i=0; $i<$nameScrape->length; $i++){

			//use sexy regexs to seperate the score and achievement data
			$score = array();
			$achievments = array();
			preg_match_all(self::GAME_REGEX_SCORE_ACHIEVEMENTS, $scoreScrape->item($i)->textContent, $score);
			preg_match_all(self::GAME_REGEX_SCORE_ACHIEVEMENTS, $achievementScrape->item($i)->textContent, $achievments);

			$slug = $this->_slugify($nameScrape->item($i)->textContent);
			//arrange in the array
			$games[$slug] = new BaseObject(array(
				'name'					=> $nameScrape->item($i)->textContent,
				'link'					=> sprintf(AchievementScrape::ACHIEVEMENT_BASE_URL, $urlScrape->item($i)->getAttribute('href')),
				'slug'					=> $slug,
				'image_64'				=> $imageScrape->item($i)->getAttribute('src'),
				'last_played'			=> $lastPlayedScrape->item($i)->hasChildNodes() ? $this->_date($lastPlayedScrape->item($i)->lastChild->textContent) : null,
				'total_score'			=> $score[2][0],
				'score'					=> $score[1][0],
				'total_achievements'	=> $achievments[2][0],
				'achievements'			=> $achievments[1][0]
			));
		}
		
		$this->setGames($games);
		
		return $this;
	}
}
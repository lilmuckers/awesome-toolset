<?php

class XboxLiveGamer extends BaseObject
{
	/**
	 * Constants for the account types
	 */
	const GAMER_ACCOUNT_GOLD 	= 'GOLD';
	const GAMER_ACCOUNT_SILVER 	= 'SILVER';

	/**
	 * Group of constants relating to the Gamercard and scraping it
	 */
	const GAMERCARD_BASE_URL 			= 'http://gamercard.xbox.com';
	const GAMERCARD_URL 				= 'http://gamercard.xbox.com/%s.card';
	const GAMERCARD_XPATH_MISC 			= '//span[@class="XbcFRAR"]';
	const GAMERCARD_XPATH_REPUTATION 	= '//span[@class="XbcFRAR"]/img';
	const GAMERCARD_XPATH_NAME 			= '//span[@class="XbcFLAL"]';
	const GAMERCARD_XPATH_ACCOUNT 		= '//h3';
	const GAMERCARD_XPATH_LINK 			= '//h3/a';
	const GAMERCARD_XPATH_IMAGE 		= '//img[@class="XbcgcGamertile"]';
	
	/**
	 * Group of constants relating to the Game overview and scraping it
	 * I am scraping the en-US site because the date format is much easier to parse in PHP.
	 */
	const GAME_SUMMERY_URL 				= 'http://live.xbox.com/en-US/GameCenter';
	const GAME_XPATH_NAME 				= '//div[@class="LineItem"]/div[@class="TitleInfo grid-17"]/h3/a';
	const GAME_XPATH_IMAGE 				= '//div[@class="LineItem"]/div[@class="TitleInfo grid-17"]/a/img';
	const GAME_XPATH_URL 				= '//div[@class="LineItem"]/div[@class="grid-7 lastgridchild"][last()]/div[@class="grid-7 lastgridchild"][last()]/a';
	const GAME_XPATH_LAST_PLAYED 		= '//div[@class="LineItem"]//div[@class="RightColumnItem GameProgressBlock"]//div[@class="PlayedBlock"]';
	const GAME_XPATH_ACHIEVEMENTS 		= '//div[@class="LineItem"]//div[@class="RightColumnItem GameProgressBlock"]//div[@class="StatBlock"]//div[@class="Achievement Stat"]';
	const GAME_XPATH_SCORE 				= '//div[@class="LineItem"]//div[@class="RightColumnItem GameProgressBlock"]//div[@class="StatBlock"]//div[@class="GamerScore Stat"]';
	const GAME_REGEX_SCORE_ACHIEVEMENTS = '/(\d+) \/ (\d+)/';
	const GAME_REGEX_LAST_PLAYED 		= '/([0-9]*\/[0-9]*/[0-9]*/';

	/**
	 * Group of constants for the Xpaths to retrieve achievement data
	 */
	const ACHIEVEMENT_BASE_URL			= 'http://live.xbox.com%s';
	const ACHIEVEMENT_XPATH_IMAGE 		= '//div[@class="SpaceItem"]//div[@class="AchievementInfo"]/img';
	const ACHIEVEMENT_XPATH_NAME 		= '//div[@class="SpaceItem"]//div[@class="AchievementInfo"]/h3';
	const ACHIEVEMENT_XPATH_DESCRIPTION = '//div[@class="SpaceItem"]//div[@class="AchievementInfo"]/p';
	const ACHIEVEMENT_XPATH_SCORE 		= '//div[@class="SpaceItem"]//div[@class="RightColumnItem AchievementProgressBlock"]/div[@class="Stat GamerScore"]';
	const ACHIEVEMENT_XPATH_ACQUIRED 	= '//div[@class="SpaceItem"]//div[@class="RightColumnItem AchievementProgressBlock"]/div[@class="AchievedOn"]';
	const ACHIEVEMENT_REGEX_ACQUIRED 	= '/_xbcDisplayDate\((\d+), (\d+), (\d+), (\d+), (\d+)\);/';

	/**
	 * Load up the base gamertag data
	 * 
	 * @param string $gamertag
	 * @param array $xboxLiveLoginDetails
	 * @return void
	 */
	public function __construct($gamertag){
		$this->setTag($gamertag);
		
		$this->_getInternalData();
		
		if(!$this->getInternal()){
			DB::insert('gamertag', array('gamertag'=>$this->getTag()));
			$this->_getInternalData()->_getExternalData();
			$this->getExternal()->setScore(0);
			$this->_saveGamertag()->_getInternalData();
		}
	}
	
	/**
	 * Initialises the scrape of the public data
	 * 
	 * @return XboxLiveGamer
	 */
	public function init(){
		$this->_getExternalData();
		return $this;
	}
	
	/**
	 * Set the internal gamer data
	 * 
	 * @param stdClass $data
	 * @return XboxLiveGamer
	 */
	protected function _getInternalData(){
		$internalData = DB::load('gamertag', $this->getTag(), 'gamertag');
		
		if($internalData){
			$oldData = new BaseObject($internalData);
			$this->setInternal($oldData);
		}
		
		return $this;
	}
	
	/**
	 * Scrape the external or new data
	 * 
	 * @return XboxLiveGamer
	 */
	protected function _getExternalData(){
		//generate the url
		$url = sprintf(self::GAMERCARD_URL, $this->getTag());
		
		//pull the html as an xpath
		$browser = $this->_getScraper();
		$gamerCard = $browser->getUrlXPath($url);
		
		//scrape the score
		$data = array(
			'score' => $gamerCard->query(self::GAMERCARD_XPATH_MISC)->item(1)->textContent,
			'gamertag' => $gamerCard->query(self::GAMERCARD_XPATH_NAME)->item(0)->textContent,
			'account_type' => $gamerCard->query(self::GAMERCARD_XPATH_ACCOUNT)->item(0)->getAttribute('class') == 'XbcGamertagGold' ? self::GAMER_ACCOUNT_GOLD : self::GAMER_ACCOUNT_SILVER,
			'link' => $gamerCard->query(self::GAMERCARD_XPATH_LINK)->item(0)->getAttribute('href'),
			'avatar' => $gamerCard->query(self::GAMERCARD_XPATH_IMAGE)->item(0)->getAttribute('src'),
			'zone' => $gamerCard->query(self::GAMERCARD_XPATH_MISC)->item(2)->textContent,
			'reputation_stars' => self::GAMERCARD_BASE_URL.$gamerCard->query(self::GAMERCARD_XPATH_REPUTATION)->item(0)->getAttribute('src')
		);
		
		//put the scraped score into the object
		$newData = new BaseObject($data);
		$this->setExternal($newData);
		
		return $this;
	}
	
	/**
	 * Returns true if the gamer has scored any points since last scrape
	 * 
	 * @return bool
	 */
	public function hasScored(){
		return $this->getExternal()->getScore() > $this->getInternal()->getScore();
	}
	
	/**
	 * Returns a list of all the games that have scored since last visit.
	 * 
	 * @return array
	 */
	public function getScoredGames(){
		if(!$this->getData('scored_games')){
			$online = $this->getExternalGames();
			$offline = $this->getInternalGames();
			
			$scoredGames = array();
			foreach($online as $slug=>$game){
				if(!isset($offline[$slug]) || $offline[$slug]->getScore() < $game->getScore()){
					$scoredGames[$slug] = $game;
					if(isset($offline[$slug])){
						$game->setId($offline[$slug]->getId());
					}
				}
			}
			$this->setData('scored_games', $scoredGames);
		}
		return $this->getData('scored_games');
	}
	
	/**
	 * Scrape Xbox site for the entire game data
	 * 
	 * @return array
	 */
	public function getExternalGames(){
		if(!$this->getData('external_games')){
			//pull the html as an xpath
			$browser = $this->_getScraper();
			
			//get the game name and use that as a basis for retrieving the other data
			$gamesScrape = $browser->getXboxPrivateUrlXPath(self::GAME_SUMMERY_URL);
			
			//base scrapes
			$nameScrape = $gamesScrape->query(self::GAME_XPATH_NAME);
			$urlScrape = $gamesScrape->query(self::GAME_XPATH_URL);
			$imageScrape = $gamesScrape->query(self::GAME_XPATH_IMAGE);
			$lastPlayedScrape = $gamesScrape->query(self::GAME_XPATH_LAST_PLAYED);
			$achievementScrape = $gamesScrape->query(self::GAME_XPATH_ACHIEVEMENTS);
			$scoreScrape = $gamesScrape->query(self::GAME_XPATH_SCORE);
			
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
					'name' => $nameScrape->item($i)->textContent,
					'link' => sprintf(self::ACHIEVEMENT_BASE_URL, $urlScrape->item($i)->getAttribute('href')),
					'slug' => $slug,
					'image_64' => $imageScrape->item($i)->getAttribute('src'),
					'last_played' => $lastPlayedScrape->item($i)->hasChildNodes() ? $this->_date($lastPlayedScrape->item($i)->lastChild->textContent) : null,
					'total_score' => $score[2][0],
					'score' => $score[1][0],
					'total_achievements' => $achievments[2][0],
					'achievements' => $achievments[1][0]
				));
			}
			$this->setData('external_games', $games);
		}
		return $this->getData('external_games');
	}
	
	/**
	 * Return all the internal game data
	 * 
	 * @return 
	 */
	public function getInternalGames(){
		if(!$this->getData('internal_games')){
			$gamesDb = DB::select('game', array('gamertag_id'=>$this->getInternal()->getId()));
			$games = array();
			if(is_array($gamesDb)){
				foreach($gamesDb as $game){
					$games[$game->slug] = new BaseObject($game);
				}
			}
			$this->setData('internal_games', $games);
		}
		return $this->getData('internal_games');
	}
	
	/**
	 * Return a list of all the new achievements. Uses the list of updated games to call the two achievement functions and compare.
	 * 
	 * @return array
	 */
	public function getNewAchievements(){
		if(!$this->getData('new_achievements')){
			$achievements = array();
			$updatedGames = $this->getScoredGames();
			
			foreach($updatedGames as $gameSlug=>$game){
				$external = $this->getExternalGameAchievements($game);
				$internal = $this->getInternalGameAcievements($game);
				
				$newAchivements = array();
				foreach($external as $slug=>$data){
					if(!isset($internal[$slug])){
						$achievements[$slug] = $data;
						$newAchivements[$slug] = $data;
					}
				}
				$game->setNewAchievements($newAchivements);
			}
			$this->setData('new_achievements', $achievements);
		}
		return $this->getData('new_achievements');
	}
	
	/**
	 * Get a list of all the achievements from Xbox-Live for the given game
	 * 
	 * @param string
	 * @return array
	 */
	public function getExternalGameAchievements($game){
		$games = $this->getData('external_game_achievements');
		if(!is_array($games)) $games = array();
		if(!isset($games[$game->getSlug()])){
			//pull the html as an xpath
			$browser = $this->_getScraper();
			//pull the game achievement data
			$achievementScrape = $browser->getXboxPrivateUrlXPath($game->getLink());
			
			//scrape all the data
			$imageScrape = $achievementScrape->query(self::ACHIEVEMENT_XPATH_IMAGE);
			$nameScrape = $achievementScrape->query(self::ACHIEVEMENT_XPATH_NAME);
			$descriptionScrape = $achievementScrape->query(self::ACHIEVEMENT_XPATH_DESCRIPTION);
			$scoreScrape = $achievementScrape->query(self::ACHIEVEMENT_XPATH_SCORE);
			$acquiredScrape = $achievementScrape->query(self::ACHIEVEMENT_XPATH_ACQUIRED);
			
			$achievements = array();
			for($i=0; $i < $scoreScrape->length; $i++){
				//arrange the data
				$data = array(
					'image' => $imageScrape->item($i)->getAttribute('src'),
					'name' => $nameScrape->item($i)->textContent,
					'description' => $descriptionScrape->item($i)->lastChild->textContent,
					'score' => trim($scoreScrape->item($i)->textContent),
					'acquired' => $acquiredScrape->item($i) ? $this->_date(str_replace('acquired on ', '', $acquiredScrape->item($i)->textContent)) : null,
					'game' => $game
				);
				//if this was achieved today, assume NOW as acquired date
				$data['acquired'] = $this->isToday($data['acquired']) ? $this->_dateTime() : $data['acquired'];
				
				$data['slug'] = $game->getSlug().'_'.$this->_slugify($data['name']);
				$achievements[$data['slug']] = new BaseObject($data);
			}
			$games[$game->getSlug()] = $achievements;
			$game->setExternalAchievements($games[$game->getSlug()]);
			$this->setData('external_game_achievements', $games);
		}
		return $games[$game->getSlug()];
	}
	
	/**
	 * Get a list of all the achievements from the database for the given game
	 * 
	 * @param string
	 * @return array
	 */
	public function getInternalGameAchievements($game){
		if(!$game->getId()){
			return array();
		}
		$games = $this->getData('internal_game_achievements');
		if(!is_array($games)) $games = array();
		
		if(!isset($games[$game->getSlug()])){
			$achievementsDb = DB::select('achievement', array('game_id'=>$game->getId()));
			$achievements = array();
			if($achievementsDb){
				foreach($achievementsDb as $achievement){
					$achievements[$achievement->slug] = new BaseObject($achievement);
				}
			}
			
			$achievements[$game->getSlug()] = $achievements;
			$game->setInternalAchievements($games[$game->getSlug()]);
			$this->setData('internal_game_achievements', $games);
		}
		return $games[$game->getSlug()];
	}
	
	/**
	 * Save all the scraped data to the database
	 * 
	 * @return XboxLiveGamer
	 */
	public function save(){
		//save gamer data - to keep the last-checked time synced
		$this->_saveGamertag();
		
		//save game data - but only if the user has scored
		if($this->hasScored()){
			//scrape all the new achievements
			$this->getNewAchievements();
			
			foreach($this->getScoredGames() as $slug=>$game){
				$this->_saveGame($game);
			}
		}
		
		return $this;
	}
	
	/**
	 * Save the Gamertag data
	 * 
	 * @return XboxLiveGamer
	 */
	protected function _saveGamertag(){
		//save gamer data since we've scraped it anyway
		$data = array(
			'id' => $this->getInternal()->getId(),
			'score' => $this->getExternal()->getScore(),
			'last_checked' => $this->_dateTime(),
			'account_type' => $this->getExternal()->getAccountType(),
			'link' => $this->getExternal()->getLink(),
			'avatar' => $this->getExternal()->getAvatar(),
			'zone' => $this->getExternal()->getZone(),
			'reputation_stars' => $this->getExternal()->getReputationStars()
		);
		$gamer_id = DB::insertUpdate('gamertag', $data);
		
		//make sure it was saved okay
		if(!$gamer_id){
			throw new Exception('Unable to save gamertag data');
		}
		$this->getInternal()->setId($gamer_id);
		return $this;
	}
	
	/**
	 * Save the Game data
	 * 
	 * @param BaseObject $game
	 * @return XboxLiveGamer
	 */
	protected function _saveGame(BaseObject $game){
		$data = array(
			'id' => $game->getId() ? $game->getId() : false,
			'name' => $game->getName(),
			'total_achievements' => $game->getTotalAchievements(),
			'total_score' => $game->getTotalScore(),
			'image_64' => $game->getData('image_64'),
			'gamertag_id' => $this->getInternal()->getId(),
			'last_played' => $game->getLastPlayed() ? $game->getLastPlayed() : null,
			'achievements' => $game->getAchievements(),
			'score' => $game->getScore(),
			'slug' => $game->getSlug(),
			'link' => $game->getLink()
		);
		$game_id = DB::insertUpdate('game', $data);
		
		if(!$game_id){
			throw new Exception('Unable to save game data');
		}
		$game->setId($game_id);
				
		//save achievement data
		if($game->getNewAchievements()){
			foreach($game->getNewAchievements() as $slug => $achievement){
				$this->_saveAchievement($achievement);
			}
		}
		return $this;
	}
	
	/**
	 * Save the Game Achievement data
	 * 
	 * @param BaseObject $achievement
	 * @return XboxLiveGamer
	 */
	protected function _saveAchievement(BaseObject $achievement)
	{
		$data = array(
			'game_id' => $achievement->getGame()->getId(),
			'gamertag_id' => $this->getInternal()->getId(),
			'image' => $achievement->getImage(),
			'name' => $achievement->getName(),
			'description' => $achievement->getDescription(),
			'score' => $achievement->getScore(),
			'acquired' => $achievement->getAcquired() ? $achievement->getAcquired() : null,
			'slug' => $achievement->getSlug()
		);
		
		$achievement_id = DB::insertUpdate('achievement', $data);
		
		if(!$achievement_id){
			throw new Exception('Unable to save achievement data');
		}
	}
	
	/**
	 * Get an instance of the scraper and pass it the login details.
	 * 
	 * @return XboxLiveScraper
	 */
	protected function _getScraper(){
		$_browser = XboxLiveScraper::Instance();
		$_browser->setLogin($this->getLoginDetails());
		return $_browser;
	}
	
	/**
	 * Get the latest achievement
	 * 
	 * @return BaseObject
	 */
	public function getLatestAchievement($limit = 1){
		if(!$this->getData('latest_achievement')){
			$achievements = DB::select('achievement', array('gamertag_id'=>$this->getInternal()->getId()), '*', 'AND', array('acquired', 'ASC'=>'id'), $limit);
			if(is_array($achievements)){
				$return = array();
				$games = array();
				foreach($achievements as $achievement){
					$a = new BaseObject($achievement);
					$return[] = $a;
					if(!isset($games[$a->getGameId()])){
						$game = DB::load('game', $a->getGameId());
						if($game){
							$games[$a->getGameId()] = new BaseObject($game);
						} else {
							$games[$a->getGameId()] = new BaseObject();
						}
					}
					$a->setGame($games[$a->getGameId()]);
				}
				
			}
			$this->setData('latest_achievement', $return);
		}
		return $this->getData('latest_achievement');
	}
	
	/**
	 * Return a datetime datatype from xbox live data
	 * 
	 * @param string $string
	 * @return string
	 */
	protected function _xboxDate($string){
		$matches = array();
		preg_match(self::ACHIEVEMENT_REGEX_ACQUIRED, $string, $matches);
		$time = mktime($matches[4], $matches[5], 0, $matches[1]+1, $matches[2], $matches[3]);
		return date('Y-m-d H:i:s', $time);
	}
}
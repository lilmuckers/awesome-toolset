<?php

class AchievementScrape extends AbstractScrape
{
	/**
	 * Group of constants for the Xpaths to retrieve achievement data
	 */
	const ACHIEVEMENT_BASE_URL			= 'http://live.xbox.com%s';
	const ACHIEVEMENT_XPATH_IMAGE		= '//div[@class="SpaceItem"]//div[@class="AchievementInfo"]/img';
	const ACHIEVEMENT_XPATH_NAME		= '//div[@class="SpaceItem"]//div[@class="AchievementInfo"]/h3';
	const ACHIEVEMENT_XPATH_DESCRIPTION	= '//div[@class="SpaceItem"]//div[@class="AchievementInfo"]/p';
	const ACHIEVEMENT_XPATH_SCORE		= '//div[@class="SpaceItem"]//div[@class="RightColumnItem AchievementProgressBlock"]/div[@class="Stat GamerScore"]';
	const ACHIEVEMENT_XPATH_ACQUIRED	= '//div[@class="SpaceItem"]//div[@class="RightColumnItem AchievementProgressBlock"]/div[@class="AchievedOn"]';
	
	/**
	 * Load all the achievement data for the set game
	 * 
	 * @param string $file To load achievements from a file
	 * @return AchievementScrape
	 */
	public function load($file = null)
	{
		$this->_scrapeAchievements($file);
		
		//iterate through games and add the achievements accordingly
		$currentAchievements = $this->getGame()->getAchievementCollection();
		
		//achivements are one-off things, that don't change, so we just need to add new ones.
		foreach($this->getAchievements() as $achievementUpdate){
			$achievement = $currentAchievements->getItemByColumn('slug', $achievementUpdate->getSlug());
			if(!$achievement){
				$achievement = new Achievement($achievementUpdate->getData());
				$achievement->setGamer($this->getGamer())
					->setGame($this->getGame());
				$currentAchievements->addItem($achievement);
			}
		}
		
		return $this;
	}
	
	/**
	 * Pull the actual data
	 * 
	 * @param string $file To load achievements from a file
	 * @return AchievementScrape
	 */
	protected function _scrapeAchievements($file = null)
	{
		if(is_null($file)){
			$achievementScrape = $this->_getProtectedXpath($this->getGame()->getLink());
		} else {
			$html = file_get_contents($file);
			$doc = new DOMDocument();
			@$doc->loadHTML($html);
			$achievementScrape = new DOMXPath($doc);
		}

		//scrape all the data
		$imageScrape		= $achievementScrape->query(self::ACHIEVEMENT_XPATH_IMAGE);
		$nameScrape			= $achievementScrape->query(self::ACHIEVEMENT_XPATH_NAME);
		$descriptionScrape	= $achievementScrape->query(self::ACHIEVEMENT_XPATH_DESCRIPTION);
		$scoreScrape		= $achievementScrape->query(self::ACHIEVEMENT_XPATH_SCORE);
		$acquiredScrape		= $achievementScrape->query(self::ACHIEVEMENT_XPATH_ACQUIRED);

		//iterate through the names and use the index to get the other data
		$achievements = array();
		for($i=0; $i < $scoreScrape->length; $i++){
			//arrange the data
			$data = array(
				'image'			=> $imageScrape->item($i)->getAttribute('src'),
				'name'			=> $nameScrape->item($i)->textContent,
				'description'	=> $descriptionScrape->item($i)->lastChild->textContent,
				'score'			=> trim($scoreScrape->item($i)->textContent),
				'acquired'		=> $acquiredScrape->item($i) ? $this->_date(str_replace('acquired on ', '', $acquiredScrape->item($i)->textContent)) : null,
				'game'			=> $this->getGame()
			);
			
			//if this was achieved today, assume NOW as acquired date - this will work for minutely cron - but not for manual runs
			$data['acquired'] = $this->isToday($data['acquired']) ? $this->_dateTime() : $data['acquired'];
			
			//concatonate the slug with the gameslug
			$data['slug'] = $this->getGame()->getSlug().'_'.$this->_slugify($data['name']);
			
			$achievements[] = new BaseObject($data);
		}
		
		$this->setAchievements($achievements);
		
		return $this;
	}
}
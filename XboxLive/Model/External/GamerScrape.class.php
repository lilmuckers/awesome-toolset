<?php

class GamerScrape extends AbstractScrape
{
	/**
	 * Constants for the account types
	 */
	const GAMER_ACCOUNT_GOLD 	= 'GOLD';
	const GAMER_ACCOUNT_SILVER 	= 'SILVER';
	
	/**
	 * Group of constants relating to the Gamercard and scraping it
	 */
	const GAMERCARD_BASE_URL			= 'http://gamercard.xbox.com';
	const GAMERCARD_URL					= 'http://gamercard.xbox.com/%s.card';
	const GAMERCARD_XPATH_SCORE			= '//div[@class="Body"]/div[@class="Stats"]//div[@class="Stat"]/div[@class="Stat"]';
	const GAMERCARD_XPATH_HEAD			= '//div[@class="Header"]/div[@class="Gamertag"]/a/span';
	const GAMERCARD_XPATH_LINK			= '//div[@class="Header"]/div[@class="Gamertag"]/a';
	const GAMERCARD_XPATH_IMAGE			= '//img[@class="GamerPic"]';
	
	/**
	 * Pull new data and put it into the current object
	 * 
	 * @param mixed $gamer
	 * @return GamerScrape
	 */
	public function load()
	{
		//scrape the gamercard
		$this->_scrapeGamercard();
		
		return $this;
	}
	
	/**
	 * Pull the new data out of the gamercard
	 * 
	 * @return array
	 */
	protected function _scrapeGamercard()
	{
		//generate the gamercard URL
		$url = sprintf(self::GAMERCARD_URL, urlencode($this->getGamer()->getGamertag()));
		
		//get the xpath
		$gamerCard = $this->_getXpath($url);
		
		//scrape the score
		$data = array(
			'score' => $gamerCard->query(self::GAMERCARD_XPATH_SCORE)->item(0)->textContent,
			'gamertag' => $gamerCard->query(self::GAMERCARD_XPATH_HEAD)->item(0)->textContent,
			'account_type' => $gamerCard->query(self::GAMERCARD_XPATH_HEAD)->item(0)->getAttribute('class') == 'Gold' ? self::GAMER_ACCOUNT_GOLD : self::GAMER_ACCOUNT_SILVER,
			'link' => self::GAMERCARD_BASE_URL.$gamerCard->query(self::GAMERCARD_XPATH_LINK)->item(0)->getAttribute('href'),
			'avatar' => $gamerCard->query(self::GAMERCARD_XPATH_IMAGE)->item(0)->getAttribute('src')
		);

		//put the scraped score into the object
		$this->setData($data);

		return $this;
	}
}
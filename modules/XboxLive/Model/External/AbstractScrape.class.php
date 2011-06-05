<?php

class AbstractScrape extends BaseObject
{
	/**
	 * Get the html of the requested URL as an xpath
	 * 
	 * @param string $url
	 * @return DOMXPath
	 */
	protected function _getXpath($url)
	{
		return $this->_getBrowser()->getUrlXPath($url);
	}
	
	/**
	 * Get the html of the requested URL as an xpath. URL is behind live login page
	 * 
	 * @param string $url
	 * @return DOMXPath
	 */
	protected function _getProtectedXpath($url)
	{
		return $this->_getBrowser()->getXboxPrivateUrlXPath($url);
	}
	
	/**
	 * Return an instance of the browser
	 * 
	 * @return Scraper
	 */
	protected function _getBrowser()
	{
		$_browser = Scraper::Instance();
		$_browser->setLogin($this->getLoginDetails());
		return $_browser;
	}
	
	/**
	 * Set the gamer data
	 * 
	 * @param Gamer $gamer
	 * @return AbstractScrape
	 */
	public function setGamer(Gamer $gamer)
	{
		list($username, $password) = $gamer->getLoginData();
		$this->setLoginDetails(array('username'=>$username, 'password'=>$password));
		$this->setData('gamer', $gamer);
		return $this;
	}
}
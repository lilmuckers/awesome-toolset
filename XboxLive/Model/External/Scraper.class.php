<?php

//Since the browser isn't namespaced like I do - manually include the file
require_once dirname(__FILE__) . '/simpletest/browser.php';

class Scraper
{
	/**
	 * @var Scraper
	 */
	protected static $_instance;

	/**
	 * @var SimpleBrowser
	 */
	protected static $_browser;

	/**
	 * @var array
	 */
	protected $_xboxLiveLogin = array();

	/**
	 * Singleton Protected constructor
	 */
	protected function __construct(){
		//setup the browser and connect
		$this->_browser = new SimpleBrowser();
	}

	/**
	 * Singleton Instance Accessor
	 * 
	 * @return Scraper
	 */
	public function Instance(){
		if(!(self::$_instance instanceof Scraper)){
			self::$_instance = new Scraper();
		}
		return self::$_instance;
	}

	/**
	 * Gets the Html from the given URL
	 * 
	 * @param string $url
	 * @return string
	 */
	public function getUrlHtml($url){
		return $this->htmlToUtf8($this->_browser->get($url));
	}

	/**
	 * Convert provided html string to utf8
	 * 
	 * @param string $html
	 * @return string
	 */
	public function htmlToUtf8($html)
	{
		$headpos = mb_strpos($html,'<head>');
		if ($headpos === false) {
			$headpos = mb_strpos($html,'<HEAD>');
		}
		if ($headpos !== false) {
			$headpos += 6;
			$html = mb_substr($html,0,$headpos) . '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' .mb_substr($html,$headpos);
		}
		$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
		return $html;
	}

	/**
	 * Get the html from the given url as an Xpath object
	 * 
	 * @param $url
	 * @return DOMXPath
	 */
	public function getUrlXPath($url){
		$html = $this->getUrlHtml($url);
		if($html){
			return $this->_convertToXPathable($html);
		}
		return false;
	}

	/**
	 * Get the xpath based on the html behind that blasted Live login page
	 * 
	 * @param string $url
	 * @return string
	 */
	public function getXboxPrivateUrlXPath($url){
		$html = $this->getXboxPrivateUrlHtml($url);
		if($html){
			return $this->_convertToXPathable($html);
		}
		return false;
	}

	/**
	 * Get the html behind that blasted Live login page
	 * 
	 * @param string $url
	 * @return string
	 */
	public function getXboxPrivateUrlHtml($url){
		$this->_browser->get($url);
		$this->_browser->setField('login', $this->_xboxLiveLogin['username']);
		$this->_browser->setField('passwd', $this->_xboxLiveLogin['password']);
		$this->_browser->clickSubmitByName('SI');
		$this->_browser->submitFormById('fmHF');
		return $this->getUrlHtml($url);
	}

	/**
	 * Convert the inputted string to something that can be xpathed
	 * 
	 * @param string $html
	 * @return DOMXPath
	 */
	protected function _convertToXPathable($html){
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
 		return new DOMXPath($doc);
	}

	/**
	 * Set the login details
	 * 
	 * @param array $array
	 * @return Scraper
	 */
	public function setLogin($array){
		$this->_xboxLiveLogin = $array;
		return $this;
	}
}
<?php

class BaseObject
{
	/**
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * @var array
	 */
	protected static $_underscoreCache = array();
	
	/**
	 * Constructor - if Data is set, then construct the data
	 * 
	 * @param mixed $data
	 * @return void
	 */
	public function __construct($data = null){
		if(!is_null($data)){
			$this->setData($data);
		}
		$this->_construct();
	}
	
	/**
	 * Internal Constructor
	 * 
	 * @return void
	 */
	protected function _construct(){}
	
	/**
	 * Convert stdClass data into an array
	 * 
	 * @param stdClass $data
	 * @return array
	 */
	public function stdClassToArray(stdClass $data){
		$return = array();
		foreach($data as $key=>$value){
			if($value instanceof stdClass){
				$return[$key] = $this->stdClassToArray($value);
			} else {
				$return[$key] = $value;
			}
		}
		return $return;
	}
	
	/**
	 * Magic call for getter/setter
	 * 
	 * @param string $method
	 * @param mixed $args
	 * @return mixed
	 */
	public function __call($method, $args){
		$underscore = $this->_underscore(substr($method,3));
		$args = current($args);
		if(substr($method, 0 , 3) == 'get'){
			return $this->getData($underscore);
		}
		if(substr($method, 0 , 3) == 'set'){
			return $this->setData($underscore, $args);
		}
		return null;
	}
	
	/**
	 * Get data from _data array
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function getData($key = null){
		if(is_null($key)){
			return $this->_data;
		}
		if(isset($this->_data[$key])){
			return $this->_data[$key];
		}
		return null;
	}
	
	/**
	 * Set data by key/value
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return BaseObject
	 */
	public function setData($key, $value = null){
		if($key instanceof stdClass){
			$key = $this->stdClassToArray($key);
		}
		if(is_array($key)){
			foreach($key as $k=>$v){
				$this->setData($k, $v);
			}
		} else {
			$this->_data[$key] = $value;
		}
		return $this;
	}
	
	/**
	 * Convert from camel case to underscored
	 * 
	 * @param string $name
	 * @return string
	 */
	protected function _underscore($name)
	{
		if (isset(self::$_underscoreCache[$name])) {
			return self::$_underscoreCache[$name];
		}
		$result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
		self::$_underscoreCache[$name] = $result;
		return $result;
	}
	
	/**
	 * Slugifys text for unique identifiers for games, achievements, etc
	 * 
	 * @param string $text
	 * @return string
	 */
	protected function _slugify($text)
	{
		$text = str_replace('&', ' and ', $text);
		$text = htmlentities($text);
		$text = str_replace(array('&Acirc;','&reg;','&acirc;','&cent;','?'), "", $text);
		$text = html_entity_decode($text);
		$text = iconv("UTF-8", "UTF-8//IGNORE", $text);
		
		if(strlen($text) > 100) {
			$text = substr($text, 0, 100);
		}
		
		// convert all characters to ascii equivalent.
		$map = array(
			'/à|á|å|â/' => 'a',
			'/è|é|ê|ẽ|ë/' => 'e',
			'/ì|í|î/' => 'i',
			'/ò|ó|ô|ø/' => 'o',
			'/ù|ú|ů|û/' => 'u',
			'/ç/' => 'c',
			'/ñ/' => 'n',
			'/ä|æ/' => 'ae',
			'/ö/' => 'oe',
			'/ü/' => 'ue',
			'/Ä/' => 'Ae',
			'/Ü/' => 'Ue',
			'/Ö/' => 'Oe',
			'/ß/' => 'ss',
			'/[^\w\s]/' => ' '
		);
	
		// remove any non letter or digit
		$text = preg_replace(array_keys($map), array_values($map), $text);
		$text = preg_replace('~[^\w\d]+~u', '-', $text);
	
		// trim
		 $text = trim($text, '-');
	
		// lowercase
		$text = strtolower($text);
	
		return $text;
	}
	
	/**
	 * Format a date to a datestamp for the DB
	 * 
	 * @param string $date
	 * @return mixed
	 */
	protected function _date($date = 'NOW'){
		$timestamp = strtotime($date);
		if($timestamp){
			return date('Y-m-d',$timestamp);
		}
		return false;
	}
	
	/**
	 * Returns a fully formatted time string for Mysql
	 * 
	 * @param string $date
	 * @return mixed
	 */
	protected function _dateTime($date = 'NOW'){
		$timestamp = strtotime($date);
		if($timestamp){
			return date('Y-m-d H:i:s',$timestamp);
		}
		return false;
	}
	
	/**
	 * Check if a supplied date/time stamp is today
	 * 
	 * @param string $date
	 * @return bool
	 */
	protected function isToday($date){
		if($date){
			return $this->_date($date) == $this->_date();
		}
		return false;
	}
	
	/**
	 * Convert the object into something for SQL to deal with
	 * 
	 * @return string|int
	 */
	protected function _toSql(){
		return get_class($this);
	}
}
<?php

class Notify extends BaseDBObject
{
	/**
	 * The type code
	 * 
	 * @var string
	 */
	protected $_typeCode;
	
	/**
	 * Code/class relations
	 * 
	 * @var array
	 */
	protected static $_types = array(
		'twitter'	=> 'TwitterNotify',
		'facebook'	=> 'FacebookNotify'
	);
	
	/**
	 * setup the database shiznaz
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct('gamer_notify');
	}
	
	/**
	 * Before save, set the variables
	 * 
	 * @return AbstractNotify
	 */
	protected function _beforeSave()
	{
		$this->setGamertagId($this->getGamer()->getId());
		$this->setNotifyId($this->getNotifier()->getId());
		$this->setType($this->_typeCode);
		return parent::_beforeSave();
	}
	
	/**
	 * Generate the new model
	 * 
	 * @param Gamer $gamer
	 * @param string $type
	 * @param string $identifier
	 * @return Notify
	 */
	public static function factory($gamer, $type, $identifier)
	{
		$class = self::getClass($type);
		$notify = new $class();
		$notify->setGamer($gamer);
		$notify->loadNotification($identifier);
		return $notify;
	}
	
	/**
	 * Get the class of the given notification type
	 * 
	 * @param mixed $code
	 * @return string
	 */
	public static function getClass($code)
	{
		if($code instanceof stdClass){
			$code = self::stdClassToArray($code);
		}
		if(is_array($code)){
			$code = $code['type'];
		}
		if(!array_key_exists($code, self::$_types)){
			throw new Exception('Invalid notification type "'.$code.'"');
		}
		return self::$_types[$code];
	}
	
	/**
	 * Append and prepend the suffix and prefix accordingly
	 * 
	 * @param string $message
	 * @return Notify
	 */
	public function send($message)
	{
		if($this->hasPrefix() && $this->getPrefix()){
			$message = $this->getPrefix()." ".$message;
		}
		if($this->hasSuffix() && $this->getSuffix()){
			$message .= " ".$this->getSuffix();
		}
		return $this->_send($message);
	}
	
	/**
	 * Load the notification type
	 * 
	 * @param string $identifier
	 * @return Notify
	 */
	public function loadNotification($identifier)
	{
		return $this;
	}
	
	/**
	 * Send the notification
	 * 
	 * @param string $message
	 * @return Notify
	 */
	protected function _send($message)
	{
		return $this;
	}
}
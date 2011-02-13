<?php

class Mcrypt extends BaseObject
{
	/**
	 * Store the encryption key
	 * 
	 * @var string
	 */
	protected static $_key = 'HOLDER_KEY';
	
	/**
	 * Set the encryption key
	 * 
	 * @param string $key
	 * @return Mcrypt
	 */
	public static function setKey($key){
		self::$_key = $key;
	}
	
	/**
	 * Encrypt the provided string by blowfish
	 * 
	 * @param string $string
	 * @return string
	 */
	public static function in($string){
		return mcrypt_encrypt(MCRYPT_BLOWFISH, self::$_key, $string, MCRYPT_MODE_ECB);
	}
	
	/**
	 * Decrypt the provided string by blowfish
	 * 
	 * @param string $string
	 * @return string
	 */
	public static function out($string){
		return mcrypt_decrypt(MCRYPT_BLOWFISH , self::$_key, $string, MCRYPT_MODE_ECB);
	}
}
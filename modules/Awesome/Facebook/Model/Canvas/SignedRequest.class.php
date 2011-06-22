<?php
namespace Awesome\Facebook\Model\Canvas;

class SignedRequest extends \Base\Object
{
	/**
	 * Constants for checking the hash
	 */
	const HASH_HMAC_TYPE = 'sha256';
	
	/**
	 * Data for making the authorisation
	 * 
	 * @var string
	 */
	protected $_appSecret;
	
	/**
	 * Set the consumer variables from the config
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		$namespace = $this->hasConfigNamespace() ? $this->getConfigNamespace() : 'Facebook';
		$this->_appSecret 		= \Base\Config::path($namespace.'/OAuth/secret');
		parent::_construct();
	}
	
	/**
	 * Process the signed request
	 * 
	 * @param string $signedRequest
	 * @return \Awesome\Facebook\Model\Canvas\SignedRequest
	 */
	public function process($signedRequest){
		list($encodedSig, $payload) = explode('.', $signedRequest, 2);
		$this->setSignature($encodedSig);
		$this->setPayload($payload);
		
		//verify that this was all kosher
		$this->_verify();
		
		//if all was good, then set the payload data
		$this->setData(json_decode($this->_base64urldecode($this->getPayload())));
		
		return $this;
	}
	
	/**
	 * Verify the payload against the signature
	 * 
	 * @return \Awesome\Facebook\Model\Canvas\SignedRequest
	 * @throws \Base\Exception
	 */
	protected function _verify(){
		$dataSignature = hash_hmac(self::HASH_HMAC_TYPE, $this->getPayload(), $this->_appSecret, true);
		if($dataSignature != $this->getSignature()){
			$this->_error('Potential hack attempt - invalid signature');
		}
		return $this;
	}
	
	/**
	 * Set the encoded signature
	 * 
	 * @param string $encodedSig
	 * @return \Awesome\Facebook\Model\Canvas\SignedRequest
	 */
	public function setSignature($encodedSig){
		return $this->setData('signature', $this->_base64urldecode($encodedSig));
	}
	
	/**
	 * Base64 decode and url decode the string
	 * 
	 * @param string $data
	 * @return string
	 */
	protected function _base64urldecode($data){
		return base64_decode(strtr($data, '-_', '+/'));
	}
}
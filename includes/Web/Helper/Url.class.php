<?php
namespace Base\Web\Helper;

class Url extends \Base\Helper\HelperAbstract
{
	/**
	 * Config paths to generate the paths
	 */
	const CONFIG_BASE_PATH			= 'Base/Environment/url/base';
	const CONFIG_BASE_SECURE_PATH	= 'Base/Secure/url/base';
	
	/**
	 * Constant for the base URL
	 */
	const PATTERN_BASE_URL			= '%s/%s';
	
	/**
	 * Generate the URL of a given internal path
	 * 
	 * @param string $path
	 * @param array $options
	 * @return string
	 */
	public function getUrl($path, $options = array()){
		$url = \Base\Config::path(self::CONFIG_BASE_PATH);
		
		$output = $path;
		
		foreach($options as $key=>$value){
			$output .= '/'.$key.'/'.$value;
		}
		
		return sprintf(self::PATTERN_BASE_URL, $url, $output);
	}
}
<?php
namespace Base;

class Config extends Object
{
	/**
	 * Store the config instance
	 * 
	 * @var \Base\Config
	 */
	protected static $_instance;
	
	/**
	 * Config file path - %s is module name
	 */
	const CONFIG_FILE_PATH = '%s/%s/config.ini';
	
	/**
	 * Regex for the elements of the xpath style recursion
	 */
	const CONFIG_XPATH_REGEX = '/^([a-zA-Z0-9_]+)\//';
	
	/**
	 * The default exception type to throw
	 * 
	 * @var string
	 */
	protected $_exceptionClass = '\Base\Exception\Config';
	
	/**
	 * Array of references to the array entries for xpath entries
	 * 
	 * @vars array
	 */
	protected $_xpathCache = array();
	
	/**
	 * List of directories to not scan
	 * 
	 * @var array
	 */
	protected $_ignoreDirs = array(
		'.','..','.git','.svn', 'web','includes', 'module','theme'
	);
	
	/**
	 * Default config files that will be overwritten by later files
	 * 
	 * @var array
	 */
	protected $_defaultConfigPaths = array(
		'includes/config/theme.ini',
		'includes/config/routing.ini'
	);
	
	/**
	 * Parse all the extant config files
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct();
		
		//load the default config
		foreach($this->_defaultConfigPaths as $path){
			$this->parseFile($path, 'Base');
		}
		
		//go hunting for config files
		foreach(explode(PATH_SEPARATOR, get_include_path()) as $includePath){
		
			foreach(scandir($includePath) as $file){
				//build the theoretical config file path for a module
				$configFile = sprintf(self::CONFIG_FILE_PATH, $includePath, $file);
				
				//check if it's a valid directory and if the file is readable
				if(is_dir($includePath.DIRECTORY_SEPARATOR.$file) && !in_array($file, $this->_ignoreDirs) && is_readable($configFile))
				{
					$this->parseFile($configFile, $file);
				}
			}
		}
	}
	
	/**
	 * Instantiate the configuration object
	 * 
	 * @return \Base\Config
	 */
	public static function instance()
	{
		if(!(self::$_instance instanceof Config))
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Parse a cofiguration file into the global config
	 * 
	 * @param string $path
	 * @param string $namespace
	 * @return \Base\Config
	 */
	public function parseFile($path, $namespace)
	{
		if(!is_readable($path)){
			$this->_error('Unable to read configuration file: '.$path);
		}
		
		//parse the data
		$data = array($namespace => parse_ini_file($path, true));
		$this->setData($data);
		return $this;
	}
	
	/**
	 * Load config by using xpath like strings
	 * 
	 * @param string $path
	 * @param array $array
	 * @return mixed
	 */
	public function getConfigByPath($path = null, &$array = null, $parentKey = null)
	{
		//first attempt to load the runtime cache (regex is spendy)
		if(array_key_exists($path, $this->_xpathCache)){
			return $this->_xpathCache[$path];
		}
		
		//if no array to scour is provided - use the internal one as a reference
		if(is_null($array)){
			$array = &$this->getData();
		}
		
		//get the first part of the array
		$matches = array();
		if(0 < preg_match(self::CONFIG_XPATH_REGEX, $path, $matches))
		{
			//if the array key exists, continue to the next part of the string
			if(array_key_exists($matches[1], $array)){
				return $this->getConfigByPath(substr($path, strlen($matches[0])), $array[$matches[1]], $parentKey.$matches[1].'/');
			}
		}
		elseif(array_key_exists($path, $array))
		{
			//if it wasn't an xpath string, then try to cache and return extant key
			$this->_xpathCache[$parentKey.$path] = &$array[$path];
			return $array[$path];
		}
		
		//if all else fails - panic and set fire to the building
		$this->_error('Invalid config path supplied');
	}
	
	/**
	 * Return an array of values fitting the pattern * / $section / $var
	 * 
	 * @param string $path
	 * @return array
	 */
	public function getAllData($section, $var)
	{
		$return = array();
		
		foreach($this->getData() as $namespace=>$data)
		{
			if(array_key_exists($section, $data) && array_key_exists($var, $data[$section])){
				$return[$namespace] = $data[$section][$var];
			}
		}
		return $return;
	}
	
	/**
	 * Simpler accessor for the xpath method
	 * 
	 * @param string $path
	 * @return mixed
	 */
	public static function path($path)
	{
		return self::instance()->getConfigByPath($path);
	}
}
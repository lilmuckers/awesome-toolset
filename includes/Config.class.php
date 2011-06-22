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
		'.','..','.git','.svn', 'web','includes', 'modules','theme'
	);
	
	/**
	 * Default config files that will be overwritten by later files
	 * 
	 * @var array
	 */
	protected $_defaultConfigPaths = array(
		'includes/config/theme.ini',
		'includes/config/routing.ini',
		'includes/config/helpers.ini'
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
			$this->_findConfigFiles($includePath);
		}
	}
	
	/**
	 * Recursively search for a module configuration file
	 * 
	 * @param string $directory
	 * @param string $ns
	 * @return \Base\Config
	 */
	protected function _findConfigFiles($directory, $ns = null)
	{
		if(is_dir($directory)){
			foreach(scandir($directory) as $file){
				//build the theoretical config file path for a module
				$configFile = sprintf(self::CONFIG_FILE_PATH, $directory, $file);
				
				//check if it's a valid directory and if the file is readable
				if(is_dir($directory.DIRECTORY_SEPARATOR.$file) && !in_array($file, $this->_ignoreDirs))
				{
					if(is_readable($configFile)) {
						$this->parseFile($configFile, $ns.$file);
					} else {
						$this->_findConfigFiles($directory.DIRECTORY_SEPARATOR.$file, $ns.$file.'\\');
					}
				}
			}
		}
		return $this;
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
		$namespace = explode('\\', $namespace);
		$moduleNamespace = count($namespace) == 2 ? $namespace[1] : $namespace[0];
		
		$data = array($moduleNamespace => parse_ini_file($path, true));
		
		$data[$moduleNamespace]['_routerNamespace'] = count($namespace) == 2 ? $namespace[0] : null;
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
	 * @param string $var
	 * @return array
	 */
	public function getAllData($section, $var = null)
	{
		$return = array();
		
		foreach($this->getData() as $namespace=>$data)
		{
			if(is_null($var) && array_key_exists($section, $data)){
				$return[$namespace] = $data[$section];
			} elseif(array_key_exists($section, $data) && array_key_exists($var, $data[$section])){
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
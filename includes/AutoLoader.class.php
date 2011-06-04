<?php
namespace Base;

class AutoLoader
{
	/**
	 * Cache of the instance of this class
	 * 
	 * @var \Base\AutoLoader
	 */
	static protected $_instance;
	
	/**
	 * Cache of already loaded classes
	 * 
	 * @var array
	 */
	protected $_loadedClasses = array();
	
	/**
	 * Define path rewrites
	 * 
	 * @var array
	 */
	protected $_pathRewrites = array(
		'Base' => 'includes'
	);
	
	/**
	 * Various constants to do with autoloading
	 */
	const PATH_PATTERN = '%s.class.php';
	const PATH_PATTERN_ZEND = '%s.php';
	
	/**
	 * Register the autoloader
	 * 
	 * @return void
	 */
	static public function register()
	{
		spl_autoload_register(array(self::instance(), 'load'));
	}
	
	/**
	 * Singleton Instance Initiation
	 * 
	 * @return \Base\AutoLoader
	 */
	static public function instance()
	{
		if(!self::$_instance){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Load the specified class file
	 * 
	 * @param string $class
	 * @return void
	 */
	public function load($class)
	{
		if($filename = $this->classExists($class)){
			//cache it for future awesome
			$this->_cacheLoad($class, $filename);
			include $filename;
		}
		return;
	}
	
	/**
	 * Return filename if the class exists
	 * 
	 * @param string $class
	 * @return mixed
	 */
	public function classExists($class)
	{
		if(!$this->_isLoaded($class)){
			//Namespace to path
		
			$classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $class)));
			$classFile = str_replace('\\', DIRECTORY_SEPARATOR, $classFile);
			foreach($this->_pathRewrites as $search=>$replace){
				$classFile = str_replace($search, $replace, $classFile);
			}
			
			//Generate paths in both the Zend and Cafe format
			$classFileCafe = sprintf(self::PATH_PATTERN, $classFile);
			$classFileZend = sprintf(self::PATH_PATTERN_ZEND, $classFile);
			//check the filename formats - cafe format taking precedence
			if($this->_fileExists($classFileCafe)){
				$classFile = $classFileCafe;
			} elseif($this->_fileExists($classFileZend)){
				$classFile = $classFileZend;
			} else {
				$classFile = $classFileCafe;
			}
			
			return $classFile;
		}
		return false;
	}
	
	/**
	 * Check the include paths for a file
	 * 
	 * @param string $filename
	 * @return bool
	 */
	protected function _fileExists($filename){
		$paths = explode(PATH_SEPARATOR, get_include_path());
		foreach ($paths as $path) {
			$path = rtrim($path, DIRECTORY_SEPARATOR);
			$fullpath = $path.DIRECTORY_SEPARATOR.$filename;
			if (file_exists($fullpath)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Check if the class has been loaded already
	 * 
	 * @param string $class
	 * @return bool
	 */
	protected function _isLoaded($class)
	{
		return array_key_exists($class, $this->_loadedClasses);
	}
	
	/**
	 * Cache the fact the file has been loaded
	 * 
	 * @param string $class
	 * @param string $classFile
	 * @return void
	 */
	protected function _cacheLoad($class, $classFile)
	{
		$this->_loadedClasses[$class] = $classFile;
	}
	
	/**
	 * Add a tree to be included - I know it's inefficient, but it solves my issues
	 * 
	 * @param $baseDir
	 * @return \Base\AutoLoader
	 */
	public function addPaths($baseDir)
	{
		$baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR);
		$paths = explode(PATH_SEPARATOR, get_include_path());
		
		//add this path to the include paths
		if(!in_array($baseDir, $paths)){
			$paths[] = $baseDir;
			set_include_path(implode(PATH_SEPARATOR, $paths));
		}
		
		foreach(scandir($baseDir) as $file){
			$filePath = $baseDir.DIRECTORY_SEPARATOR.$file;
			if($file != '.' && $file != '..' && is_dir($filePath)){
				$this->addPaths($filePath);
			}
		}
		
		return $this;
	}
}

<?php
namespace Base;

//set the root working directory and include the autoloader
chdir(dirname(__DIR__));
require_once('includes/AutoLoader.class.php');

//Register the auto-loader
$autoLoader = AutoLoader::instance();
$autoLoader->addPath(getcwd().DIRECTORY_SEPARATOR.'modules');
$autoLoader->register();

//register the configuration
$config = Config::instance();
$config->parseFile('config.ini', 'Base');

//do the basic setup
DB::configure(
	$config->getConfigByPath('Base/Database/host'), 
	$config->getConfigByPath('Base/Database/username'), 
	$config->getConfigByPath('Base/Database/password'), 
	$config->getConfigByPath('Base/Database/database')
);
Mcrypt::setKey($config->getConfigByPath('Base/MCrypt/key'));

//do the routing setup
Web\Action\Router::basenameNamespacing();
Web\Action\Response\View\Layout::instance()->loadFiles();
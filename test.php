<?php
define('XBL_DB_HOST', 'localhost');
define('XBL_DB_USER', 'dev');
define('XBL_DB_PASSWORD', 'dev');
define('XBL_DB_DB', 'xbox_scrape');

define('XBL_USERNAME', 'patrick.w.mckinley@gmail.com');
define('XBL_PASSWORD', '');

require_once 'DB.class.php';
require_once 'BaseObject.class.php';
require_once 'XboxLiveGamer.class.php';
require_once 'XboxLiveScraper.class.php';

//run it
$xbl = new XboxLiveGamer('lilmuckers');
$xbl->init();
$xbl->setLoginDetails(array('username'=>'passport@email.com', 'password'=>'password'));
$xbl->save();

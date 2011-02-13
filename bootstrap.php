<?php

require_once('includes/AutoLoader.class.php');

$autoLoader = AutoLoader::instance();
$autoLoader->addPaths(dirname(__FILE__));
$autoLoader->register();

DB::configure('localhost', 'username', 'password', 'database');
Mcrypt::setKey('RANDOM ENCRYPTION KEY PLEASE');
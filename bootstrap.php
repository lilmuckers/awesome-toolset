<?php
namespace Base;

require_once('includes/AutoLoader.class.php');

$autoLoader = AutoLoader::instance();
$autoLoader->register();

DB::configure('localhost', 'username', 'password', 'database');
Mcrypt::setKey('RANDOM ENCRYPTION KEY PLEASE');
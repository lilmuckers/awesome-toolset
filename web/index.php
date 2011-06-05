<?php
namespace Base;

//bootstrap
require_once(dirname(__DIR__).'/includes/bootstrap.php');

//set the web scope
Scope::setScope(Scope::SCOPE_WEB);

//run the web controller
$run = new Controller();
$run->web();

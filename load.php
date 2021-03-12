<?php
define('ABSPATH',__DIR__);//php will detect the director where is being called and save as a constant
define('ADMIN_PATH',ABSPATH.'/admin');
define('ADMIN_SCRIPT_PATH',ADMIN_PATH.'/scripts');

//  ini_set('display_errors',1); or change 1 to 0
ini_set('display_errors', 1);

session_start();//server create session for us and it unique

//load constant rather than repeat path x times
require_once ABSPATH. '/config/database.php';
require_once ADMIN_SCRIPT_PATH.'/login.php';
require_once ADMIN_SCRIPT_PATH.'/functions.php';
<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('log_errors', 1);
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set('memory_limit','512M');
ini_set('auto_globals_jit', 1);
ini_set('expose_php','off');
set_time_limit(60);

header_remove('X-Powered-By');

define('DIRSEPS_REGEX', '/\/|\\\/');

define('ROOT_DIRNAME',dirname(__DIR__ . '..' . DIRECTORY_SEPARATOR));
define('PATH_TO_404',ROOT_DIRNAME . DIRECTORY_SEPARATOR . '404.php');
define('DEBUG_PATH',ROOT_DIRNAME . DIRECTORY_SEPARATOR . 'debug_folder');
define('DEBUG_FILE',DEBUG_PATH . DIRECTORY_SEPARATOR . 'log.txt');

require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';

ini_set('error_log',DEBUG_PATH . DIRECTORY_SEPARATOR . 'php_errorlog.txt');

define('SQLH_MANAGER', 'mysql');
define('SQLH_HOST', '127.0.0.1');
define('SQLH_PORT', '3306');
define('SQLH_USERNAME', 'root');
define('SQLH_PASSWORD', '');
define('SQLH_DATABASE', 'AppBikeServer');

spl_autoload_register(function ($class_name)
{
	try
	{
		require __DIR__ . DIRECTORY_SEPARATOR . 'classes/' . $class_name . '.php';
	}
	catch(Exception $e)
	{
		print_r($e);
		exit('E5555');
	}
});
?>
<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('log_errors', 1);
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('memory_limit','512M');
ini_set('auto_globals_jit', 1);
ini_set('expose_php','off');
set_time_limit(60);

header_remove('X-Powered-By');

header("Access-Control-Allow-Origin: *");

define('DIRSEPS_REGEX', '/\/|\\\/');

if(defined('__DIR__') === FALSE)
	define('__DIR__', dirname(__FILE__));

define('ROOT_DIRNAME',dirname(__DIR__ . '..' . DIRECTORY_SEPARATOR));
define('DEBUG_PATH',ROOT_DIRNAME . DIRECTORY_SEPARATOR . 'debug_folder');
define('DEBUG_FILE',DEBUG_PATH . DIRECTORY_SEPARATOR . 'log.txt');

ini_set('session.cookie_httponly', 1 );
ini_set('session.save_path', realpath(ROOT_DIRNAME . DIRECTORY_SEPARATOR . 'session'));
session_start();

ini_set('error_log',DEBUG_PATH . DIRECTORY_SEPARATOR . 'php_errorlog.txt');

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'db_defines.php';

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

require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';
?>
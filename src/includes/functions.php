<?php

// http://php.net/manual/pt_BR/function.boolval.php#111674
if (!function_exists('boolval'))
{
	function boolval($val)
	{
		return (bool) $val;
	}
}

function ReturnSelectOptions(/*Array*/ $options, /*String*/ $key_CheckedOption = NULL, /*Bool*/ $invert_paradigm = FALSE)
{
	$str = '';
	if($invert_paradigm === TRUE)
		$options = array_flip($options);
	foreach($options as $key=>$value)
	{
		$real_key = filterhtmlspecials(strval($key));
		$real_value = filterhtmlspecials(strval($value));
		$str .= "<option";
		$str .= " value='{$real_value}'";
		if($key === $key_CheckedOption)
			$str .= " SELECTED='SELECTED'";
		$str .= ">";
		$str .= $real_key;
		$str .= "</option>";
	}
	return $str;
}



function IfDBErrorDebug(PDO_MODDED $DBInstance, $stmt, $result, $debug = DEBUG)
{
	if($result === FALSE)
	{
		$ReturnArr = Array();
		$DBInstance->Debug_PDO_Error($stmt, $debug);
		// Se debug for true, já vai ter dado Exit lá em cima
		$ReturnArr['result'] = FALSE;
		$ReturnArr['message'] = "Erro interno do servidor";
		JsonResponse($ReturnArr);
	}
	else return TRUE;
}


// http://php.net/manual/en/function.realpath.php#105876
function relativePath($from, $to, $ps = DIRECTORY_SEPARATOR)
{
  $arFrom = explode($ps, rtrim($from, $ps));
  $arTo = explode($ps, rtrim($to, $ps));
  while(count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0]))
  {
	array_shift($arFrom);
	array_shift($arTo);
  }
  return str_pad("", count($arFrom) * 3, '..'.$ps).implode($ps, $arTo);
}


function JsonResponse($var)
{
	if(func_num_args() > 0)
	{
		$vr = $var;
	}
	else
	{
		global $ReturnArr;
		$vr = $ReturnArr;
	}
	exit(json_encode($vr));
}




// http://stackoverflow.com/questions/4783802/converting-string-into-web-safe-uri
function toAscii($str, $replace=Array(), $delimiter='-') {
 if( !empty($replace) ) {
  $str = str_replace((array)$replace, ' ', $str);
 }

 $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
 $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
 $clean = strtolower(trim($clean, '-'));
 $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

 return $clean;
}


function ToPatternString(/*String*/ $str)
{
	return toAscii($str);
}








// http://php.net/manual/en/function.hash-equals.php
if(!function_exists('hash_equals')) {
	  function hash_equals($str1, $str2) {
		if(strlen($str1) != strlen($str2)) {
		  return false;
		} else {
		  $res = $str1 ^ $str2;
		  $ret = 0;
		  for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
		  return !$ret;
		}
	  }
	}


function CryptBlowFish(/*String*/ $password,/*int*/ $runs = 10, $salt = '')
{
	if(strlen($salt) === 60)
	{
		$cripto = crypt($password, $salt);
	}
	else
	{
		$runs = intval($runs);
		if($runs < 4 || $runs > 31)
			return FALSE;
		if(empty($salt) === TRUE)
		{
			$salt = random_string(22, 'abcdefghijklmnopqrstuvwxyz0123456789./');
		}
		$cripto = crypt($password, '$2y$' . $runs . '$' . $salt . '$');
	}

	if(strlen($cripto) === 60)
		return Array('cripto' => $cripto, 'salt' => $salt);
	else
		return FALSE;
}

function CheckCripto(/*String*/ $password = '',/*int*/ $runs = 10, /*String*/ $salt = '', /*String*/ $cripto = '')
{
	if($password === '' || $cripto === '')
	{
		return FALSE;
	}
	if($salt === '')
	{
		$salt = explode('$', $cripto);
		$salt = $salt[3];
	}
	$crpx = CryptBlowFish($password, $runs, $salt);
	return hash_equals( $cripto, $crpx['cripto'] );
}

// http://stackoverflow.com/a/1037136/6090603
function random_string($length, $character_set = 'abcdefghijklmnopqrstuvwxyz0123456789./')
{
	$temp_array = Array();
	for ($i = 0; $i < $length; $i++)
	{
		$temp_array[] = $character_set[rand(0, strlen($character_set) - 1)];
	}
	shuffle($temp_array);
	return implode('', $temp_array);
}

// http://php.net/manual/pt_BR/function.strcasecmp.php#107016
function mb_strcasecmp($str1, $str2, $encoding = null) {
    if (null === $encoding) { $encoding = mb_internal_encoding(); }
    return strcmp(mb_strtoupper($str1, $encoding), mb_strtoupper($str2, $encoding));
}

function str_case_to_bool($val)
{
	if(mb_strcasecmp($val, "true") === 0)
		$val = TRUE;
	elseif(mb_strcasecmp($val, "false") === 0)
		$val = FALSE;
	else
		$val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
	return $val;
}

function numeric_file_sort_ASC(/*String*/ $filename1, /*String*/ $filename2)
{
	$filename1 = intval(chopExtension(basename($filename1)));
	$filename2 = intval(chopExtension(basename($filename2)));

	return ($filename1 > $filename2);
}

function chopExtension($filename)
{
	return substr($filename, 0, strrpos($filename, '.'));
}

function filterhtmlspecials(/*String*/ $string)
{
	return htmlspecialchars($string, ENT_HTML5 | ENT_QUOTES, 'UTF-8');
}

function escht(/*String*/ $string)
{
	return filterhtmlspecials($string);
}

function Get_User_Constants()
{
	$Arr = get_defined_constants(true);
	$Arr = $Arr['user'];
	return print_r($Arr,TRUE);
}


function Write_To_Logfile()
{
	$finalText = '';

	$finalText .= "[" . gmdate('Y-m-d h:i:s \G\M\T') . "]\n";

	foreach (func_get_args() as $param)
	{
		$finalText .= print_r($param,true) . "\n";
	}

	$finalText .= "\n";

	file_put_contents(DEBUG_FILE, $finalText, FILE_APPEND);
}



// http://php.net/manual/en/function.mime-content-type.php#107798
// TODO: Cachear isso
define('APACHE_MIME_TYPES_URL','http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types');
function generateUpToDateMimeArray($url)
{
	$s=Array();
	foreach(@explode("\n",@file_get_contents($url))as $x)
		if(isset($x[0])&&$x[0]!=='#'&&preg_match_all('#([^\s]+)#',$x,$out)&&isset($out[1])&&($c=count($out[1]))>1)
			for($i=1;$i<$c;$i++)
				$s[$out[1][$i]]=$out[1][0];
	return $s;
}


function ParseAcceptHeaderFormat($str)
{
	$str = strval($str);
	$Farr = Array();

	$bufs = Array('', '1.0');
	$i = 0;

	$str = $str . ',';

	for($j=0; $j<strlen($str); ++$j)
	{
		$char = $str[$j];

		if($char === ',')
		{
			$Farr[$bufs[0]] = floatval($bufs[1]);
			$bufs = Array('', '1.0');
			$i = 0;
			continue;
		}
		elseif($char === ';')
		{
			$i = 1;
			$bufs[$i] = '';
			$j += 2; // q=
			continue;
		}
		elseif($char === ' ' || $char === '=')
		{
			continue;
		}

		$bufs[$i] .= $char;
		continue;
	}

	krsort($Farr, SORT_NUMERIC);

	return $Farr;
}

function SendOverFile($path)
{
	$extension = pathinfo($path, PATHINFO_EXTENSION);
	$mime = generateUpToDateMimeArray(APACHE_MIME_TYPES_URL);
	$mime = $mime[$extension];
	$filestr = file_get_contents($path, FILE_TEXT);

	header_remove();

	$HEADERS = getallheaders();
	$HEADERS = array_change_key_case($HEADERS, CASE_LOWER);

	header('HTTP/1.1 200 OK');
	// header('Accept-Ranges', 'bytes'); // Need to actually implement this here later i guess
	header('Vary: Accept-Encoding, User-Agent');
	header('Content-Type: ' . $mime);

	$HEADERS["accept-encoding"] = ParseAcceptHeaderFormat($HEADERS["accept-encoding"]);

	if(substr($mime,0,6) !== "image/")
	{
		foreach($HEADERS["accept-encoding"] as $encoding=>$q)
		{
			switch(strtolower($encoding))
			{
				case 'gzip':
					$filestr = gzencode($filestr, 9, FORCE_GZIP);
					header('Content-Encoding: gzip');
					break 2;
				default:
					continue;
			}
		}
	}

	$len = strlen($filestr);
	header('Content-Length: ' . $len);

	echo $filestr;

	exit();
}

// https://github.com/ralouphie/getallheaders
if (!function_exists('getallheaders')) {

	/**
	 * Get all HTTP header key/values as an associative array for the current request.
	 *
	 * @return string[string] The HTTP header key/value pairs.
	 */
	function getallheaders()
	{
		$headers = Array();

		$copy_server = Array(
			'CONTENT_TYPE'   => 'Content-Type',
			'CONTENT_LENGTH' => 'Content-Length',
			'CONTENT_MD5'	=> 'Content-Md5',
		);

		foreach ($_SERVER as $key => $value) {
			if (substr($key, 0, 5) === 'HTTP_') {
				$key = substr($key, 5);
				if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
					$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
					$headers[$key] = $value;
				}
			} elseif (isset($copy_server[$key])) {
				$headers[$copy_server[$key]] = $value;
			}
		}

		if (!isset($headers['Authorization'])) {
			if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
				$headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
			} elseif (isset($_SERVER['PHP_AUTH_USER'])) {
				$basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
				$headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
			} elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
				$headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
			}
		}

		return $headers;
	}

}


function Print_User_Constants()
{
	$DBInstancest = get_defined_constants(true);
	$DBInstancest = $DBInstancest['user'];
	print_r($DBInstancest);
}


function logval($forceLogoff = TRUE)
{
	if(@$_SESSION["loggedin"] != TRUE)
	{
		if($forceLogoff === TRUE) ACTION_logout();
		return FALSE;
	}
	/*else if(time() - $_SESSION["last_activity"] >= 2700)
	{
		if($forceLogoff === TRUE) ACTION_logout();
		return FALSE;
	}*/
	else
	{
		ACTION_updatelet();
		return TRUE;
	}
}

function critical_logval()
{
	if(logval() === FALSE)
	{
		$ReturnArr = Array();
		$ReturnArr['result'] = FALSE;
		$ReturnArr['message'] = "Sua sessão expirou!";
		$ReturnArr['special'] = "RELOG";
		JsonResponse($ReturnArr);
	}
	else
	{
		return TRUE;
	}
}

function ACTION_logout()
{
	$_SESSION = Array();
	if(isset($_COOKIE[session_name()]))
	{
		setcookie(session_name(), '', time()-42000, '/');
	}
	return FALSE;
}

function ACTION_updatelet()
{
	$_SESSION["last_activity"] = time();
	return true;
}

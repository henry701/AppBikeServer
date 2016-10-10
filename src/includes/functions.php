<?php
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






// http://stackoverflow.com/questions/4783802/converting-string-into-web-safe-uri
function toAscii($str, $replace=array(), $delimiter='-') {
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
	$runs = intval($runs);
	if($runs < 4 || $runs > 31)
		return FALSE;
	if(empty($salt) === TRUE)
		$salt = str_replace( Array('+',DIRECTORY_SEPARATOR), '0', trim(base64_encode(openssl_random_pseudo_bytes(15)),'=') );
	$cripto = crypt($password, '$2y$' . $runs . '$' . $salt . '$');

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
	return hash_equals( $cripto, crypt($password, '$2y$' . $runs . '$' . $salt . '$') );
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

	$finalText .= '==============================================' . "\n";
	$finalText .= gmdate('Y-m-d h:i:s \G\M\T') . "\n";
	$finalText .= '==============================================' . "\n";

	foreach (func_get_args() as $param)
	{
		$finalText .= print_r($param,true) . "\n";
	}

	$finalText .= "\n";
	$finalText .= '==============================================' . "\n";
	$finalText .= '==============================================' . "\n";
	$finalText .= '==============================================' . "\n";

	$finalText .= "\n\n";

	file_put_contents(DEBUG_FILE,$finalText,FILE_APPEND);
}



// http://php.net/manual/en/function.mime-content-type.php#107798
// TODO: Cachear isso
define('APACHE_MIME_TYPES_URL','http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types');
function generateUpToDateMimeArray($url)
{
    $s=array();
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
        $headers = array();

        $copy_server = array(
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
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
	$const = get_defined_constants(true);
	$const = $const['user'];
	print_r($const);
}


function logval($forceLogoff = TRUE)
{
	if($_SESSION["loggedin"] != TRUE)
	{
		if($forceLogoff) ACTION_logout();
		return FALSE;
	}
	else if(time() - $_SESSION["last_activity"] >= 2700)
	{
		if($forceLogoff) ACTION_logout();
		return FALSE;
	}
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
		exit('Não está logado!');
	}
	else
	{
		return TRUE;
	}
}

function ACTION_logout()
{
	$_SESSION = array();
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

?>
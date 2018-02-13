<?php
//  AcmlmBoard XD support - Handy snippets
// TODO organize better
if (!defined('BLARG')) die();

function endsWith($a, $b){
	return substr($a, strlen($a) - strlen($b)) == $b;
}

function endsWithIns($a, $b){
	return endsWith(strtolower($a), strtolower($b));
}

function startsWith($a, $b){
	return substr($a, 0, strlen($b)) == $b;
}

function startsWithIns($a, $b){
	return startsWith(strtolower($a), strtolower($b));
}


//	Not really much different to kill()
function Alert($s, $t="")
{
	if($t=="")
		$t = __("Notice");

	RenderTemplate('messagebox', 
		array(	'msgtitle' => $t,
				'message' => $s));
}

function Kill($s, $t="")
{
	if($t=="")
		$t = __("Error");
	Alert($s, $t);
	throw new KillException();
}

function dieAjax($what)
{
	global $ajaxPage;

	echo $what;
	$ajaxPage = true;
	exit;
	throw new KillException();
}

// returns FALSE if it fails.
function QueryURL($url)
{
	if (function_exists('curl_init'))
	{
		$page = curl_init($url);
		if ($page === FALSE)
			return FALSE;
		
		curl_setopt($page, CURLOPT_TIMEOUT, 10);
		curl_setopt($page, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($page, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($page, CURLOPT_USERAGENT, 'Blargboard/'.BLARG_VERSION);
			
		$result = curl_exec($page);
		curl_close($page);
		return $result;
	}
	else if (ini_get('allow_url_fopen'))
	{
		return file_get_contents($url);
	}
	else
		return FALSE;
}


function format()
{
	$argc = func_num_args();
	if($argc == 1)
		return func_get_arg(0);
	$args = func_get_args();
	$output = $args[0];
	for($i = 1; $i < $argc; $i++)
	{
		// TODO kill that hack
		$splicethis = preg_replace("'\{([0-9]+)\}'", "&#x7B;\\1&#x7D;", $args[$i]);
		$output = str_replace("{".($i-1)."}", $splicethis, $output);
	}
	return $output;
}

// TODO NUKE
function write()
{
	$argc = func_num_args();
	if($argc == 1)
	{
		echo func_get_arg(0);
		return;
	}
	$args = func_get_args();
	$output = $args[0];
	for($i = 1; $i < $argc; $i++)
	{
		// TODO kill that hack
		$splicethis = preg_replace("'\{([0-9]+)\}'", "&#x7B;\\1&#x7D;", $args[$i]);
		$output = str_replace("{".($i-1)."}", $splicethis, $output);
	}
	echo $output;
}


function OptimizeLayouts($text)
{
	$bucket = array();

	// Save the tags in the temp array and remove them from where they were originally
	$regexps = array("@<style(.*?)</style(.*?)>(\r?\n?)@si", "@<link(.*?)>(\r?\n?)@si", "@<script(.*?)</script(.*?)>(\r?\n?)@si");
	foreach ($regexps as $regexp)
	{
		preg_match_all($regexp, $text, $temp, PREG_PATTERN_ORDER);
		$text = preg_replace($regexp, "", $text);
		$bucket = array_merge($bucket, $temp[0]);
	}

	// Remove duplicates
	$bucket = array_unique($bucket);

	// Put the tags back
	$newStyles = "<!-- head tags -->".implode("", $bucket)."<!-- /head tags -->";
	$text = str_replace("</head>", $newStyles."</head>", $text);
	$text = str_replace("<recaptcha", "<script", $text);
	return $text;
}


function LoadPostToolbar()
{
	echo "<script type=\"text/javascript\">window.addEventListener(\"load\", hookUpControls, false);</script>";
}



function TimeUnits($sec)
{
	if($sec <    60) return "$sec sec.";
	if($sec <  3600) return floor($sec/60)." min.";
	if($sec < 86400) return floor($sec/3600)." hour".($sec >= 7200 ? "s" : "");
	return floor($sec/86400)." day".($sec >= 172800 ? "s" : "");
}


function cdate($format, $date = 0)
{
	global $loguser;
	if($date == 0) $date = time();
	return gmdate($format, $date+$loguser['timezone']);
}

function Report($stuff, $hidden = 0, $severity = 0)
{
	$full = GetFullURL();
	$here = substr($full, 0, strrpos($full, "/"));

	$req = 'NULL';

	$data = array("content" => str_replace("#HERE#", $here, $stuff), "username" => "Forum logs");

	$curl = curl_init(Settings::get("logwebhook"));
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	return curl_exec($curl);

	Query("insert into {reports} (ip,user,time,text,hidden,severity,request)
		values ({0}, {1}, {2}, {3}, {4}, {5}, {6})", $_SERVER['REMOTE_ADDR'], (int)$loguserid, time(), str_replace("#HERE#", $here, $stuff), $hidden, $severity, $req);
}

function Shake()
{
	$cset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
	$salt = "";
	$chct = strlen($cset) - 1;
	while (strlen($salt) < 16)
		$salt .= $cset[mt_rand(0, $chct)];
	return $salt;
}

function IniValToBytes($val)
{
	$val = trim($val);
	$last = strtolower($val[strlen($val)-1]);
	switch($last)
	{
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}

	return $val;
}

function BytesToSize($size, $retstring = '%01.2f&nbsp;%s')
{
	$sizes = array('B', 'KiB', 'MiB');
	$lastsizestring = end($sizes);
	foreach($sizes as $sizestring)
	{
		if($size < 1024)
			break;
		if($sizestring != $lastsizestring)
			$size /= 1024;
	}
	if($sizestring == $sizes[0])
		$retstring = '%01d %s'; // Bytes aren't normally fractional
	return sprintf($retstring, $size, $sizestring);
}

function makeThemeArrays()
{
	global $themes, $themefiles;
	$themes = array();
	$themefiles = array();
	$dir = @opendir("themes");
	while ($file = readdir($dir))
	{
		if ($file != "." && $file != "..")
		{
			$themefiles[] = $file;
			$name = explode("\n", @file_get_contents("./themes/".$file."/themeinfo.txt"));
			$themes[] = trim($name[0]);
		}
	}
	closedir($dir);
}

function getdateformat()
{
	global $loguserid, $loguser;

	if($loguserid)
		return $loguser['dateformat'].", ".$loguser['timeformat'];
	else
		return Settings::get("dateformat");
}

function formatdate($date)
{
	return cdate(getdateformat(), $date);
}
function formatdatenow()
{
	return cdate(getdateformat());
}
function relativedate($date) {
	return '<time class="timeago" datetime="' . date(DATE_ATOM, $date) . '">' . formatdate($date) . '</time>';
}

function formatBirthday($b) {
	return format("{0} ({1} old)", date("F j, Y", $b), Plural(floor((time() - $b) / 86400 / 365.2425), "year"));
}

function unround_number($number, $decimalsonly = false) {
	$broken_number = explode('.', $number);
	if ($decimalsonly) return $broken_number[1];
	return $broken_number;
}

function time_elapsed_string($ptime, $future = false, $color = false) {
	if ($future)
		$etime = max(time(),$ptime) - min(time(),$ptime);
	else
		$etime = time() - $ptime;

	if ($etime < 1) {
		return '0 seconds';
	}

	$a = [
			12 * 30 * 24 * 60 * 60 + 20952	=>	'year', // Leap years, guys!
			30 * 24 * 60 * 60				=>	'month',
			 7 * 24 * 60 * 60				=>	'week',
			24 * 60 * 60					=>	'day',
			60 * 60							=>	'hour',
			60								=>	'minute',
			1								=>	'second'
		];

	foreach ($a as $secs => $str) {
		$d = $etime / $secs;
		if ($d >= 1) {
			$t = unround_number($d);
			$t[1] = unround_number(number_format(('0.'.$t[1]), 1), true);
			if ($color) {
				switch ($str) {
					case 'second':
					case 'minute':
						$style = ' style="color: red;"';
						break;
					case 'hour':
						$style = ' style="color: orange;"';
						break;
					case 'day':
					case 'week':
						$style = '';
						break;
					default:
						$style = ' style="color: grey;"';
						break;
				}
			} else $color = '';
			return "<span$style>" . ($future ? 'in ' : '') . $t[0] . ($t[1] > 0 ? '.' . $t[1] : '') . ' ' . $str . (($t[0].'.'.$t[1]) > 1 ? 's' : '') . ($future ? '' : ' ago') . '</span>';
		}
	}
}

function getSexName($sex) {
	$sexes = array(
		0 => __("Male"),
		1 => __("Female"),
		2 => __("N/A"),
	);

	return $sexes[$sex];
}


function formatIP($ip) {
	global $loguser;

	$res = $ip;
	$res .=  " " . IP2C($ip);
	$res = "<nobr>$res</nobr>";
	$ip = ip2long_better($ip);
	if (HasPermission('admin.ipsearch'))
		return actionLinkTag($res, "ipquery", $ip);
	else
		return $res;
}

function ip2long_better($ip) {
	$v = explode('.', $ip); 
	return ($v[0]*16777216)+($v[1]*65536)+($v[2]*256)+$v[3];
}

function long2ip_better($ip) {
   return long2ip((float)$ip);
}

//TODO: Optimize it so that it can be made with a join in online.php and other places.
function IP2C($ip) {
	global $dblink;
	//This nonsense is because ips can be greater than 2^31, which will be interpreted as negative numbers by PHP.
	$ipl = ip2long($ip);
	$r = Fetch(Query("SELECT * 
				 FROM {ip2c}
				 WHERE ip_from <= {0s} 
				 ORDER BY ip_from DESC
				 LIMIT 1", 
				 sprintf("%u", $ipl)));
}

function getBirthdaysText($ret = true) {
	global $luckybastards, $loguser;
	
	$luckybastards = array();
	$today = gmdate('m-d', time()+$loguser['timezone']);
	
	$rBirthdays = Query("select u.birthday, u.(_userfields) from {users} u where u.birthday > 0 and u.primarygroup!={0} order by u.name", Settings::get('bannedGroup'));
	$birthdays = array();
	while($user = Fetch($rBirthdays)) {
		$b = $user['birthday'];
		if(gmdate("m-d", $b) == $today) {
			$luckybastards[] = $user['u_id'];
			if ($ret) {
				$y = gmdate("Y") - gmdate("Y", $b);
				$birthdays[] = UserLink(getDataPrefix($user, 'u_'))." (".$y.")";
			}
		}
	}
	if (!$ret) return '';
	if(count($birthdays))
		$birthdaysToday = implode(", ", $birthdays);
	if(isset($birthdaysToday))
		return __("We wish a happy birthday to:")." ".$birthdaysToday;
	else
		return "";
}

function getKeywords($stuff) {
	$common = array('the', 'and', 'that', 'have', 'for', 'not', 'this');

	$stuff = strtolower($stuff);
	$stuff = str_replace('\'s', '', $stuff);
	$stuff = preg_replace('@[^\w\s]+@', '', $stuff);
	$stuff = preg_replace('@\s+@', ' ', $stuff);

	$stuff = explode(' ', $stuff);
	$stuff = array_unique($stuff);
	$finalstuff = '';
	foreach ($stuff as $word) {
		if (strlen($word) < 3 && !is_numeric($word)) continue;
		if (in_array($word, $common)) continue;
		
		$finalstuff .= $word.' ';
	}

	return substr($finalstuff,0,-1);
}

function forumRedirectURL($redir) {
	if ($redir[0] == ':')
	{
		$redir = explode(':', $redir);
		return actionLink($redir[1], $redir[2], $redir[3], $redir[4]);
	}
	else
		return $redir;
}


function smarty_function_plural($params, $template) {
	return Plural($params['num'], $params['what']);
}

function entity_fix__callback($matches) {
	if (!isset($matches[2]))
		return '';

	$num = $matches[2][0] === 'x' ? hexdec(substr($matches[2], 1)) : (int) $matches[2];

	// we don't allow control characters, characters out of range, byte markers, etc
	if ($num < 0x20 || $num > 0x10FFFF || ($num >= 0xD800 && $num <= 0xDFFF) || $num == 0x202D || $num == 0x202E)
		return '';
	else
		return '&#' . $num . ';';
}

function utfmb4_fix($string) {
	$i = 0;
	$len = strlen($string);
	$new_string = '';
	while ($i < $len) {
		$ord = ord($string[$i]);
		if ($ord < 128)	{
			$new_string .= $string[$i];
			$i++;
		} elseif ($ord < 224) {
			$new_string .= $string[$i] . $string[$i+1];
			$i += 2;
		} elseif ($ord < 240) {
			$new_string .= $string[$i] . $string[$i+1] . $string[$i+2];
			$i += 3;
		} elseif ($ord < 248) {
			// Magic happens.
			$val = (ord($string[$i]) & 0x07) << 18;
			$val += (ord($string[$i+1]) & 0x3F) << 12;
			$val += (ord($string[$i+2]) & 0x3F) << 6;
			$val += (ord($string[$i+3]) & 0x3F);
			$new_string .= '&#' . $val . ';';
			$i += 4;
		}
	}
	return $new_string;
}

function utfmb4String($string) {
	return utfmb4_fix(preg_replace_callback('~(&#(\d{1,7}|x[0-9a-fA-F]{1,6});)~', 'entity_fix__callback', $string));
}

function echo_memory_usage() { 
	$mem_usage = memory_get_usage(true); 

	if ($mem_usage < 1024) 
		$memoryusage = $mem_usage." bytes"; 
	elseif ($mem_usage < 1048576) 
		$memoryusage = round($mem_usage/1024,2)." kB"; 
	else
		$memoryusage = round($mem_usage/1048576,2)." MB";
	
	return $memoryusage;
}

function checknumeric(&$var) {
	if(!is_numeric($var)) {
		$var=0;
		return false;
	}
	return true;
}
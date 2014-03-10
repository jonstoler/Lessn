<?php

include('config.php');
include('SIDB423.php');
include('db.php');

define('UPPN_VERSION',	'1.1.1');

define('UPPN_DOMAIN', 	preg_replace('#^www\.#', '', $_SERVER['SERVER_NAME']));
define('UPPN_URL', 	str_replace('-/index.php', '', 'http://'.UPPN_DOMAIN.$_SERVER['PHP_SELF']));

define('COOKIE_NAME', 	DB_PREFIX.'auth');
define('COOKIE_VALUE',	md5(USERNAME.PASSWORD.COOKIE_SALT));
define('COOKIE_DOMAIN', '.'.UPPN_DOMAIN);

if (!defined('API_SALT')) define('API_SALT', 'L35sm4K35M0U7hSAP1'); // added in 1.0.5
define('API_KEY', md5(USERNAME.PASSWORD.API_SALT));

define('NOW', 		time());
define('YEAR',		365 * 24 * 60 * 60);

// handle login
if (isset($_POST['username']))
{
	if (md5($_POST['username'].$_POST['password'].COOKIE_SALT) == COOKIE_VALUE)
	{
		setcookie(COOKIE_NAME, COOKIE_VALUE, NOW + YEAR, '/', COOKIE_DOMAIN);
		$_COOKIE[COOKIE_NAME] = COOKIE_VALUE;
	}
}
// API login
else if (isset($_GET['api']) && $_GET['api'] == API_KEY)
{
	$_COOKIE[COOKIE_NAME] = COOKIE_VALUE;
}

// handle logout
if (isset($_GET['logout']))
{
	setcookie(COOKIE_NAME, '', NOW - YEAR, '/', COOKIE_DOMAIN);
	unset($_COOKIE[COOKIE_NAME]);
	header('Location:./');
}

// require login
if (!isset($_COOKIE[COOKIE_NAME]) || $_COOKIE[COOKIE_NAME] != COOKIE_VALUE)
{
	include('pages/login.php');
	exit();
}
// prolong login for another year, unless this is an API request
else if (!isset($_GET['api']))
{
	setcookie(COOKIE_NAME, COOKIE_VALUE, NOW + YEAR, '/', COOKIE_DOMAIN);
}

// new shortcut
if (isset($_FILES['media']) && !empty($_FILES['media']))
{
	$file = $_FILES['media'];
	$info = getimagesize($file['tmp_name']);

	if($info !== false){
		$mime = $info['mime'];

		if(in_array($mime, $ALLOWED_TYPES)){
			$ext = explode('.', $file['name']);
			$ext = end($ext);

			$chars = range('a', 'z');
			$filename = '';
			for($i = 0; $i < 6; $i++){
				shuffle($chars);
				$filename .= current($chars);
			}
			$filename .= '.' . $ext;

			@move_uploaded_file($file['tmp_name'], '../p/' . $filename);

			$url = UPPN_URL . 'p/' . $filename;
			if (!preg_match('#^[^:]+://#', $url))
			{
				$url = 'http://'.$url;
			}
			$checksum 		= sprintf('%u', crc32($url));
			if ($db->query($db->prepare('SELECT `id` FROM `'.DB_PREFIX.'urls` WHERE `checksum`=? AND `url`=? LIMIT 1', $checksum, $url))) {
				if ($rows = $db->rows()) {
					$id = $rows[0]['id'];
				}
				else {
					$db->query($db->prepare('INSERT INTO `'.DB_PREFIX.'urls` SET `url`=?, `checksum`=?, `mime`=?', $filename, $checksum, $mime));
					$id = $db->insert_id();
				}
			}
			$new_url = UPPN_URL.base_convert($id, 10, 36);
			
			if (isset($_GET['tweet']))
			{
				$_GET['redirect'] = 'http://twitter.com/?status=%l';
			}
			
			if (isset($_GET['redirect']))
			{
				header('Location:'.str_replace('%l', urlencode($new_url), $_GET['redirect']));
				exit();
			}
			
			if (isset($_GET['api']))
			{
				if(isset($_GET['mediaurl'])){ // for twitter apps
					echo '<mediaurl>'.$new_url.'</mediaurl>';
					exit();
				}
				echo $new_url;
				exit();
			}
			
			include('pages/done.php');
		} else {
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			header('Status:404');
			die('404 Not Found');
		}
	}
}
else
{
	include('pages/add.php');
}
<?php
include 'function.php';
/*
$dbName = 'seucontest';
require 'opensql.php';
$myToken = isset($ck['seuToken']) ? $ck['seuToken'] : '0';
$IP = $_SERVER['REMOTE_ADDR'];
$sql = 'select * from `token` where `token` = '.$dbh->quote($myToken).' and `expire_time` >= '.time().' and `ip` = '.$dbh->quote($IP);
$res = SQLquery($sql);
if (isset($res[0]['uid'])) {
	$loginUID = $res[0]['uid'];
	$sql = 'select * from `users` where `uid` = '.$loginUID;
	$Loginfo = SQLquery($sql)[0];
} else {
	header('Content-type: text/plain');
	header('Refresh: 3; url=/');
	exit('The environment was changed and you will login again. Your IP is: "'.$IP.'" and token saved in your PC is '.$myToken);
}*/

define('FLAG_STU', 0); // 学生权限
define('FLAG_ASI', 10); // 辅导员权限
define('FLAG_TEA', 20); // 教师权限
define('FLAG_ADM', 30); // 管理权限
define('FLAG_MAX', 100); // 后台权限
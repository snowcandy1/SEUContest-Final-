<?php

set_time_limit(0);

//防止误操作
if(!isset($_REQUEST['confirm']) or $_REQUEST['confirm']!='initialize'){
    echo date('Y-m-d H:i:s : ')."未确认的操作<br/>";
    die(0);
}

$serverlogs=array();
function appendLog($log){
	global $serverlogs;
	$serverlogs[]=date('Y-m-d H:i:s : ').$log;
	echo end($serverlogs)."<br/>";
	ob_flush();
	flush();
}

include_once "../include.php";
include_once WEB_ROOT."/backstage/import.php";

if(my_redis()===false){
	echo date('Y-m-d H:i:s : ')."Redis连接失败<br/>";
	die();
}

my_redis()->flushAll();
my_redis()->bgRewriteAOF();

$serverdata['status']=server::DEBUGGING;
$serverdata['note']="考试未开始";

appendLog("开始初始化数据库");

import();

appendLog("数据库初始化完成");

unengageKey(0,'server',$serverdata,$serverlogs);
unengageKey(5,'lastappeal',[],[]);


?>
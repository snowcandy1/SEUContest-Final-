<?php
//防止误操作
if(!isset($_REQUEST['confirm']) or $_REQUEST['confirm']!='end'){
    echo date('Y-m-d H:i:s : ')."未确认的操作<br/>";
    die(0);
}

include_once "../include.php";

$server=engageKey(0,'server');
if($server[0]['status']!=server::TEST_STARTED){
    echo date('Y-m-d H:i:s : ')."没有开始考试<br/>";
    unengageKey(0,'server');
    die(0);
}
$server[0]['status']=server::TEST_ENDED;
$server[0]['note']="考试已结束";
$server[1][]=date('Y-m-d H:i:s : ')."结束考试";
echo end($server[1])."<br/>";
unengageKey(0,'server',$server[0],$server[1]);

?>
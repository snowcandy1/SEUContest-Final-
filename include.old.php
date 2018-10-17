<?php

if(!defined("WEB_ROOT")){
	define("WEB_ROOT",__DIR__);
}


include_once WEB_ROOT."/include/class_account.php";
include_once WEB_ROOT."/include/class_admin.php";
include_once WEB_ROOT."/include/class_server.php";
include_once WEB_ROOT."/include/class_student.php";
include_once WEB_ROOT."/include/class_teacher.php";
include_once WEB_ROOT."/include/functions_appeal.php";
include_once WEB_ROOT."/include/functions_test.php";
include_once WEB_ROOT."/config.php";


if(!defined("REQUEST_SUCCESS")){

	define("REQUEST_SUCCESS",0);

	define("ERR_SERVER_NOT_WORKING",-1);
	define("ERR_MISSING_PARAM",12000);
	define("ERR_UNKNOWN_REQUEST",14000);
	define("ERR_UNAUTHED",20000);

	define("REQUEST_FAILED",1000);
	define("REQUEST_OBJECT_NOT_EXSIST",2000);
	define("REQUEST_FORBIDDEN",10000);
}


/**
 * 返回消息
 * 
 * @param int $code
 * @param string $message
 * @param array $data
 */
function message($code,$message,$data=array()){
	return array(
		"code"=>$code,
		"message"=>$message,
		"data"=>$data
	);
}



/**
 * 返回一个redis连接
 * 
 * @return Redis|bool false on connection error
 */
function my_redis(){
	if(!isset($GLOBALS['my_redis_connection'])){
		$GLOBALS['my_redis_connection']=new Redis();
		try{
			$GLOBALS['my_redis_connection']->pconnect('127.0.0.1');
		}
		catch(RedisException $expection){
			return false;
		}
	}
	return $GLOBALS['my_redis_connection'];
}

/**
 * @param string $log
 */
function mylog($log){
	return date('Y-m-d H:i:s : ').$log;
}

/**
 * 检查 key 是否存在
 * @param int $db
 * @param string $key
 * @return bool
 */
function keyExists($db,$key){
	$result=my_redis()->multi(2)->select($db)->exists($key)->exec();
	return $result[1];
}

/**
 * 返回 keys
 * @param int $db
 * @param string $keys
 * @return array
 */
function getKeys($db,$keys="*"){
	$result=my_redis()->multi(2)->select($db)->keys($keys)->exec();
	return $result[1];
}

/**
 * 获取一个 key
 * 返回 data 部分
 * @param int $db
 * @param string $key
 * @return array
 */
function getKeyData($db,$key){
	$result=my_redis()->multi(2)->select($db)->hGet($key,'data')->exec();
	if($result[1]===false){
		return null;
	}
	return igbinary_unserialize($result[1]);
}

/**
 * 获取一个 key
 * 返回 [0]=>data [1]=>logs
 * @param int $db
 * @param string $key
 * @return array
 */
function getKeyAll($db,$key){
	$result=my_redis()->multi(2)->select($db)->hGetAll($key)->exec();
	if(count($result[1])==0){
		return null;
	}
	return [igbinary_unserialize($result[1]['data']),igbinary_unserialize($result[1]['logs'])];
}

function getKeysDataRaw($keys){
	$pipe=my_redis()->multi(2);
	foreach($keys as $key){
		$pipe->hGet($key,'data');
	}
	return $pipe->exec();
}


/**
 * 占用一个 key
 * 返回 [0]=>data [1]=>logs
 * 
 * @param int $db
 * @param string $key
 * @return array
 */
function engageKey($db,$key){
	for($i=0;$i<2000;$i++){
		$result=my_redis()->multi(2)->select($db)->hGetAll($key)->exec();
		if(count($result[1]) <= 0 || $result[1]===false){
			return null;
		}
		if(isset($result[1]['engaged']) and microtime(true)-$result[1]['engaged']<1){
			usleep(1000);
			continue;
		}
		my_redis()->hSet($key,'engaged',microtime(true));
		return [igbinary_unserialize($result[1]['data']),igbinary_unserialize($result[1]['logs'])];		
	}
	return null;
}

/**
 * 设置并解除占用
 * 
 * @param int $db
 * @param string $key
 * @param array $data
 * @param array $logs
 */
function unengageKey($db,$key,$data=null,$logs=null){
	$pipe=my_redis()->multi(2)->select($db);
	$pipe->hDel($key,'engaged');
	if($data!==null){
		$pipe->hSet($key,'data',igbinary_serialize($data));
	}
	if($logs!==null){
		$pipe->hSet($key,'logs',igbinary_serialize($logs));
	}
	$pipe->exec();
	return true;
}

?>
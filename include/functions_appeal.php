<?php

/**
 * @var array $apppeal
 * string ['id']
 * string ['account']
 * string ['message']
 * string ['extra']
 * array ['logs']
 */

/**
 * 创建一个申诉
 * 
 * @param string $id
 * @param string $message
 * @return message()
 */
function createAppeal($id,$message){
	$lastappeal=engageKey(5,'lastappeal')[0];
	if(isset($lastappeal[$id]) and time()-$lastappeal[$id]<300){
		return message(REQUEST_FORBIDDEN,"五分钟内不能重复申诉");
		unengageKey(5,'lastappeal');
	}
	$lastappeal[$id]=time();
	unengageKey(5,'lastappeal',$lastappeal);

	while(1){
		$appeal['id']=mt_rand(100000,999999);
		if(!keyExists(5,$appeal['id'])){
			break;
		}
	}
	$appeal['account']=$id;
	$appeal['message']=$message;
	$appeal['status']="not dealing";
	$logs[]=mylog("Appeal created by {$id}. Appeal id {$appeal['id']}");
	
	unengageKey(5,$appeal['id'],$appeal,$logs);
	return message(REQUEST_SUCCESS,"申诉成功，请注意保存申诉id",$appeal['id']);
}

/**
 * 获取一个申诉
 * 
 * @param string $id
 * @param string $appealid
 * @return message()
 */
function queryAppeal($id,$appealid){
	$appeal=getKeyAll(5,$appealid);
	if($appeal==false){
		return message(REQUEST_FAILED,"没有这个申诉");
	}
	if($appeal[0]['account']!==$id){
		return message(REQUEST_FORBIDDEN,"申诉人id错误");
	}
	return message(REQUEST_SUCCESS,"请求成功",["info"=>$appeal[0],"logs"=>$appeal[1]]);
	
}


?>